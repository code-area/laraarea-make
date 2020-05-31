<?php

return [
    'by_database' => '_db',
    'root_path' => 'app',
    'root_namespace' => app()->getNamespace(),
    'suffix' => [
        'trait' => 'Traits',
        'interface' => 'Interface',
    ],
    'ucfirst' => true,
    'ignore_tables' => [
        'migrations',
        'password_resets',
        'failed_jobs',
        'jobs',
        'oauth_access_tokens',
        'oauth_auth_codes',
        'oauth_clients',
        'oauth_personal_access_clients',
        'oauth_refresh_tokens',
        'telescope_entries',
        'telescope_entries_tags',
        'telescope_monitoring',
    ],
    'not_fillable_columns' => [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ],
    'not_fillable_table_columns' => [
        'users' => [
            'remember_token'
        ],
    ],
];