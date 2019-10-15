<?php

return [
    'uri2link_check' => [
        'path' => '/uri2link/check',
        'target' => \GeorgRinger\Uri2Link\Controller\AjaxController::class . '::checkAction'
    ],
];
