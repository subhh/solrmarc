# VuFind SolrMarc

SolrMarc is copyright (c) 2018-2023 by Staats- und Universitätsbibliothek Hamburg and released under the terms of the
GNU General Public License v2.

## Usage

Add the SolrMarc view helper and the partials to the module configuration.

```php
$config = [
    'view_helpers' => [
        'aliases' => [
            'solrDetails' => SUBHH\VuFind\SolrMarc\ViewHelper::class,
        ],
        'invokables' => [
            SUBHH\VuFind\SolrMarc\ViewHelper::class => SUBHH\VuFind\SolrMarc\ViewHelper::class,
        ],
        ...
    ],
    'view_manager' => [
        'template_path_stack' => [
            SUBHH\VuFind\SolrMarc\ResourceLocator::getResourceDirectory(),
        ],
    ]
    ...
];
```

## Authors

Hajo Seng &lt;hajo.seng@sub.uni-hamburg.de&gt;

David Maus &lt;david.maus@sub.uni-hamburg.de&gt;
