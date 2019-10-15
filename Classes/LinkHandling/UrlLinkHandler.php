<?php
declare(strict_types=1);

namespace GeorgRinger\Uri2Link\LinkHandling;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UrlLinkHandler extends \TYPO3\CMS\Recordlist\LinkHandler\UrlLinkHandler
{
    public function render(ServerRequestInterface $request)
    {
        GeneralUtility::makeInstance(PageRenderer::class)->loadRequireJsModule('TYPO3/CMS/Uri2link/BetterUrlLinkHandler');
        $this->view->assign('url', !empty($this->linkParts) ? $this->linkParts['url'] : '');
        return $this->view->render('Url');
    }
}
