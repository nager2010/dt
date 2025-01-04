<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'الاسم',
    'column.guard_name' => 'نوع الحماية',
    'column.roles' => 'الأدوار',
    'column.permissions' => 'الصلاحيات',
    'column.updated_at' => 'تاريخ التحديث',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'الاسم',
    'field.guard_name' => 'نوع الحماية',
    'field.permissions' => 'الصلاحيات',
    'field.select_all.name' => 'تحديد الكل',
    'field.select_all.message' => 'تفعيل كافة الصلاحيات المتاحة حالياً <span class="text-primary font-medium">لهذا الدور</span>',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'إعدادات النظام',
    'nav.label' => 'الأدوار والصلاحيات',
    'resource.label.role' => 'الدور',
    'resource.label.roles' => 'الأدوار',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */
    
    'section' => 'الوحدات',
    'resources' => 'الموارد',
    'widgets' => 'الأدوات',
    'pages' => 'الصفحات',
    'custom' => 'صلاحيات إضافية',

    /**
     * Role Setting Page
     */
    'super_admin.toggle_label' => 'مدير النظام',
    'super_admin.text' => 'المدير لديه الوصول الكامل إلى كافة <span class="text-primary font-medium">الموارد والصلاحيات</span>',

    'direct_permissions.toggle_label' => 'صلاحيات مباشرة',
    'direct_permissions.text' => 'يمكنك إضافة صلاحيات <span class="text-primary font-medium">مخصصة</span> للمستخدم',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'ليس لديك صلاحية الوصول',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'عرض',
        'view_any' => 'عرض الكل',
        'create' => 'إضافة',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'delete_any' => 'حذف متعدد',
        'print' => 'طباعة',
    ],

    'entities' => [
        'license_request' => [
            'label' => 'طلبات التراخيص',
            'approve' => 'موافقة على الطلب',
            'reject' => 'رفض الطلب',
        ],
        'issuing_license' => [
            'label' => 'التراخيص المصدرة',
            'print' => 'طباعة الترخيص',
            'scan' => 'مسح QR للتراخيص',
        ],
        'expired_license' => [
            'label' => 'التراخيص المنتهية',
        ],
        'region' => [
            'label' => 'المناطق',
        ],
        'municipality' => [
            'label' => 'البلديات',
        ],
        'user' => [
            'label' => 'المستخدمين',
        ],
        'settings' => [
            'label' => 'الإعدادات',
            'manage' => 'إدارة الإعدادات',
            'roles' => 'إدارة الأدوار والصلاحيات',
        ],
    ],
];