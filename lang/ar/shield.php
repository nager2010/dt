<?php

return [
    'resource' => [
        'label' => 'الأدوار',
        'plural_label' => 'الأدوار',
        'navigation_label' => 'الأدوار والصلاحيات',
        'navigation_group' => 'الإعدادات',
    ],

    'forms' => [
        'name' => 'الاسم',
        'guard_name' => 'نوع الحماية',
        'select_all' => 'تحديد الكل',
        'select_all_description' => 'تفعيل كل الصلاحيات لهذا الدور',
        'super_admin' => [
            'label' => 'مدير النظام',
            'description' => 'المدراء لديهم وصول كامل للنظام'
        ],
        'roles' => [
            'label' => 'اسم الدور',
            'description' => 'المستخدمون في هذا الدور سيحصلون على الصلاحيات المحددة'
        ],
    ],

    'columns' => [
        'name' => 'الاسم',
        'guard_name' => 'نوع الحماية',
        'permissions_count' => 'عدد الصلاحيات',
    ],

    'permissions' => [
        'view' => 'عرض',
        'view_any' => 'عرض الكل',
        'create' => 'إنشاء',
        'update' => 'تعديل',
        'delete' => 'حذف',
        'delete_any' => 'حذف الكل',
        'force_delete' => 'حذف نهائي',
        'force_delete_any' => 'حذف نهائي للكل',
        'restore' => 'استعادة',
        'restore_any' => 'استعادة الكل',
        'replicate' => 'نسخ',
        'reorder' => 'إعادة ترتيب',
        'import' => 'استيراد',
        'export' => 'تصدير',
    ],

    'messages' => [
        'created' => 'تم إنشاء الدور بنجاح',
        'updated' => 'تم تحديث الدور بنجاح',
        'deleted' => 'تم حذف الدور بنجاح',
    ],
];
