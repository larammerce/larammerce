<?php

// config/tntsearch.php

return [
    'storage'  => storage_path('tntsearch'), // Storage path for the generated index files

    'fuzziness' => env('TNTSEARCH_FUZZINESS', true), // Enable or disable fuzzy search
    'fuzzy' => [
        'prefix_length' => 2, // Length of the prefix to check for fuzzy matches
        'max_expansions' => 50, // Maximum number of variants to consider when fuzzy searching
        'distance' => 2 // Maximum Levenshtein distance for fuzzy matches
    ],

    'asYouType' => true, // Enable or disable "search as you type" feature

    'searchBoolean' => env('TNTSEARCH_BOOLEAN', false), // Perform a boolean search when enabled

    'tokenizer'  => [
        'tokenizer' => env('TNTSEARCH_TOKENIZER', 'standard'), // Default tokenizer
        'stopwords' => [], // An array of stopwords (common words that are ignored during searching)
    ],

    'scout' => [
        'queue' => env('SCOUT_QUEUE', false), // Enable or disable indexing asynchronously (via Laravel Scout)
        'queueConnection' => env('SCOUT_QUEUE_CONNECTION'), // Name of the queue connection to use
        'queueAfterCommit' => env('SCOUT_QUEUE_AFTER_COMMIT', false), // Whether to queue a job after the database transaction is committed
    ],

    'zero_terms_query' => 'all', // How to handle a query with zero terms ('all' or 'none')

    // Language-specific stemming (word simplification)
    'stemmer' => \Wamania\Snowball\English::class,

    'stopwords' => storage_path('app/tntsearch/stopwords'), // Stopwords file path, can be a json file like en.json

    'dictionary' => storage_path('tntsearch/dict-fa'), // Language dictionary file path
];
