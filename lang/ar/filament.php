<?php

return [
    // Common Actions
    'actions' => [
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'restore' => 'استعادة',
        'force_delete' => 'حذف نهائي',
        'back' => 'رجوع',
        'view' => 'عرض التفاصيل',
        'accept' => 'قبول الطلب',
        'reject' => 'رفض الطلب',
        'on_the_way' => 'قيد التوصيل',
        'delivered' => 'تم التسليم',
    ],

    // Error / access denied messages
    'errors' => [
        'cannot_edit_super_admin' => 'لا يمكن تعديل حساب المسؤول الأعلى.',
        'cannot_edit_super_admin_role' => 'لا يمكن تعديل دور المسؤول الأعلى.',
    ],

    // Tabs
    'tabs' => [
        'info' => 'المعلومات',
        'marketplace' => 'السوق',
        'admins' => 'المسؤولون',
        'shops' => 'المحلات',
        'news' => 'الأخبار',
        'drivers' => 'السائقون',
    ],

    // Navigation & Branding
    'navigation' => [
        'brand' => 'لوحة تحكم تانِس',
        'dashboard' => 'لوحة التحكم',
        'management' => 'الإدارة',
        'shop_management' => 'إدارة المحلات',
        'directory_management' => 'إدارة الدليل',
        'news_management' => 'إدارة الأخبار',
        'ads_management' => 'إدارة الإعلانات',
        'content_pages_management' => 'إدارة صفحات المحتوى',
        'transport_management' => 'إدارة وسائل النقل',
        'transportation_management' => 'إدارة وسائل النقل',
        'marketplace_management' => 'إدارة السوق',
        'users_management' => 'إدارة المستخدمين',
        'support' => 'الدعم',
        'operations' => 'العمليات',
        'content' => 'المحتوى',
        'system' => 'النظام',
        'access_control' => 'التحكم بالوصول',
        'accounts_and_permissions' => 'الحسابات والصلاحيات',
        'content_and_support' => 'المحتوى والدعم',
        'system_services' => 'خدمات النظام',
        'notifications_management' => 'إدارة الإشعارات',
        'posts_management' => 'إدارة المنشورات',
        'jobs_management' => 'إدارة الوظائف',
        'events_management' => 'إدارة الأحداث',
        'pending_listings' => 'قيد المراجعة',
        'all_listings' => 'الكل',
    ],

    // Resource Names (Plurals for navigation)
    'resources' => [
        'admin' => 'المسؤولون',
        'role' => 'الأدوار',
        'permission' => 'الصلاحيات',
        'shop_category' => 'فئات المحلات',
        'shop_category_singular' => 'فئة محل',
        'shop' => 'المحلات',
        'shop_singular' => 'محل',
        'shop_section' => 'أقسام المحلات',
        'shop_section_singular' => 'قسم محل',
        'product' => 'المنتجات',
        'product_singular' => 'منتج',
        'directory_main_category' => 'أقسام الدليل',
        'directory_main_category_singular' => 'قسم الدليل',
        'directory_sub_category' => 'الأقسام الفرعية للدليل',
        'directory_sub_category_singular' => 'قسم الدليل الفرعي',
        'directory_listing' => 'قائمة الدليل',
        'directory_listing_singular' => 'قائمة الدليل',
        'news_category' => 'أقسام الأخبار',
        'news_category_singular' => 'قسم أخبار',
        'news' => 'قوائم الأخبار',
        'news_singular' => 'قائمة خبر',
        'transport_area' => 'مناطق وسائل النقل',
        'transport_area_singular' => 'منطقة وسائل النقل',
        'transport_driver' => 'سائقين وسائل النقل',
        'transport_driver_singular' => 'سائق وسائل النقل',
        'transportation_area' => 'مناطق وسائل النقل',
        'transportation_area_singular' => 'منطقة وسائل النقل',
        'transportation_driver' => 'سائقين وسائل النقل',
        'transportation_driver_singular' => 'سائق وسائل النقل',
        'transportation_trip' => 'رحلات وسائل النقل',
        'transportation_trip_singular' => 'رحلة وسائل النقل',
        'marketplace_category' => 'فئات السوق',
        'marketplace_category_singular' => 'فئة سوق',
        'marketplace_listing' => 'قوائم السوق',
        'marketplace_listing_singular' => 'قائمة سوق',
        'ad' => 'الإعلانات',
        'ad_singular' => 'إعلان',
        'content_page' => 'صفحات المحتوى',
        'content_page_singular' => 'صفحة محتوى',
        'user' => 'المستخدمون',
        'user_singular' => 'مستخدم',
        'support_tickets' => 'تذاكر الدعم',
        'support_ticket_singular' => 'تذكرة دعم',
        'job_listing' => 'الوظائف',
        'job_listing_singular' => 'وظيفة',
        'event' => 'الأحداث',
        'event_singular' => 'حدث',
        'shop_orders' => 'طلبات المحلات',
        'shop_order_singular' => 'طلب محل',
        'devices' => 'الأجهزة',
        'device_singular' => 'جهاز',
        'notifications' => 'الإشعارات',
        'notification_singular' => 'إشعار',
        'notification_broadcasts' => 'إشعارات البث',
        'notification_broadcast_singular' => 'إشعار بث',
    ],

    // Common Fields
    'fields' => [
        'id' => 'المعرف',
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'phone' => 'الهاتف',
        'created_at' => 'تاريخ الإنشاء',
        'updated_at' => 'تاريخ التحديث',
        'created_by' => 'أنشئ بواسطة',
        'updated_by' => 'حدّث بواسطة',
        'is_active' => 'نشط',
        'roles' => 'الأدوار',
        'permissions' => 'الصلاحيات',
        'description' => 'الوصف',
        'title' => 'العنوان',
        'slug' => 'المعرف',
        'address' => 'العنوان',
        'main_category' => 'القسم الرئيسي',
        'shop' => 'المحل',
        'shop_section' => 'القسم',
        'sort_order' => 'ترتيب العرض',
        'price' => 'السعر',
        'currency_egp' => 'ج.م',
        'image_url' => 'رابط الصورة',
        'image' => 'الصورة',
        'status' => 'الحالة',
        'total' => 'الإجمالي',
        'user' => 'المستخدم',
        'notes' => 'ملاحظات',
        'product' => 'المنتج',
        'quantity' => 'الكمية',
        'order_number' => 'رقم الطلب',
        'action' => 'الإجراء',
        'performed_by' => 'تم بواسطة',
        'device_id' => 'معرف الجهاز',
        'platform' => 'المنصة',
        'manufacturer' => 'الشركة المصنعة',
        'model' => 'الموديل',
        'os_version' => 'إصدار النظام',
        'app_version' => 'إصدار التطبيق',
        'last_used_at' => 'آخر استخدام',
        'global' => 'عام',
        'birth_date' => 'تاريخ الميلاد',
        'gender' => 'النوع',
        'gender_male' => 'ذكر',
        'gender_female' => 'أنثى',
    ],

    'notifications' => [
        'send_notification' => 'إرسال إشعار',
        'sent' => 'تم الإرسال',
        'created_count' => 'تم إنشاء :count إشعار',
        'recipient_type' => 'نوع المستلم',
        'recipients' => 'المستلمين',
        'recipient' => 'المستلم',
        'recipient_id' => 'معرف المستلم',
        'body' => 'المحتوى',
        'target_type' => 'نوع الهدف',
        'target_id' => 'الهدف',
        'data_json' => 'البيانات (JSON)',
        'sent_at' => 'تاريخ الإرسال',
        'read_at' => 'تاريخ القراءة',
        'select_target_type_first' => 'اختر نوع الهدف أولاً...',
        'select_recipient_type_first' => 'اختر نوع المستلم أولاً...',
        'all_users' => 'كل المستخدمين',
        'topic' => 'الموضوع',
    ],

    // Content Pages
    'content_pages' => [
        'id' => 'المعرف',
        'nav' => [
            'content_pages' => 'صفحات المحتوى',
            'section_info' => 'معلومات صفحة المحتوى',
        ],
        'section_arabic' => 'العربية',
        'section_english' => 'English',
        'title_ar' => 'العنوان (عربي)',
        'content_ar' => 'المحتوى (عربي)',
        'title_en' => 'العنوان (إنجليزي)',
        'content_en' => 'المحتوى (إنجليزي)',
        'title' => 'العنوان',
    ],

    // Audit (created by / updated by)
    'audit' => [
        'section_title' => 'التدقيق',
    ],

    // Empty states
    'empty' => [
        'no_devices' => 'لا توجد أجهزة',
        'no_notification_broadcasts' => 'لا توجد إشعارات بث',
    ],

    // Admin
    'admin' => [
        'admin_information' => 'معلومات المسؤول',
        'type_admin' => 'مسؤول',
        'type_super_admin' => 'مسؤول أعلى',
    ],

    // Role
    'role' => [
        'role_information' => 'معلومات الدور',
        'no_admins' => 'لا يوجد مسؤولون لديهم هذا الدور',
        'no_permissions' => 'لا توجد صلاحيات مخصصة',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
    ],

    // Permission
    'permission' => [
        'permission_information' => 'معلومات الصلاحية',
        'no_roles' => 'لا توجد أدوار لديها هذه الصلاحية',
        'key' => 'المفتاح',
        'name_ar' => 'الاسم (عربي)',
        'name_en' => 'الاسم (إنجليزي)',
    ],

    // Shop Category
    'shop_category' => [
        'category_information' => 'معلومات الفئة',
        'shops_count' => 'المحلات',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
        'add_shop' => 'إضافة محل',
    ],

    // Shop
    'shop' => [
        'shop_information' => 'معلومات المحل',
        'sections_tab' => 'الأقسام',
        'products_tab' => 'المنتجات',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
    ],

    'shop_order' => [
        'status_pending' => 'قيد المراجعة',
        'status_accepted' => 'تم القبول',
        'status_rejected' => 'مرفوض',
        'status_on_the_way' => 'قيد التوصيل',
        'status_delivered' => 'تم التسليم',
        'status_cancelled' => 'ملغي من المستخدم',
        'products_section' => 'منتجات الطلب',
        'logs_section' => 'سجلات الطلب',
        'log_singular' => 'سجل',
        'logs' => 'السجلات',
        'from_status' => 'من حالة',
        'to_status' => 'إلى حالة',
        'log_action_created' => 'تم إنشاء الطلب',
        'log_action_status_changed' => 'تم تغيير حالة الطلب',
    ],

    // Shop Section
    'shop_section' => [
        'section_information' => 'معلومات القسم',
        'products_count' => 'المنتجات',
        'new_section' => 'قسم جديد',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
    ],

    // Product
    'product' => [
        'product_information' => 'معلومات المنتج',
        'new_product' => 'منتج جديد',
    ],

    // User
    'user' => [
        'password' => 'كلمة المرور',
        'user_information' => 'معلومات المستخدم',
    ],

    'post' => [
        'nav' => 'المنشورات',
        'singular' => 'منشور',
        'section' => 'المنشور',
        'body' => 'المحتوى',
        'author' => 'الكاتب',
        'likes_count' => 'الإعجابات',
        'comments_count' => 'التعليقات',
        'likes_tab' => 'الإعجابات',
        'comments_tab' => 'التعليقات',
        'like_singular' => 'إعجاب',
        'comment_singular' => 'تعليق',
        'liked_by' => 'المستخدم',
        'commented_by' => 'المستخدم',
        'comment_text' => 'نص التعليق',
        'linked_post' => 'منشور مرتبط',
    ],

    'post_comment_preset' => [
        'nav' => 'التعليقات',
        'singular' => 'تعليق جاهز',
        'section' => 'التعليق الجاهز',
        'text' => 'النص',
    ],

    // Support Tickets
    'support_ticket' => [
        'nav' => [
            'support_tickets' => 'تذاكر الدعم',
        ],
        'ticket_singular' => 'تذكرة دعم',
        'ticket_number' => 'رقم التذكرة',
        'ticket_information' => 'معلومات التذكرة',
        'owner' => 'صاحب التذكرة',
        'phone' => 'الهاتف',
        'email' => 'البريد الإلكتروني',
        'phone_email' => 'الهاتف / البريد',
        'owner_type' => 'التذكرة لـ',
        'link_to_user' => 'ربط بمستخدم',
        'visitor' => 'زائر',
        'visitor_name' => 'اسم الزائر',
        'visitor_phone' => 'هاتف الزائر',
        'visitor_email' => 'بريد الزائر',
        'user' => 'المستخدم',
        'message' => 'الرسالة',
        'status' => 'الحالة',
        'priority' => 'الأولوية',
        'status_open' => 'مفتوحة',
        'status_in_progress' => 'قيد المعالجة',
        'status_resolved' => 'تم الحل',
        'status_closed' => 'مغلقة',
        'priority_low' => 'منخفضة',
        'priority_normal' => 'عادية',
        'priority_high' => 'عالية',
        'attachments' => 'المرفقات',
        'attachments_documents' => 'مستندات',
        'attachment_type_image' => 'صورة',
        'attachment_type_document' => 'مستند',
        'attachment_click_to_view' => 'اضغط لعرض الملف',
        'view_attachments' => 'عرض المرفقات',
        'view_message' => 'عرض الرسالة',
        'close' => 'إغلاق',
        'change_status' => 'تغيير الحالة',
        'change_priority' => 'تغيير الأولوية',
        'logs_tab' => 'السجلات',
        'log_singular' => 'سجل',
        'add_log' => 'إضافة سجل',
        'who' => 'من',
        'log_type' => 'النوع',
        'log_type_comment' => 'تعليق',
        'log_type_status_change' => 'تغيير الحالة',
        'log_type_priority_change' => 'تغيير الأولوية',
        'log_type_internal_note' => 'ملاحظة داخلية',
        'actor_admin' => 'مسؤول',
        'actor_user' => 'مستخدم',
        'actions_menu' => 'إجراءات',
        'post_report_reason' => 'سبب البلاغ',
        'post_report_details' => 'تفاصيل إضافية من المُبلّغ',
    ],

    // Post report reasons (user API + support tickets)
    'post_reports' => [
        'reasons' => [
            'spam' => 'محتوى دعائي أو سبام',
            'harassment' => 'تحرش أو تنمر',
            'hate_speech' => 'خطاب كراهية',
            'violence' => 'عنف أو تهديدات',
            'misinformation' => 'معلومات مضللة',
            'nudity_or_sexual' => 'محتوى جنسي أو عري',
            'illegal_content' => 'محتوى غير قانوني',
            'copyright' => 'انتهاك حقوق نشر أو ملكية',
            'impersonation' => 'انتحال شخصية',
            'self_harm' => 'إيذاء النفس أو سلوك خطير',
        ],
    ],

    // Placeholders & filters
    'placeholder' => [
        'empty' => '—',
        'no_roles' => 'لا توجد أدوار أو صلاحيات معيّنة لهذا المسؤول',
        'select' => 'اختر...',
    ],
    'filters' => [
        'select_shop_first' => 'اختر المحل أولاً',
        'all' => 'الكل',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
    ],

    // Activity Log (used for copy message)
    'activity' => [
        'copied' => 'تم النسخ إلى الحافظة',
    ],

    // Permissions Translation
    'permissions_list' => [
        // Admin Management
        'view_admins' => 'عرض المسؤولين',
        'create_admins' => 'إضافة مسؤول',
        'edit_admins' => 'تعديل مسؤول',
        'delete_admins' => 'حذف مسؤول',
        'restore_admins' => 'استعادة مسؤول',
        'force_delete_admins' => 'حذف مسؤول نهائياً',

        // Role Management
        'view_roles' => 'عرض الأدوار',
        'create_roles' => 'إضافة دور',
        'edit_roles' => 'تعديل دور',
        'delete_roles' => 'حذف دور',

        // Permission Management
        'view_permissions' => 'عرض الصلاحيات',

        // Dashboard
        'view_dashboard' => 'عرض لوحة التحكم',

        // Settings
        'view_settings' => 'عرض الإعدادات',
        'edit_settings' => 'تعديل الإعدادات',

        // Shop Categories (فئات المحلات)
        'view_main_categories' => 'عرض فئات المحلات',
        'create_main_categories' => 'إضافة فئة محل',
        'edit_main_categories' => 'تعديل فئة محل',
        'delete_main_categories' => 'حذف فئة محل',

        // Shop Management
        'view_shops' => 'عرض المحلات',
        'create_shops' => 'إضافة محل',
        'edit_shops' => 'تعديل محل',
        'delete_shops' => 'حذف محل',

        // Shop Sections (أقسام المحلات)
        'view_shop_sections' => 'عرض أقسام المحلات',
        'create_shop_sections' => 'إضافة قسم محل',
        'edit_shop_sections' => 'تعديل قسم محل',
        'delete_shop_sections' => 'حذف قسم محل',

        // Products
        'view_products' => 'عرض المنتجات',
        'create_products' => 'إضافة منتج',
        'edit_products' => 'تعديل منتج',
        'delete_products' => 'حذف منتج',

        // Shop Orders (طلبات المحلات)
        'view_shop_orders' => 'عرض طلبات المحلات',
        'create_shop_orders' => 'إنشاء طلبات المحلات',
        'edit_shop_orders' => 'تعديل طلبات المحلات',
        'delete_shop_orders' => 'حذف طلبات المحلات',
        'manage_shop_orders' => 'إدارة حالة طلبات المحلات',

        // Directory – Main categories
        'view_directory_main_categories' => 'عرض أقسام الدليل',
        'create_directory_main_categories' => 'إضافة قسم دليل',
        'edit_directory_main_categories' => 'تعديل قسم دليل',
        'delete_directory_main_categories' => 'حذف قسم دليل',

        // Directory – Sub categories
        'view_directory_sub_categories' => 'عرض الأقسام الفرعية للدليل',
        'create_directory_sub_categories' => 'إضافة قسم فرعي للدليل',
        'edit_directory_sub_categories' => 'تعديل قسم فرعي للدليل',
        'delete_directory_sub_categories' => 'حذف قسم فرعي للدليل',

        // Directory – Listings
        'view_directory_listings' => 'عرض قوائم الدليل',
        'create_directory_listings' => 'إضافة قائمة دليل',
        'edit_directory_listings' => 'تعديل قائمة دليل',
        'delete_directory_listings' => 'حذف قائمة دليل',

        // News Categories
        'view_news_categories' => 'عرض أقسام الأخبار',
        'create_news_categories' => 'إضافة قسم أخبار',
        'edit_news_categories' => 'تعديل قسم أخبار',
        'delete_news_categories' => 'حذف قسم أخبار',

        // News
        'view_news' => 'عرض الأخبار',
        'create_news' => 'إضافة خبر',
        'edit_news' => 'تعديل خبر',
        'delete_news' => 'حذف خبر',

        // Ads
        'view_ads' => 'عرض الإعلانات',
        'create_ads' => 'إضافة إعلان',
        'edit_ads' => 'تعديل إعلان',
        'delete_ads' => 'حذف إعلان',

        // Content Pages
        'view_content_pages' => 'عرض صفحات المحتوى',
        'create_content_pages' => 'إضافة صفحة محتوى',
        'edit_content_pages' => 'تعديل صفحة المحتوى',
        'delete_content_pages' => 'حذف صفحة المحتوى',

        // Marketplace Categories
        'view_marketplace_categories' => 'عرض فئات السوق',
        'create_marketplace_categories' => 'إضافة فئة سوق',
        'edit_marketplace_categories' => 'تعديل فئة سوق',
        'delete_marketplace_categories' => 'حذف فئة سوق',

        // Marketplace Listings
        'view_marketplace_listings' => 'عرض قوائم السوق',
        'create_marketplace_listings' => 'إضافة قائمة سوق',
        'edit_marketplace_listings' => 'تعديل قائمة سوق',
        'delete_marketplace_listings' => 'حذف قائمة سوق',

        // Users
        'view_users' => 'عرض المستخدمين',
        'create_users' => 'إضافة مستخدم',
        'edit_users' => 'تعديل مستخدم',
        'delete_users' => 'حذف مستخدم',
        'restore_users' => 'استعادة مستخدم',
        'force_delete_users' => 'حذف مستخدم نهائياً',

        // Posts
        'view_posts' => 'عرض المنشورات',
        'create_posts' => 'إضافة منشور',
        'edit_posts' => 'تعديل المنشورات',
        'delete_posts' => 'حذف المنشورات',
        'restore_posts' => 'استعادة المنشورات',
        'force_delete_posts' => 'حذف منشور نهائياً',

        // Post comment presets
        'view_post_comment_presets' => 'عرض التعليقات الجاهزة',
        'create_post_comment_presets' => 'إضافة تعليق جاهز',
        'edit_post_comment_presets' => 'تعديل تعليق جاهز',
        'delete_post_comment_presets' => 'حذف تعليق جاهز',
        'restore_post_comment_presets' => 'استعادة تعليق جاهز',
        'force_delete_post_comment_presets' => 'حذف تعليق جاهز نهائياً',

        // Transportation Management (وسائل النقل)
        'view_transportation_areas' => 'عرض مناطق وسائل النقل',
        'create_transportation_areas' => 'إضافة منطقة وسائل النقل',
        'edit_transportation_areas' => 'تعديل منطقة وسائل النقل',
        'delete_transportation_areas' => 'حذف منطقة وسائل النقل',
        'view_transportation_drivers' => 'عرض سائقين وسائل النقل',
        'create_transportation_drivers' => 'إضافة سائق وسائل النقل',
        'edit_transportation_drivers' => 'تعديل سائق وسائل النقل',
        'delete_transportation_drivers' => 'حذف سائق وسائل النقل',
        'view_transportation_trips' => 'عرض رحلات وسائل النقل',
        'create_transportation_trips' => 'إضافة رحلة وسائل النقل',
        'edit_transportation_trips' => 'تعديل رحلة وسائل النقل',
        'delete_transportation_trips' => 'حذف رحلة وسائل النقل',
    ],

    // Dashboard
    'dashboard' => [
        'section_marketplace' => 'إحصائيات السوق',
        'section_content' => 'إحصائيات المحتوى والإعلانات',
        'section_shops' => 'إحصائيات المحلات والمنتجات',
        'section_directory' => 'إحصائيات دليل الهاتف',
        'section_access' => 'إحصائيات المستخدمين والصلاحيات',
        'section_transportation' => 'إحصائيات وسائل النقل',

        'total_marketplace_listings' => 'إجمالي إعلانات السوق',
        'active_marketplace_listings' => 'إعلانات نشطة: :count',
        'pending_marketplace_listings' => 'إعلانات قيد المراجعة',
        'pending_marketplace_listings_help' => 'إعلانات تحتاج مراجعة وموافقة من المسؤول.',

        'total_marketplace_categories' => 'فئات السوق',
        'marketplace_categories_active_of_total' => 'فئات نشطة: :active من :total',

        'total_news_categories' => 'أقسام الأخبار',
        'news_categories_active_of_total' => 'أقسام نشطة: :active من :total',
        'total_news' => 'إجمالي الأخبار',
        'news_published_of_total' => 'أخبار منشورة: :published من :total',

        'total_shop_categories' => 'فئات المحلات',
        'shop_categories_active_of_total' => 'فئات نشطة: :active من :total',
        'total_shops' => 'إجمالي المحلات',
        'shops_active_of_total' => 'محلات نشطة: :active من :total',
        'total_shop_sections' => 'أقسام المحلات',
        'shop_sections_active_of_total' => 'أقسام نشطة: :active من :total',
        'total_products' => 'إجمالي المنتجات',
        'products_active_of_total' => 'منتجات نشطة: :active من :total',

        'total_shop_orders' => 'طلبات المحلات',
        'shop_orders_status_pending' => 'قيد الانتظار: :count',
        'shop_orders_status_accepted' => 'مقبول: :count',
        'shop_orders_status_rejected' => 'مرفوض: :count',
        'shop_orders_status_on_the_way' => 'في الطريق: :count',
        'shop_orders_status_delivered' => 'تم التوصيل: :count',
        'shop_orders_status_cancelled' => 'ملغى: :count',

        'total_directory_main_categories' => 'أقسام الدليل الرئيسية',
        'directory_main_categories_active_of_total' => 'أقسام نشطة: :active من :total',
        'total_directory_sub_categories' => 'الأقسام الفرعية للدليل',
        'directory_sub_categories_active_of_total' => 'أقسام فرعية نشطة: :active من :total',
        'total_directory_listings' => 'إجمالي قوائم الدليل',
        'directory_listings_active_of_total' => 'قوائم نشطة: :active من :total',
        'directory_listings_active_pending_total' => ':active نشطة، :pending قيد المراجعة من :total',
        'directory_listings_active_pending_rejected_total' => ':active نشطة، :pending قيد المراجعة، :rejected مرفوضة من :total',

        'total_ads' => 'إجمالي الإعلانات',
        'ads_visible_of_total' => 'إعلانات ظاهرة للمستخدمين: :visible من :total',

        'total_transportation_areas' => 'مناطق وسائل النقل',
        'transportation_areas_active_of_total' => 'مناطق نشطة: :active من :total',
        'total_transportation_drivers' => 'سائقين وسائل النقل',
        'transportation_drivers_active_of_total' => 'سائقين نشطين: :active من :total',
        'total_transportation_trips' => 'رحلات وسائل النقل',
        'transportation_trips_active_of_total' => 'رحلات نشطة: :active من :total',

        'total_users' => 'إجمالي المستخدمين',
        'users_active_of_total' => 'مستخدمون نشطون: :active من :total',

        'total_admins' => 'إجمالي المسؤولين',
        'admins_active_of_total' => 'مسؤولون نشطون: :active من :total',
        'total_roles' => 'إجمالي الأدوار',
        'total_roles_help' => 'الأدوار المستخدمة للتحكم في صلاحيات المسؤولين.',
        'total_permissions' => 'إجمالي الصلاحيات',
        'total_permissions_help' => 'كل الصلاحيات المعرفة في النظام.',

        'section_jobs' => 'إحصائيات الوظائف',
        'total_job_listings' => 'إجمالي إعلانات الوظائف',
        'job_listings_active_of_total' => 'إعلانات نشطة: :active من :total',
        'pending_job_listings' => 'وظائف قيد المراجعة',
        'pending_job_listings_help' => 'إعلانات في انتظار المراجعة والموافقة.',
        'job_listings_by_status' => 'مرفوضة / مغلقة',
        'job_listings_rejected_closed' => 'مرفوضة: :rejected، مغلقة: :closed',

        'section_events' => 'إحصائيات الأحداث',
        'total_events' => 'إجمالي الأحداث',
        'events_active_of_total' => 'أحداث نشطة: :active من :total',
        'pending_events' => 'أحداث قيد المراجعة',
        'pending_events_help' => 'أحداث في انتظار المراجعة والموافقة.',
        'events_by_status' => 'مرفوضة / مغلقة',
        'events_rejected_closed' => 'مرفوضة: :rejected، مغلقة: :closed',

        'section_support_tickets' => 'إحصائيات تذاكر الدعم',
        'total_support_tickets' => 'إجمالي تذاكر الدعم',
        'support_tickets_total_help' => 'كل التذاكر (مفتوحة، قيد المعالجة، مغلقة).',
        'support_tickets_open_help' => 'تذاكر في انتظار المعالجة.',
        'support_tickets_in_progress_help' => 'تذاكر قيد المعالجة.',
        'support_tickets_closed_help' => 'تذاكر تم إغلاقها.',

        'marketplace_by_status_heading' => 'إعلانات السوق حسب الحالة',
        'marketplace_by_status_dataset' => 'عدد الإعلانات',
    ],

    // Directory (phone directory)
    'directory' => [
        'nav' => [
            'directory_categories' => 'أقسام الدليل',
            'directory_sub_categories' => 'الأقسام الفرعية للدليل',
            'directory_listing' => 'قوائم الدليل',
            'pending_listings' => 'الدليل - قيد المراجعة',
            'all_listings' => 'كل القوائم',
        ],
        'main_category' => 'القسم الرئيسي',
        'section_info' => 'معلومات القسم',
        'sub_category' => 'القسم الفرعي',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
        'phone1' => 'الهاتف الأول',
        'phone2' => 'الهاتف الثاني',
        'validation' => [
            'sub_category_must_belong_to_main' => 'القسم الفرعي يجب أن يتبع القسم الرئيسي المحدد.',
            'phone2_different_from_phone1' => 'الهاتف الثاني يجب أن يختلف عن الهاتف الأول.',
            'cannot_delete_has_sub_categories' => 'لا يمكن حذف هذا القسم لوجود أقسام فرعية مرتبطة به.',
            'cannot_delete_has_listings' => 'لا يمكن حذف هذا القسم لوجود قوائم مرتبطة به.',
        ],
        'sub_categories_tab' => 'الأقسام الفرعية',
        'listings_tab' => 'قوائم الدليل',
        'add_sub_category' => 'إضافة قسم فرعي',
        'add_listing' => 'إضافة قائمة',
        'status' => 'الحالة',
        'status_pending' => 'قيد المراجعة',
        'status_approved' => 'معتمد',
        'status_rejected' => 'مرفوض',
        'approve' => 'اعتماد',
        'approve_confirm' => 'اعتماد هذا السجل للنشر؟',
        'reject' => 'رفض',
        'reject_confirm' => 'رفض هذا السجل؟ لن يتم نشره.',
        'created_by_user' => 'المستخدم',
        'not_owner' => 'أنت لست مالك هذا السجل في الدليل.',
    ],

    // Job Listings
    'job_listing' => [
        'nav' => [
            'job_listings' => 'الوظائف',
            'pending_listings' => 'الوظائف - قيد المراجعة',
            'all_listings' => 'كل الطلبات',
        ],
        'status' => 'الحالة',
        'status_pending' => 'قيد المراجعة',
        'status_active' => 'نشط',
        'status_rejected' => 'مرفوض',
        'status_closed' => 'مغلق',
        'company_name' => 'اسم الشركة',
        'location' => 'الموقع',
        'user' => 'المستخدم',
        'approve' => 'اعتماد',
        'approve_confirm' => 'اعتماد هذه الوظيفة للنشر؟',
        'reject' => 'رفض',
        'reject_confirm' => 'رفض هذه الوظيفة؟ لن يتم نشرها.',
    ],

    // Events
    'event' => [
        'nav' => [
            'events' => 'الأحداث',
            'pending_listings' => 'الأحداث - قيد المراجعة',
            'all_listings' => 'كل الأحداث',
        ],
        'status' => 'الحالة',
        'status_pending' => 'قيد المراجعة',
        'status_active' => 'نشط',
        'status_rejected' => 'مرفوض',
        'status_closed' => 'مغلق',
        'location' => 'الموقع',
        'start_at' => 'تاريخ ووقت البداية',
        'end_at' => 'تاريخ ووقت النهاية',
        'user' => 'المستخدم',
        'approve' => 'اعتماد',
        'approve_confirm' => 'اعتماد هذا الحدث للنشر؟',
        'reject' => 'رفض',
        'reject_confirm' => 'رفض هذا الحدث؟ لن يتم نشره.',
        'attendees' => 'المسجلون',
        'attendee_singular' => 'مسجل',
    ],

    // Marketplace
    'marketplace' => [
        'nav' => [
            'marketplace_categories' => 'فئات السوق',
            'marketplace_listings' => 'قوائم السوق',
            'review' => 'مراجعة',
            'pending_listings' => 'السوق - قيد المراجعة',
            'all_listings' => 'كل القوائم',
        ],
        'section_info' => 'معلومات القائمة',
        'category' => 'الفئة',
        'listing_title' => 'العنوان',
        'condition' => 'الحالة',
        'condition_label' => 'حالة المنتج',
        'status_label' => 'حالة الإعلان',
        'condition_new' => 'جديد',
        'condition_used' => 'مستعمل',
        'status' => 'الحالة',
        'status_pending' => 'قيد المراجعة',
        'status_active' => 'نشط',
        'status_sold' => 'تم البيع',
        'status_hidden' => 'مخفي',
        'status_rejected' => 'مرفوض',
        'created_by_user' => 'أنشئ بواسطة المستخدم',
        'reviewed_by' => 'راجع بواسطة',
        'reviewed_at' => 'تاريخ المراجعة',
        'phone' => 'رقم الهاتف',
        'whatsapp_phone' => 'رقم الواتساب',
        'whatsapp_phone_hint' => 'اختياري. اتركه فارغاً إن لم يتوفر واتساب. يمكن أن يكون نفس رقم الهاتف.',
        'has_whatsapp' => 'يوجد واتساب',
        'views' => 'المشاهدات',
        'whatsapp_clicks' => 'نقرات واتساب',
        'calls_clicks' => 'نقرات الاتصال',
        'listings_tab' => 'قوائم السوق',
        'approve' => 'موافقة',
        'reject' => 'رفض',
        'hide' => 'إخفاء',
        'mark_sold' => 'وضع كـ مباع',
        'notified_approved' => 'تمت الموافقة على الإعلان',
        'notified_rejected' => 'تم رفض الإعلان',
        'notified_hidden' => 'تم إخفاء الإعلان',
        'notified_sold' => 'تم وضع الإعلان كـ مباع',
    ],

    'marketplace_category' => [
        'category_information' => 'معلومات الفئة',
        'listings_count' => 'القوائم',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
    ],

    // News
    'news' => [
        'nav' => [
            'news_categories' => 'أقسام الأخبار',
            'news' => 'قوائم الأخبار',
            'add_news' => 'إضافة خبر',
            'section_info' => 'معلومات القسم',
            'news_info' => 'معلومات الخبر',
        ],
        'title' => 'العنوان',
        'body' => 'المحتوى',
        'priority' => 'الأولوية',
        'priority_normal' => 'عادي',
        'priority_urgent' => 'عاجل',
        'publish_at' => 'تاريخ النشر',
        'expires_at' => 'تاريخ الانتهاء',
        'created_by' => 'تم الإنشاء بواسطة',
        'images' => 'الصور',
        'status' => 'الحالة',
        'badge_urgent' => 'عاجل',
        'badge_expired' => 'منتهي',
        'badge_scheduled' => 'مجدول',
        'badge_published' => 'منشور',
        'name_en' => 'الاسم (إنجليزي)',
        'name_ar' => 'الاسم (عربي)',
    ],

    // Transport (legacy)
    'transport' => [
        'nav' => [
            'transport_areas' => 'مناطق وسائل النقل',
            'transport_drivers' => 'سائقين وسائل النقل',
        ],
        'area' => [
            'information_section_title' => 'معلومات منطقة وسائل النقل',
            'meeting_place' => 'مكان التجمع',
            'fare_price' => 'سعر الأجرة',
            'notes' => 'ملاحظات',
            'drivers_tab' => 'السائقون',
            'drivers_count' => 'عدد السائقين',
            'drivers_field_label' => 'السائقون المرتبطون',
        ],
        'driver' => [
            'information_section_title' => 'معلومات سائق وسائل النقل',
            'areas_tab' => 'المناطق',
        ],
    ],

    // Transportation
    'transportation' => [
        'area' => [
            'information_section_title' => 'معلومات منطقة وسائل النقل',
            'meeting_place' => 'مكان التجمع',
            'fare_price' => 'سعر الأجرة',
            'notes' => 'ملاحظات',
            'schedules' => 'المواعيد',
            'drivers_tab' => 'السائقون',
            'drivers_count' => 'عدد السائقين',
            'drivers_field_label' => 'السائقون المرتبطون',
        ],
        'driver' => [
            'information_section_title' => 'معلومات سائق وسائل النقل',
            'areas_tab' => 'المناطق',
        ],
        'nav' => [
            'transportation_areas' => 'مناطق وسائل النقل',
            'transportation_drivers' => 'سائقين وسائل النقل',
            'transportation_trips' => 'رحلات وسائل النقل',
        ],
        'trip' => [
            'information_section_title' => 'معلومات الرحلة',
            'from_area' => 'من منطقة',
            'to_area' => 'إلى منطقة',
            'departure_at' => 'وقت المغادرة',
            'departure_at_format' => 'd/m/Y H:i',
            'available_seats' => 'المقاعد المتاحة',
            'price_per_seat' => 'السعر للمقعد',
            'meeting_point' => 'نقطة اللقاء',
            'contact_phone' => 'هاتف التواصل',
            'contact_whatsapp' => 'واتساب',
            'contact_actions' => 'التواصل',
            'action_call' => 'اتصال',
            'action_whatsapp' => 'واتساب',
            'status' => 'الحالة',
            'status_open' => 'مفتوح',
            'status_full' => 'مكتمل',
            'status_closed' => 'مغلق',
            'status_expired' => 'منتهي',
            'views_count' => 'المشاهدات',
            'clicks_count' => 'النقرات',
            'phone_clicks_count' => 'نقرات الهاتف',
            'whatsapp_clicks_count' => 'نقرات الواتساب',
            'owner' => 'صاحب الرحلة',
        ],
    ],

    // Ads
    'ads' => [
        'nav' => [
            'ads' => 'الإعلانات',
            'section_info' => 'معلومات الإعلان',
        ],
        'title' => 'العنوان',
        'image' => 'الصورة',
        'action_type' => 'نوع الإجراء',
        'action_value' => 'قيمة الإجراء',
        'priority' => 'الأولوية',
        'priority_normal' => 'عادي',
        'priority_featured' => 'مميز',
        'clicks' => 'النقرات',
        'publish_at' => 'تاريخ النشر',
        'expires_at' => 'تاريخ الانتهاء',
        'created_by' => 'تم الإنشاء بواسطة',
        'action_type_shop' => 'محل',
        'action_type_product' => 'منتج',
        'action_type_directory_listing' => 'قائمة الدليل',
        'action_type_external_url' => 'رابط خارجي',
    ],
];
