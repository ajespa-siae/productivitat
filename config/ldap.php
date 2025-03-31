<?php

return [
    'default' => env('LDAP_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'hosts' => [env('LDAP_HOST', '127.0.0.1')],
            'username' => env('LDAP_USERNAME', 'cn=user,dc=local,dc=com'),
            'password' => env('LDAP_PASSWORD', 'secret'),
            'port' => env('LDAP_PORT', 389),
            'base_dn' => env('LDAP_BASE_DN', 'dc=local,dc=com'),
            'timeout' => env('LDAP_TIMEOUT', 5),
            'use_ssl' => env('LDAP_SSL', false),
            'use_tls' => env('LDAP_TLS', false),
            'options' => [
                LDAP_OPT_X_TLS_REQUIRE_CERT => LDAP_OPT_X_TLS_NEVER,
                LDAP_OPT_REFERRALS => 0,
            ],
        ],
    ],
    'logging' => [
        'enabled' => env('LDAP_LOGGING', true),
        'events' => [
            'enabled' => true,
            'dispatcher' => null,
        ],
    ],
    'cache' => [
        'enabled' => env('LDAP_CACHE', true),
        'driver' => env('CACHE_DRIVER', 'file'),
    ],
];
