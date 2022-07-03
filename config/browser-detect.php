<?php
return [
    'cache' => [
        /**
         * Interval in seconds, as how long a result should be cached.
         */
        'interval' => 10080,
        /**
         * Cache prefix, the user agent string will be hashed and appended at the end.
         */
        'prefix' => 'bd4_'
    ],
    'security' => [
        /**
         * Byte length where the header is cut off, if some attacker sends a long header
         * then the library will make a cut this byte point.
         */
        'max-header-length' => 2048,
    ],
    'min_major_version' => [
        /**
         * Identified by browser name, followed by major version 
         */
        'Firefox' => 90,
        'Seamonkey' => 2,
        'Chrome' => 56,
        'Chromium' => 56,
        'Safari' => 12,
        'Opera' => 3,
        'Internet Explorer' => 10
    ]
];
