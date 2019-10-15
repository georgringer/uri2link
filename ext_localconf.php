<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['uri2link'] =
    \GeorgRinger\Uri2Link\Hooks\DataHandlerHook::class;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    TCEMAIN.linkHandler {
        url {
            handler = GeorgRinger\Uri2Link\LinkHandling\UrlLinkHandler
        }

    }
');
