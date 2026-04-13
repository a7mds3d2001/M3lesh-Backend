<?php

return [
    // Common Actions
    'actions' => [
        'edit' => 'Edit',
        'delete' => 'Delete',
        'restore' => 'Restore',
        'force_delete' => 'Delete permanently',
        'back' => 'Back',
        'view' => 'View details',
        'accept' => 'Accept',
        'reject' => 'Reject',
        'on_the_way' => 'On the way',
        'delivered' => 'Delivered',
    ],

    // Error / access denied messages
    'errors' => [
        'cannot_edit_super_admin' => 'The Super Admin account cannot be edited.',
        'cannot_edit_super_admin_role' => 'The Super Admin role cannot be edited.',
    ],

    // Tabs
    'tabs' => [
        'info' => 'Information',
        'marketplace' => 'Marketplace',
        'admins' => 'Admins',
        'shops' => 'Shops',
        'news' => 'News',
        'drivers' => 'Drivers',
    ],

    // Navigation & Branding
    'navigation' => [
        'brand' => 'M3lesh Dashboard',
        'dashboard' => 'Dashboard',
        'management' => 'Management',
        'shop_management' => 'Shop Management',
        'directory_management' => 'Directory Management',
        'news_management' => 'News Management',
        'ads_management' => 'Ads Management',
        'content_pages_management' => 'Content Pages Management',
        'transport_management' => 'Transport Means Management',
        'transportation_management' => 'Transportation Management',
        'marketplace_management' => 'Marketplace Management',
        'users_management' => 'Users Management',
        'support' => 'Support',
        'operations' => 'Operations',
        'content' => 'Content',
        'system' => 'System',
        'access_control' => 'Access Control',
        'accounts_and_permissions' => 'Accounts & Permissions',
        'content_and_support' => 'Content & Support',
        'system_services' => 'System Services',
        'notifications_management' => 'Notifications Management',
        'posts_management' => 'Posts management',
        'jobs_management' => 'Jobs Management',
        'events_management' => 'Events Management',
        'pending_listings' => 'Pending',
        'all_listings' => 'All',
    ],

    // Resource Names (Plurals for navigation)
    'resources' => [
        'admin' => 'Admins',
        'role' => 'Roles',
        'permission' => 'Permissions',
        'shop_category' => 'Shops Categories',
        'shop_category_singular' => 'Shop Category',
        'shop' => 'Shops',
        'shop_singular' => 'Shop',
        'shop_section' => 'Shop Sections',
        'shop_section_singular' => 'Shop Section',
        'product' => 'Products',
        'product_singular' => 'Product',
        'directory_main_category' => 'Directory Categories',
        'directory_main_category_singular' => 'Directory Category',
        'directory_sub_category' => 'Directory Sub Categories',
        'directory_sub_category_singular' => 'Directory Sub Category',
        'directory_listing' => 'Directory Listing',
        'directory_listing_singular' => 'Directory Listing',
        'news_category' => 'News Categories',
        'news_category_singular' => 'News Category',
        'news' => 'News Listings',
        'news_singular' => 'News Listing',
        'transport_area' => 'Transport Means Areas',
        'transport_area_singular' => 'Transport Means Area',
        'transport_driver' => 'Transport Means Drivers',
        'transport_driver_singular' => 'Transport Means Driver',
        'transportation_area' => 'Transportation Areas',
        'transportation_area_singular' => 'Transportation Area',
        'transportation_driver' => 'Transportation Drivers',
        'transportation_driver_singular' => 'Transportation Driver',
        'transportation_trip' => 'Transportation Trips',
        'transportation_trip_singular' => 'Transportation Trip',
        'marketplace_category' => 'Marketplace Categories',
        'marketplace_category_singular' => 'Marketplace Category',
        'marketplace_listing' => 'Marketplace Listings',
        'marketplace_listing_singular' => 'Marketplace Listing',
        'ad' => 'Ads',
        'ad_singular' => 'Ad',
        'content_page' => 'Content Pages',
        'content_page_singular' => 'Content Page',
        'user' => 'Users',
        'user_singular' => 'User',
        'support_tickets' => 'Support Tickets',
        'support_ticket_singular' => 'Support Ticket',
        'job_listing' => 'Job Listings',
        'job_listing_singular' => 'Job Listing',
        'event' => 'Events',
        'event_singular' => 'Event',
        'shop_orders' => 'Shop Orders',
        'shop_order_singular' => 'Shop Order',
        'devices' => 'Devices',
        'device_singular' => 'Device',
        'notifications' => 'Notifications',
        'notification_singular' => 'Notification',
        'notification_broadcasts' => 'Notification Broadcasts',
        'notification_broadcast_singular' => 'Notification Broadcast',
    ],

    // Common Fields
    'fields' => [
        'id' => 'ID',
        'name' => 'Name',
        'email' => 'Email',
        'phone' => 'Phone',
        'created_at' => 'Created At',
        'updated_at' => 'Updated At',
        'created_by' => 'Created By',
        'updated_by' => 'Updated By',
        'is_active' => 'Is Active',
        'roles' => 'Roles',
        'permissions' => 'Permissions',
        'description' => 'Description',
        'title' => 'Title',
        'slug' => 'Slug',
        'address' => 'Address',
        'main_category' => 'Main Category',
        'shop' => 'Shop',
        'shop_section' => 'Section',
        'sort_order' => 'Sort Order',
        'price' => 'Price',
        'currency_egp' => 'EGP',
        'image_url' => 'Image URL',
        'image' => 'Image',
        'status' => 'Status',
        'total' => 'Total',
        'user' => 'User',
        'notes' => 'Notes',
        'product' => 'Product',
        'quantity' => 'Quantity',
        'order_number' => 'Order Number',
        'action' => 'Action',
        'performed_by' => 'Performed By',
        'device_id' => 'Device ID',
        'platform' => 'Platform',
        'manufacturer' => 'Manufacturer',
        'model' => 'Model',
        'os_version' => 'OS Version',
        'app_version' => 'App Version',
        'last_used_at' => 'Last Used',
        'global' => 'Global',
        'birth_date' => 'Birth date',
        'gender' => 'Gender',
        'gender_male' => 'Male',
        'gender_female' => 'Female',
    ],

    'notifications' => [
        'send_notification' => 'Send notification',
        'sent' => 'Sent',
        'created_count' => 'Notifications created: :count',
        'recipient_type' => 'Recipient type',
        'recipients' => 'Recipients',
        'recipient' => 'Recipient',
        'recipient_id' => 'Recipient ID',
        'body' => 'Body',
        'target_type' => 'Target type',
        'target_id' => 'Target',
        'data_json' => 'Data (JSON)',
        'sent_at' => 'Sent at',
        'read_at' => 'Read at',
        'select_target_type_first' => 'Select target type first...',
        'select_recipient_type_first' => 'Select recipient type first...',
        'all_users' => 'All users',
        'topic' => 'Topic',
    ],

    // Content Pages
    'content_pages' => [
        'id' => 'ID',
        'nav' => [
            'content_pages' => 'Content Pages',
            'section_info' => 'Content Page Information',
        ],
        'section_arabic' => 'Arabic',
        'section_english' => 'English',
        'title_ar' => 'Title (Arabic)',
        'content_ar' => 'Content (Arabic)',
        'title_en' => 'Title (English)',
        'content_en' => 'Content (English)',
        'title' => 'Title',
    ],

    // Audit (created by / updated by)
    'audit' => [
        'section_title' => 'Audit',
    ],

    // Empty states
    'empty' => [
        'no_devices' => 'No devices found.',
        'no_notification_broadcasts' => 'No notification broadcasts found.',
    ],

    // Admin
    'admin' => [
        'admin_information' => 'Admin Information',
        'type_admin' => 'Admin',
        'type_super_admin' => 'Super Admin',
    ],

    // Role
    'role' => [
        'role_information' => 'Role Information',
        'no_admins' => 'No admins have this role',
        'no_permissions' => 'No permissions assigned',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
    ],

    // Permission
    'permission' => [
        'permission_information' => 'Permission Information',
        'no_roles' => 'No roles have this permission',
        'key' => 'Key',
        'name_ar' => 'Name (Arabic)',
        'name_en' => 'Name (English)',
    ],

    // Shop Category
    'shop_category' => [
        'category_information' => 'Category Information',
        'shops_count' => 'Shops',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
        'add_shop' => 'Add Shop',
    ],

    // Shop
    'shop' => [
        'shop_information' => 'Shop Information',
        'sections_tab' => 'Sections',
        'products_tab' => 'Products',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
    ],

    'shop_order' => [
        'status_pending' => 'Pending',
        'status_accepted' => 'Accepted',
        'status_rejected' => 'Rejected',
        'status_on_the_way' => 'On the way',
        'status_delivered' => 'Delivered',
        'status_cancelled' => 'Cancelled',
        'products_section' => 'Order Products',
        'logs_section' => 'Order Logs',
        'log_singular' => 'Log',
        'logs' => 'Logs',
        'from_status' => 'From status',
        'to_status' => 'To status',
        'log_action_created' => 'Order created',
        'log_action_status_changed' => 'Status changed',
    ],

    // Shop Section
    'shop_section' => [
        'section_information' => 'Section Information',
        'products_count' => 'Products',
        'new_section' => 'New Section',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
    ],

    // Product
    'product' => [
        'product_information' => 'Product Information',
        'new_product' => 'New Product',
    ],

    // User
    'user' => [
        'password' => 'Password',
        'user_information' => 'User Information',
    ],

    'post' => [
        'nav' => 'Posts',
        'singular' => 'Post',
        'section' => 'Post',
        'body' => 'Content',
        'author' => 'Author',
        'likes_count' => 'Likes',
        'comments_count' => 'Comments',
        'likes_tab' => 'Likes',
        'comments_tab' => 'Comments',
        'like_singular' => 'Like',
        'comment_singular' => 'Comment',
        'liked_by' => 'User',
        'commented_by' => 'User',
        'comment_text' => 'Comment',
        'linked_post' => 'Linked post',
    ],

    'post_comment_preset' => [
        'nav' => 'Comments',
        'singular' => 'Comment preset',
        'section' => 'Comment preset',
        'text' => 'Text',
    ],

    'avatar' => [
        'nav' => 'Profile avatars',
        'singular' => 'Avatar',
        'section' => 'Avatar',
    ],

    // Support Tickets
    'support_ticket' => [
        'nav' => [
            'support_tickets' => 'Support Tickets',
        ],
        'ticket_singular' => 'Support Ticket',
        'ticket_number' => 'Ticket Number',
        'ticket_information' => 'Ticket Information',
        'owner' => 'Owner',
        'phone' => 'Phone',
        'email' => 'Email',
        'phone_email' => 'Phone / Email',
        'owner_type' => 'Ticket for',
        'link_to_user' => 'Link to User',
        'visitor' => 'Visitor',
        'visitor_name' => 'Visitor Name',
        'visitor_phone' => 'Visitor Phone',
        'visitor_email' => 'Visitor Email',
        'user' => 'User',
        'message' => 'Message',
        'status' => 'Status',
        'priority' => 'Priority',
        'status_open' => 'Open',
        'status_in_progress' => 'In Progress',
        'status_resolved' => 'Resolved',
        'status_closed' => 'Closed',
        'priority_low' => 'Low',
        'priority_normal' => 'Normal',
        'priority_high' => 'High',
        'attachments' => 'Attachments',
        'attachments_documents' => 'Documents',
        'attachment_type_image' => 'Image',
        'attachment_type_document' => 'Document',
        'attachment_click_to_view' => 'Click to view',
        'view_attachments' => 'View attachments',
        'view_message' => 'View message',
        'close' => 'Close',
        'change_status' => 'Change Status',
        'change_priority' => 'Change Priority',
        'logs_tab' => 'Logs',
        'log_singular' => 'Log',
        'add_log' => 'Add Log',
        'who' => 'Who',
        'log_type' => 'Type',
        'log_type_comment' => 'Comment',
        'log_type_status_change' => 'Status Change',
        'log_type_priority_change' => 'Priority Change',
        'log_type_internal_note' => 'Internal Note',
        'actor_admin' => 'Admin',
        'actor_user' => 'User',
        'actions_menu' => 'Actions',
        'post_report_reason' => 'Report reason',
        'post_report_details' => 'Reporter details',
    ],

    // Post report reasons (user API + support tickets)
    'post_reports' => [
        'reasons' => [
            'spam' => 'Spam or advertising',
            'harassment' => 'Harassment or bullying',
            'hate_speech' => 'Hate speech',
            'violence' => 'Violence or threats',
            'misinformation' => 'Misinformation',
            'nudity_or_sexual' => 'Nudity or sexual content',
            'illegal_content' => 'Illegal content',
            'copyright' => 'Copyright infringement',
            'impersonation' => 'Impersonation',
            'self_harm' => 'Self-harm or dangerous acts',
        ],
    ],

    // Placeholders & filters
    'placeholder' => [
        'empty' => '—',
        'no_roles' => 'No roles or permissions assigned to this admin',
        'select' => 'Select...',
    ],
    'filters' => [
        'select_shop_first' => 'Select shop first',
        'all' => 'All',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    // Activity Log (used for copy message)
    'activity' => [
        'copied' => 'Copied to clipboard',
    ],

    // Permissions Translation
    'permissions_list' => [
        // Admin Management
        'view_admins' => 'View admins',
        'create_admins' => 'Add admin',
        'edit_admins' => 'Edit admin',
        'delete_admins' => 'Delete admin',
        'restore_admins' => 'Restore admin',
        'force_delete_admins' => 'Permanently delete admin',

        // Role Management
        'view_roles' => 'View roles',
        'create_roles' => 'Add role',
        'edit_roles' => 'Edit role',
        'delete_roles' => 'Delete role',

        // Permission Management
        'view_permissions' => 'View permissions',

        // Dashboard
        'view_dashboard' => 'View dashboard',

        // Settings
        'view_settings' => 'View settings',
        'edit_settings' => 'Edit settings',

        // Shop Main Categories
        'view_main_categories' => 'View shop categories',
        'create_main_categories' => 'Add shop category',
        'edit_main_categories' => 'Edit shop category',
        'delete_main_categories' => 'Delete shop category',

        // Shop Management
        'view_shops' => 'View shops',
        'create_shops' => 'Add shop',
        'edit_shops' => 'Edit shop',
        'delete_shops' => 'Delete shop',

        // Shop Sections
        'view_shop_sections' => 'View shop sections',
        'create_shop_sections' => 'Add shop section',
        'edit_shop_sections' => 'Edit shop section',
        'delete_shop_sections' => 'Delete shop section',

        // Products
        'view_products' => 'View products',
        'create_products' => 'Add product',
        'edit_products' => 'Edit product',
        'delete_products' => 'Delete product',

        // Shop Orders
        'view_shop_orders' => 'View shop orders',
        'create_shop_orders' => 'Create shop orders',
        'edit_shop_orders' => 'Edit shop orders',
        'delete_shop_orders' => 'Delete shop orders',
        'manage_shop_orders' => 'Manage shop order status',

        // Directory – Main categories
        'view_directory_main_categories' => 'View directory categories',
        'create_directory_main_categories' => 'Add directory category',
        'edit_directory_main_categories' => 'Edit directory category',
        'delete_directory_main_categories' => 'Delete directory category',

        // Directory – Sub categories
        'view_directory_sub_categories' => 'View directory subcategories',
        'create_directory_sub_categories' => 'Add directory subcategory',
        'edit_directory_sub_categories' => 'Edit directory subcategory',
        'delete_directory_sub_categories' => 'Delete directory subcategory',

        // Directory – Listings
        'view_directory_listings' => 'View directory listings',
        'create_directory_listings' => 'Add directory listing',
        'edit_directory_listings' => 'Edit directory listing',
        'delete_directory_listings' => 'Delete directory listing',

        // News Categories
        'view_news_categories' => 'View news categories',
        'create_news_categories' => 'Add news category',
        'edit_news_categories' => 'Edit news category',
        'delete_news_categories' => 'Delete news category',

        // News
        'view_news' => 'View news',
        'create_news' => 'Add news',
        'edit_news' => 'Edit news',
        'delete_news' => 'Delete news',

        // Ads
        'view_ads' => 'View ads',
        'create_ads' => 'Add ad',
        'edit_ads' => 'Edit ad',
        'delete_ads' => 'Delete ad',

        // Content Pages
        'view_content_pages' => 'View content pages',
        'create_content_pages' => 'Add content page',
        'edit_content_pages' => 'Edit content page',
        'delete_content_pages' => 'Delete content page',

        // Marketplace Categories
        'view_marketplace_categories' => 'View marketplace categories',
        'create_marketplace_categories' => 'Add marketplace category',
        'edit_marketplace_categories' => 'Edit marketplace category',
        'delete_marketplace_categories' => 'Delete marketplace category',

        // Marketplace Listings
        'view_marketplace_listings' => 'View marketplace listings',
        'create_marketplace_listings' => 'Add marketplace listing',
        'edit_marketplace_listings' => 'Edit marketplace listing',
        'delete_marketplace_listings' => 'Delete marketplace listing',

        // Users
        'view_users' => 'View users',
        'create_users' => 'Add user',
        'edit_users' => 'Edit user',
        'delete_users' => 'Delete user',
        'restore_users' => 'Restore user',
        'force_delete_users' => 'Permanently delete user',

        // Posts
        'view_posts' => 'View posts',
        'create_posts' => 'Create post',
        'edit_posts' => 'Edit posts',
        'delete_posts' => 'Delete posts',
        'restore_posts' => 'Restore posts',
        'force_delete_posts' => 'Permanently delete posts',

        // Post comment presets
        'view_post_comment_presets' => 'View comment presets',
        'create_post_comment_presets' => 'Add comment preset',
        'edit_post_comment_presets' => 'Edit comment preset',
        'delete_post_comment_presets' => 'Delete comment preset',
        'restore_post_comment_presets' => 'Restore comment preset',
        'force_delete_post_comment_presets' => 'Permanently delete comment preset',

        // Profile avatars
        'view_avatars' => 'View profile avatars',
        'create_avatars' => 'Add profile avatar',
        'delete_avatars' => 'Delete profile avatar',

        // Transport Management
        'view_transportation_areas' => 'View transportation areas',
        'create_transportation_areas' => 'Add transportation area',
        'edit_transportation_areas' => 'Edit transportation area',
        'delete_transportation_areas' => 'Delete transportation area',
        'view_transportation_drivers' => 'View transportation drivers',
        'create_transportation_drivers' => 'Add transportation driver',
        'edit_transportation_drivers' => 'Edit transportation driver',
        'delete_transportation_drivers' => 'Delete transportation driver',
        'view_transportation_trips' => 'View transportation trips',
        'create_transportation_trips' => 'Add transportation trip',
        'edit_transportation_trips' => 'Edit transportation trip',
        'delete_transportation_trips' => 'Delete transportation trip',
    ],

    // Dashboard
    'dashboard' => [
        'section_marketplace' => 'Marketplace stats',
        'section_content' => 'Content & ads stats',
        'section_shops' => 'Shop & product stats',
        'section_directory' => 'Directory stats',
        'section_access' => 'Users & access stats',
        'section_transportation' => 'Transportation stats',

        'total_marketplace_listings' => 'Total marketplace listings',
        'active_marketplace_listings' => 'Active listings: :count',
        'pending_marketplace_listings' => 'Pending marketplace listings',
        'pending_marketplace_listings_help' => 'Listings waiting for review and approval.',

        'total_marketplace_categories' => 'Marketplace categories',
        'marketplace_categories_active_of_total' => 'Active categories: :active of :total',

        'total_news_categories' => 'News categories',
        'news_categories_active_of_total' => 'Active categories: :active of :total',
        'total_news' => 'Total news',
        'news_published_of_total' => 'Published news: :published of :total',

        'total_shop_categories' => 'Shop categories',
        'shop_categories_active_of_total' => 'Active categories: :active of :total',
        'total_shops' => 'Total shops',
        'shops_active_of_total' => 'Active shops: :active of :total',
        'total_shop_sections' => 'Shop sections',
        'shop_sections_active_of_total' => 'Active sections: :active of :total',
        'total_products' => 'Total products',
        'products_active_of_total' => 'Active products: :active of :total',

        'total_shop_orders' => 'Shop orders',
        'shop_orders_status_pending' => 'Pending: :count',
        'shop_orders_status_accepted' => 'Accepted: :count',
        'shop_orders_status_rejected' => 'Rejected: :count',
        'shop_orders_status_on_the_way' => 'On the way: :count',
        'shop_orders_status_delivered' => 'Delivered: :count',
        'shop_orders_status_cancelled' => 'Cancelled: :count',

        'total_directory_main_categories' => 'Directory main categories',
        'directory_main_categories_active_of_total' => 'Active categories: :active of :total',
        'total_directory_sub_categories' => 'Directory sub categories',
        'directory_sub_categories_active_of_total' => 'Active sub categories: :active of :total',
        'total_directory_listings' => 'Total directory listings',
        'directory_listings_active_of_total' => 'Active listings: :active of :total',
        'directory_listings_active_pending_total' => ':active active, :pending pending of :total',
        'directory_listings_active_pending_rejected_total' => ':active active, :pending pending, :rejected rejected of :total',

        'total_ads' => 'Total ads',
        'ads_visible_of_total' => 'Ads visible to users: :visible of :total',

        'total_transportation_areas' => 'Transportation areas',
        'transportation_areas_active_of_total' => 'Active areas: :active of :total',
        'total_transportation_drivers' => 'Transportation drivers',
        'transportation_drivers_active_of_total' => 'Active drivers: :active of :total',
        'total_transportation_trips' => 'Transportation trips',
        'transportation_trips_active_of_total' => 'Active trips: :active of :total',

        'total_users' => 'Total users',
        'users_active_of_total' => 'Active users: :active of :total',

        'total_admins' => 'Total admins',
        'admins_active_of_total' => 'Active admins: :active of :total',
        'total_roles' => 'Total roles',
        'total_roles_help' => 'Roles used to organize admin permissions.',
        'total_permissions' => 'Total permissions',
        'total_permissions_help' => 'All permissions defined in the system.',

        'section_jobs' => 'Jobs stats',
        'total_job_listings' => 'Total job listings',
        'job_listings_active_of_total' => 'Active listings: :active of :total',
        'pending_job_listings' => 'Pending job listings',
        'pending_job_listings_help' => 'Listings waiting for review and approval.',
        'job_listings_by_status' => 'Rejected / closed',
        'job_listings_rejected_closed' => 'Rejected: :rejected, Closed: :closed',

        'section_events' => 'Events stats',
        'total_events' => 'Total events',
        'events_active_of_total' => 'Active events: :active of :total',
        'pending_events' => 'Pending events',
        'pending_events_help' => 'Events waiting for review and approval.',
        'events_by_status' => 'Rejected / closed',
        'events_rejected_closed' => 'Rejected: :rejected, Closed: :closed',

        'section_support_tickets' => 'Support tickets stats',
        'total_support_tickets' => 'Total support tickets',
        'support_tickets_total_help' => 'All tickets (open, in progress, closed).',
        'support_tickets_open_help' => 'Tickets waiting for handling.',
        'support_tickets_in_progress_help' => 'Tickets being worked on.',
        'support_tickets_closed_help' => 'Tickets already closed.',

        'marketplace_by_status_heading' => 'Marketplace listings by status',
        'marketplace_by_status_dataset' => 'Listings count',
    ],

    // Directory (phone directory)
    'directory' => [
        'nav' => [
            'directory_categories' => 'Directory Categories',
            'directory_sub_categories' => 'Directory Sub Categories',
            'directory_listing' => 'Directory Listing',
            'pending_listings' => 'Directory - Pending',
            'all_listings' => 'All Listings',
        ],
        'main_category' => 'Main Category',
        'section_info' => 'Category Information',
        'sub_category' => 'Sub Category',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
        'phone1' => 'Phone 1',
        'phone2' => 'Phone 2',
        'validation' => [
            'sub_category_must_belong_to_main' => 'The sub category must belong to the selected main category.',
            'phone2_different_from_phone1' => 'Phone 2 must be different from Phone 1.',
            'cannot_delete_has_sub_categories' => 'Cannot delete: this category has sub categories.',
            'cannot_delete_has_listings' => 'Cannot delete: this category has listings.',
        ],
        'sub_categories_tab' => 'Sub Categories',
        'listings_tab' => 'Listings',
        'add_sub_category' => 'Add Sub Category',
        'add_listing' => 'Add Listing',
        'status' => 'Status',
        'status_pending' => 'Pending',
        'status_approved' => 'Approved',
        'status_rejected' => 'Rejected',
        'approve' => 'Approve',
        'approve_confirm' => 'Approve this listing for publication?',
        'reject' => 'Reject',
        'reject_confirm' => 'Reject this listing? It will not be published.',
        'created_by_user' => 'User',
        'not_owner' => 'You do not own this directory listing.',
    ],

    // Job Listings
    'job_listing' => [
        'nav' => [
            'job_listings' => 'Job Listings',
            'pending_listings' => 'Jobs - Pending',
            'all_listings' => 'All Listings',
        ],
        'status' => 'Status',
        'status_pending' => 'Pending',
        'status_active' => 'Active',
        'status_rejected' => 'Rejected',
        'status_closed' => 'Closed',
        'company_name' => 'Company Name',
        'location' => 'Location',
        'user' => 'User',
        'approve' => 'Approve',
        'approve_confirm' => 'Approve this job listing for publication?',
        'reject' => 'Reject',
        'reject_confirm' => 'Reject this job listing? It will not be published.',
    ],

    // Events
    'event' => [
        'nav' => [
            'events' => 'Events',
            'pending_listings' => 'Events - Pending',
            'all_listings' => 'All Events',
        ],
        'status' => 'Status',
        'status_pending' => 'Pending',
        'status_active' => 'Active',
        'status_rejected' => 'Rejected',
        'status_closed' => 'Closed',
        'location' => 'Location',
        'start_at' => 'Start Date & Time',
        'end_at' => 'End Date & Time',
        'user' => 'User',
        'approve' => 'Approve',
        'approve_confirm' => 'Approve this event for publication?',
        'reject' => 'Reject',
        'reject_confirm' => 'Reject this event? It will not be published.',
        'attendees' => 'Attendees',
        'attendee_singular' => 'Attendee',
    ],

    // Marketplace
    'marketplace' => [
        'nav' => [
            'marketplace_categories' => 'Marketplace Categories',
            'marketplace_listings' => 'Marketplace Listings',
            'review' => 'Review',
            'pending_listings' => 'Marketplace - Pending',
            'all_listings' => 'All Listings',
        ],
        'section_info' => 'Listing Information',
        'category' => 'Category',
        'listing_title' => 'Title',
        'condition' => 'Condition',
        'condition_label' => 'Product condition',
        'status_label' => 'Listing status',
        'condition_new' => 'New',
        'condition_used' => 'Used',
        'status' => 'Status',
        'status_pending' => 'Pending',
        'status_active' => 'Active',
        'status_sold' => 'Sold',
        'status_hidden' => 'Hidden',
        'status_rejected' => 'Rejected',
        'created_by_user' => 'Created By User',
        'reviewed_by' => 'Reviewed By',
        'reviewed_at' => 'Reviewed At',
        'phone' => 'Phone',
        'whatsapp_phone' => 'WhatsApp number',
        'whatsapp_phone_hint' => 'Optional. Leave empty if no WhatsApp. Can be same as phone.',
        'has_whatsapp' => 'Has WhatsApp',
        'views' => 'Views',
        'whatsapp_clicks' => 'WhatsApp Clicks',
        'calls_clicks' => 'Calls Clicks',
        'listings_tab' => 'Listings',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'hide' => 'Hide',
        'mark_sold' => 'Mark as Sold',
        'notified_approved' => 'Listing approved',
        'notified_rejected' => 'Listing rejected',
        'notified_hidden' => 'Listing hidden',
        'notified_sold' => 'Listing marked as sold',
    ],

    'marketplace_category' => [
        'category_information' => 'Category Information',
        'listings_count' => 'Listings',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
    ],

    // News
    'news' => [
        'nav' => [
            'news_categories' => 'News Categories',
            'news' => 'News Listings',
            'add_news' => 'Add News',
            'section_info' => 'Section Information',
            'news_info' => 'News Information',
        ],
        'title' => 'Title',
        'body' => 'Body',
        'priority' => 'Priority',
        'priority_normal' => 'Normal',
        'priority_urgent' => 'Urgent',
        'publish_at' => 'Publish At',
        'expires_at' => 'Expires At',
        'created_by' => 'Created By',
        'images' => 'Images',
        'status' => 'Status',
        'badge_urgent' => 'Urgent',
        'badge_expired' => 'Expired',
        'badge_scheduled' => 'Scheduled',
        'badge_published' => 'Published',
        'name_en' => 'Name (English)',
        'name_ar' => 'Name (Arabic)',
    ],

    // Transport (legacy)
    'transport' => [
        'nav' => [
            'transport_areas' => 'Transport Means Areas',
            'transport_drivers' => 'Transport Means Drivers',
        ],
        'area' => [
            'information_section_title' => 'Transport Means Area Information',
            'meeting_place' => 'Meeting Place',
            'fare_price' => 'Fare Price',
            'notes' => 'Notes',
            'drivers_tab' => 'Drivers',
            'drivers_count' => 'Drivers Count',
            'drivers_field_label' => 'Assigned Drivers',
        ],
        'driver' => [
            'information_section_title' => 'Transport Means Driver Information',
            'areas_tab' => 'Areas',
        ],
    ],

    // Transportation
    'transportation' => [
        'area' => [
            'information_section_title' => 'Transportation Area Information',
            'meeting_place' => 'Meeting Place',
            'fare_price' => 'Fare Price',
            'notes' => 'Notes',
            'schedules' => 'Schedules',
            'drivers_tab' => 'Drivers',
            'drivers_count' => 'Drivers Count',
            'drivers_field_label' => 'Assigned Drivers',
        ],
        'driver' => [
            'information_section_title' => 'Transportation Driver Information',
            'areas_tab' => 'Areas',
        ],
        'nav' => [
            'transportation_areas' => 'Transportation Areas',
            'transportation_drivers' => 'Transportation Drivers',
            'transportation_trips' => 'Transportation Trips',
        ],
        'trip' => [
            'information_section_title' => 'Trip Information',
            'from_area' => 'From Area',
            'to_area' => 'To Area',
            'departure_at' => 'Departure',
            'departure_at_format' => 'd/m/Y H:i',
            'available_seats' => 'Available Seats',
            'price_per_seat' => 'Price per Seat',
            'meeting_point' => 'Meeting Point',
            'contact_phone' => 'Contact Phone',
            'contact_whatsapp' => 'WhatsApp',
            'contact_actions' => 'Contact',
            'action_call' => 'Call',
            'action_whatsapp' => 'WhatsApp',
            'status' => 'Status',
            'status_open' => 'Open',
            'status_full' => 'Full',
            'status_closed' => 'Closed',
            'status_expired' => 'Expired',
            'views_count' => 'Views',
            'clicks_count' => 'Clicks',
            'phone_clicks_count' => 'Phone Clicks',
            'whatsapp_clicks_count' => 'WhatsApp Clicks',
            'owner' => 'Owner',
        ],
    ],

    // Ads
    'ads' => [
        'nav' => [
            'ads' => 'Ads',
            'section_info' => 'Ad Information',
        ],
        'title' => 'Title',
        'image' => 'Image',
        'action_type' => 'Action Type',
        'action_value' => 'Action Value',
        'priority' => 'Priority',
        'priority_normal' => 'Normal',
        'priority_featured' => 'Featured',
        'clicks' => 'Clicks',
        'publish_at' => 'Publish At',
        'expires_at' => 'Expires At',
        'created_by' => 'Created By',
        'action_type_shop' => 'Shop',
        'action_type_product' => 'Product',
        'action_type_directory_listing' => 'Directory Listing',
        'action_type_external_url' => 'External URL',
    ],
];
