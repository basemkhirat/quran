<?php

$types = [
    "nested" => ["type" => "nested"],
    "arabic_text" => [
        "type" => "text",
        "analyzer" => "arabic",
        "fields" => [
            'keyword' => [
                "type" => "keyword",
                "ignore_above" => 256
            ]
        ]
    ],
    "english_text" => [
        "type" => "text",
        "analyzer" => "english",
        "fields" => [
            'keyword' => [
                "type" => "keyword",
                "ignore_above" => 256
            ]
        ]
    ],
    "keyword" => ["type" => "keyword"],
    "date" => [
        'type' => 'date',
        "format" => "yyyy-MM-dd HH:mm:ss"
    ],
    "text_keyword" => [
        "type" => "text",
        "fields" => [
            "keyword" => [
                "type" => "keyword"
            ]
        ]
    ]
];

$analysis = [
    "arabic" => [
        "filter" => [
            "arabic_stop" => [
                "type" => "stop",
                "stopwords" => "_arabic_"
            ],
            "arabic_keywords" => [
                "type" => "keyword_marker",
                "keywords" => ["مثال"]
            ],
            "arabic_stemmer" => [
                "type" => "stemmer",
                "language" => "arabic"
            ]
        ],

        "analyzer" => [
            "rebuilt_arabic" => [
                "tokenizer" => "standard",
                "filter" => [
                    "lowercase",
                    "decimal_digit",
                    "arabic_stop",
                    "arabic_normalization",
                    "arabic_keywords",
                    "arabic_stemmer"
                ]
            ]
        ]

    ]
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Elasticsearch Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the Elasticsearch connections below you wish
    | to use as your default connection for all work. Of course.
    |
    */

    'default' => env('ELASTIC_CONNECTION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the Elasticsearch connections setup for your application.
    | Of course, examples of configuring each Elasticsearch platform.
    |
    */

    'connections' => [

        'default' => [

            'servers' => [

                [
                    'host' => env('ELASTIC_HOST', '127.0.0.1'),
                    'port' => env('ELASTIC_PORT', 9200),
                    'user' => env('ELASTIC_USER', ''),
                    'pass' => env('ELASTIC_PASS', ''),
                    'scheme' => env('ELASTIC_SCHEME', 'http'),
                ]

            ],

            'index' => env('ELASTIC_INDEX', 'ayat'),

            // Elasticsearch handlers
            // 'handler' => new MyCustomHandler(),

            'logging' => [
                'enabled' => env('ELASTIC_LOGGING_ENABLED', false),
                'level' => env('ELASTIC_LOGGING_LEVEL', 'all'),
                'location' => env('ELASTIC_LOGGING_LOCATION', base_path('storage/logs/elasticsearch.log'))
            ],
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Indices
    |--------------------------------------------------------------------------
    |
    | Here you can define your indices, with separate settings and mappings.
    | Edit settings and mappings and run 'php artisan es:index:update' to update
    | indices on elasticsearch server.
    |
    | 'my_index' is just for test. Replace it with a real index name.
    |
    */

    'indices' => [

        'ayat_1' => [

            'aliases' => [
                'ayat'
            ],

            'settings' => [
                'number_of_shards' => 1,
                'number_of_replicas' => 0,
                "index.mapping.ignore_malformed" => true,
                "analysis" => [
                    "analyzer" => [
                        "default" => [
                            "type" => "arabic"
                        ]
                    ]
                ]
            ],

            'mappings' => [
                'quran' => [
                    'properties' => [
                        'text' => $types["arabic_text"]
                    ]
                ]
            ]
        ]
    ]


];
