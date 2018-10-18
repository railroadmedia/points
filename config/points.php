<?php

return [
    // database
    'database_connection_name' => 'mysql',

    // host does the db migrations, clients do not
    'data_mode' => 'host', // 'host' or 'client'

    // brand
    'brand' => 'brand',

    // tables
    'table_prefix' => 'points_',
    'tables' => [
        'user_points' => 'user_points',
    ],
];