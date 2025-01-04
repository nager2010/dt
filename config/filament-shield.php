<?php

return [
    'shield_resource' => [
        'should_register_navigation' => true,
        'slug' => 'shield/roles',
        'navigation_sort' => -1,
        'navigation_badge' => true,
        'navigation_group' => 'الإعدادات',
        'is_globally_searchable' => false,
        'show_model_path' => true,
    ],

    'auth_provider_model' => [
        'fqcn' => 'App\\Models\\User',
    ],

    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
        'define_via_gate' => false,
        'intercept_gate' => 'before',
        'user_model' => [
            'multiple' => false,
        ],
    ],

    'permission_prefixes' => [
        'resource' => [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'print',
        ],

        'page' => 'page',
        'widget' => 'widget',
    ],

    'entities' => [
        'pages' => true,
        'widgets' => true,
        'resources' => true,
        'custom_permissions' => [
            'license_requests' => [
                'title' => 'طلبات التراخيص',
                'permissions' => [
                    'approve_license_request' => 'الموافقة على الطلب',
                    'reject_license_request' => 'رفض الطلب',
                ],
            ],
            'issuing_licenses' => [
                'title' => 'التراخيص المصدرة',
                'permissions' => [
                    'print_license' => 'طباعة الترخيص',
                    'scan_licenses' => 'مسح QR للتراخيص',
                ],
            ],
            'settings' => [
                'title' => 'إعدادات النظام',
                'permissions' => [
                    'manage_settings' => 'إدارة الإعدادات',
                    'manage_roles' => 'إدارة الأدوار والصلاحيات',
                ],
            ],
        ],
    ],

    'generator' => [
        'option' => 'policies_and_permissions',
    ],

    'exclude' => [
        'enabled' => true,

        'pages' => [
            'Dashboard',
        ],

        'widgets' => [
            'AccountWidget',
            'FilamentInfoWidget',
        ],

        'resources' => [],
    ],

    'register_role_policy' => [
        'enabled' => true,
    ],
];
