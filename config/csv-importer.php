<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Define the route prefix and middlewares that will be applied to the
    | CSV importer UI and api endpoints.
    |
    */
    'route_prefix' => 'csv-importer',
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Tables
    |--------------------------------------------------------------------------
    |
    | Specify database tables that are permitted to receive imported CSV data.
    | If empty, all tables in the database schema will be fetchable, excluding
    | sensitive Laravel internal tables.
    |
    */
    'allowed_tables' => [],

    /*
    |--------------------------------------------------------------------------
    | Excluded Tables
    |--------------------------------------------------------------------------
    |
    | If 'allowed_tables' is empty, these tables will be excluded from the
    | available destination list.
    |
    */
    'excluded_tables' => [
        'migrations',
        'password_resets',
        'password_reset_tokens',
        'failed_jobs',
        'personal_access_tokens',
        'sessions',
        'jobs',
        'job_batches',
        'cache',
        'cache_locks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Column Validation Patterns
    |--------------------------------------------------------------------------
    |
    | Define frontend/backend regular expression validations based on typical
    | database column names. Used to automatically flag invalid cells in the UI.
    |
    */
    'column_validation' => [
        'email' => '/^[^\s@]+@[^\s@]+\.[^\s@]+$/',
        'phone' => '/^\+?[0-9\s\-()]{7,20}$/',
        'age' => '/^[0-9]+$/',
        'postal_code' => '/^[0-9a-zA-Z\s\-]{3,10}$/',
    ],
];
