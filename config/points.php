<?php

return [
    // database
    'database_connection_name' => 'mysql',

    // cache
    // ttl value in minutes
    'cache_duration' => 60 * 24,
    'cache_prefix' => 'user_points',
    'cache_driver' => env('CACHE_DRIVER', 'redis'),

    // host does the db migrations, clients do not
    'data_mode' => 'host', // 'host' or 'client'

    // brand
    'brand' => 'brand',

    // tables
    'tables' => [
        'user_points' => 'points_user_points',
    ],

    // mapping points to tiers
    'tier_default' => 'Beginner',
    'tier_map' => [
        ['name' => 'Level 1', 'start' => 0],
        ['name' => 'Level 2', 'start' => 1000],
        ['name' => 'Level 3', 'start' => 2000],
        ['name' => 'Level 4', 'start' => 3000],
        ['name' => 'Level 5', 'start' => 4000],
        ['name' => 'Level 6', 'start' => 5000],
        ['name' => 'Level 7', 'start' => 6000],
        ['name' => 'Level 8', 'start' => 7000],
        ['name' => 'Level 9', 'start' => 8000],
        ['name' => 'Level 10', 'start' => 9000],
    ],
];