<?php

return [
    // database
    'database_connection_name' => 'mysql',

    // host does the db migrations, clients do not
    'data_mode' => 'host', // 'host' or 'client'

    // brand
    'brand' => 'brand',

    // tables
    'tables' => [
        'user_points' => 'points_user_points',
    ],
];