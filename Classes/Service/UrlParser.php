<?php
declare(strict_types=1);

namespace GeorgRinger\Uri2Link\Service;

use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Error\Http\ServiceUnavailableException;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\LinkHandling\PageLinkHandler;
use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\RouteNotFoundException;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Service\TypoLinkCodecService;
use TYPO3\CMS\Frontend\Typolink\PageLinkBuilder;
use TYPO3\CMS\Frontend\Typolink\UnableToLinkException;

class UrlParser implements SingletonInterface
{
    /** @var PageLinkHandler */
    protected $pageLinkHandler;

    /** @var TypoLinkCodecService */
    protected $typoLinkCodecService;

    public function __construct()
    {
        $this->pageLinkHandler = GeneralUtility::makeInstance(PageLinkHandler::class);
        $this->typoLinkCodecService = GeneralUtility::makeInstance(TypoLinkCodecService::class);
    }

    /**
     * @param string $uri
     * @return string
     * @throws UnableToLinkException
     * @throws RouteNotFoundException
     */
    public function parse(string $uri): string
    {
        $uriParts = $this->typoLinkCodecService->decode($uri);
        $uri = $uriParts['url'];
        $request = new ServerRequest($uri, 'GET');

        $siteMatcher = GeneralUtility::makeInstance(SiteMatcher::class);
        $routeResult = $siteMatcher->matchRequest($request);

        $site = $routeResult->getSite();
        if (!$site instanceof Site) {
            throw new \RuntimeException(sprintf('No site found for url: %s', $uri), 1568481276);
        }
        $pageArguments = $site->getRouter()->matchRequest($request, $routeResult);
        $parameters = $this->buildLinkParameters($routeResult, $pageArguments);

        if ($this->validateUrl($uri, $parameters, $site)) {
            $uriParts['url'] = $this->pageLinkHandler->asString($parameters);;
            return $this->typoLinkCodecService->encode($uriParts);
        } else {
//                print_r($parameters);
//                die;
        }

        return $uri;
    }

    protected function buildLinkParameters(SiteRouteResult $routeResult, PageArguments $pageArguments): array
    {
        $parameters = [
            'pageuid' => $pageArguments->getPageId(),
            'pagetype' => $pageArguments->getPageType() !== '0' ? $pageArguments->getPageType() : ''
        ];
        $extraParams = [];
        if (!empty($pageArguments->getStaticArguments())) {
            $extraParams = $extraParams + $pageArguments->getStaticArguments();
        }
        if ($routeResult->getLanguage() && $routeResult->getLanguage()->getLanguageId() > 0) {
            $extraParams['L'] = $routeResult->getLanguage()->getLanguageId();
        }

        if (!empty($extraParams)) {
            $parameters['parameters'] = http_build_query($extraParams, '', '&', PHP_QUERY_RFC3986);
        }

        return $parameters;
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param Site $site
     * @return bool
     * @throws UnableToLinkException
     */
    protected function validateUrl(string $uri, array $parameters, Site $site): bool
    {

        $queryParams = [];

        $controller = $this->bootFrontendController($site, $queryParams);
        $pageLinkBuilder = GeneralUtility::makeInstance(PageLinkBuilder::class, $controller->cObj, $controller);
        $newUrlResult = $pageLinkBuilder->build($parameters, 'fake', '', []);
        if ($newUrlResult[0] === $uri) {
            return true;
        }

        return false;
    }

    /**
     * Finishing booting up TSFE, after that the following properties are available.
     *
     * Instantiating is done by the middleware stack (see Configuration/RequestMiddlewares.php)
     *
     * - TSFE->fe_user
     * - TSFE->sys_page
     * - TSFE->tmpl
     * - TSFE->config
     * - TSFE->cObj
     *
     * So a link to a page can be generated.
     *
     * @param Site $site
     * @param array $queryParams
     * @return TypoScriptFrontendController
     * @throws ServiceUnavailableException
     * @throws ImmediateResponseException
     */
    protected function bootFrontendController(Site $site, array $queryParams): TypoScriptFrontendController
    {
        $pageId = $site ? $site->getRootPageId() : 0;
        $controller = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            GeneralUtility::makeInstance(Context::class),
            $site,
            $site->getDefaultLanguage(),
            new PageArguments((int)$pageId, '0', [])
        );
        $controller->fe_user = GeneralUtility::makeInstance(FrontendUserAuthentication::class);;
        $controller->fetch_the_id();
        $controller->calculateLinkVars($queryParams);
        $controller->getConfigArray();
        $controller->settingLanguage();
        $controller->newCObj();
        return $controller;
    }
}
