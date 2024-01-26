<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Convert external urls to TYPO3 links',
    'description' => 'Transforms url like domain.tld/services to TYPO3 links (t3://) if those are actually internal links',
    'category' => 'be',
    'author' => 'Georg Ringer',
    'author_email' => '',
    'state' => 'beta',
    'clearCacheOnLoad' => true,
    'version' => '0.2.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.33-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
