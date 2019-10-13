<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Convert external urls to TYPO3 links',
    'description' => 'Transforms url like domain.tld/services to TYPO3 links (t3://) if those are actually internal links',
    'category' => 'be',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '9.5.0-10.2.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
