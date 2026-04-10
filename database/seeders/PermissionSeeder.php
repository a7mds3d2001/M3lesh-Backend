<?php

namespace Database\Seeders;

use App\Models\User\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Permissions with localized display names (clear & consistent).
     * key: value used in code/API & DB column; name_ar, name_en: display names.
     */
    protected array $permissions = [
        // ——— Administrators ———
        ['key' => 'view_admins', 'name_ar' => 'عرض قائمة المسؤولين', 'name_en' => 'View administrators list'],
        ['key' => 'create_admins', 'name_ar' => 'إضافة مسؤول جديد', 'name_en' => 'Add new administrator'],
        ['key' => 'edit_admins', 'name_ar' => 'تعديل بيانات مسؤول', 'name_en' => 'Edit administrator'],
        ['key' => 'delete_admins', 'name_ar' => 'حذف مسؤول', 'name_en' => 'Delete administrator'],
        ['key' => 'restore_admins', 'name_ar' => 'استعادة مسؤول محذوف', 'name_en' => 'Restore deleted administrator'],
        ['key' => 'force_delete_admins', 'name_ar' => 'حذف مسؤول نهائياً', 'name_en' => 'Permanently delete administrator'],

        // ——— Roles ———
        ['key' => 'view_roles', 'name_ar' => 'عرض قائمة الأدوار', 'name_en' => 'View roles list'],
        ['key' => 'create_roles', 'name_ar' => 'إضافة دور جديد', 'name_en' => 'Add new role'],
        ['key' => 'edit_roles', 'name_ar' => 'تعديل دور', 'name_en' => 'Edit role'],
        ['key' => 'delete_roles', 'name_ar' => 'حذف دور', 'name_en' => 'Delete role'],

        // ——— Permissions ———
        ['key' => 'view_permissions', 'name_ar' => 'عرض قائمة الصلاحيات', 'name_en' => 'View permissions list'],

        // ——— Dashboard ———
        ['key' => 'view_dashboard', 'name_ar' => 'عرض لوحة التحكم', 'name_en' => 'View dashboard'],

        // ——— Content Pages ———
        ['key' => 'view_content_pages', 'name_ar' => 'عرض صفحات المحتوى', 'name_en' => 'View content pages'],
        ['key' => 'create_content_pages', 'name_ar' => 'إضافة صفحة محتوى جديدة', 'name_en' => 'Add new content page'],
        ['key' => 'edit_content_pages', 'name_ar' => 'تعديل صفحة المحتوى', 'name_en' => 'Edit content page'],
        ['key' => 'delete_content_pages', 'name_ar' => 'حذف صفحة المحتوى', 'name_en' => 'Delete content page'],

        // ——— Users ———
        ['key' => 'view_users', 'name_ar' => 'عرض قائمة المستخدمين', 'name_en' => 'View users list'],
        ['key' => 'create_users', 'name_ar' => 'إضافة مستخدم جديد', 'name_en' => 'Add new user'],
        ['key' => 'edit_users', 'name_ar' => 'تعديل بيانات مستخدم', 'name_en' => 'Edit user'],
        ['key' => 'delete_users', 'name_ar' => 'حذف مستخدم', 'name_en' => 'Delete user'],
        ['key' => 'restore_users', 'name_ar' => 'استعادة مستخدم محذوف', 'name_en' => 'Restore deleted user'],
        ['key' => 'force_delete_users', 'name_ar' => 'حذف مستخدم نهائياً', 'name_en' => 'Permanently delete user'],

        // ——— Posts ———
        ['key' => 'view_posts', 'name_ar' => 'عرض المنشورات', 'name_en' => 'View posts'],
        ['key' => 'create_posts', 'name_ar' => 'إضافة منشور', 'name_en' => 'Create post'],
        ['key' => 'edit_posts', 'name_ar' => 'تعديل المنشورات', 'name_en' => 'Edit posts'],
        ['key' => 'delete_posts', 'name_ar' => 'حذف المنشورات', 'name_en' => 'Delete posts'],
        ['key' => 'restore_posts', 'name_ar' => 'استعادة المنشورات', 'name_en' => 'Restore posts'],
        ['key' => 'force_delete_posts', 'name_ar' => 'حذف منشور نهائياً', 'name_en' => 'Permanently delete posts'],

        // ——— Post comment presets ———
        ['key' => 'view_post_comment_presets', 'name_ar' => 'عرض التعليقات الجاهزة', 'name_en' => 'View comment presets'],
        ['key' => 'create_post_comment_presets', 'name_ar' => 'إضافة تعليق جاهز', 'name_en' => 'Create comment preset'],
        ['key' => 'edit_post_comment_presets', 'name_ar' => 'تعديل تعليق جاهز', 'name_en' => 'Edit comment preset'],
        ['key' => 'delete_post_comment_presets', 'name_ar' => 'حذف تعليق جاهز', 'name_en' => 'Delete comment preset'],
        ['key' => 'restore_post_comment_presets', 'name_ar' => 'استعادة تعليق جاهز', 'name_en' => 'Restore comment preset'],
        ['key' => 'force_delete_post_comment_presets', 'name_ar' => 'حذف تعليق جاهز نهائياً', 'name_en' => 'Permanently delete comment preset'],

        // ——— Support Tickets ———
        ['key' => 'view_support_tickets', 'name_ar' => 'عرض تذاكر الدعم', 'name_en' => 'View support tickets'],
        ['key' => 'create_support_tickets', 'name_ar' => 'إنشاء تذكرة دعم', 'name_en' => 'Create support ticket'],
        ['key' => 'edit_support_tickets', 'name_ar' => 'تعديل تذكرة الدعم', 'name_en' => 'Edit support ticket'],
        ['key' => 'delete_support_tickets', 'name_ar' => 'حذف تذكرة الدعم', 'name_en' => 'Delete support ticket'],
        ['key' => 'restore_support_tickets', 'name_ar' => 'استعادة تذكرة الدعم', 'name_en' => 'Restore support ticket'],
        ['key' => 'force_delete_support_tickets', 'name_ar' => 'حذف نهائي لتذكرة الدعم', 'name_en' => 'Force delete support ticket'],
        ['key' => 'manage_support_ticket_status', 'name_ar' => 'إدارة حالة تذكرة الدعم', 'name_en' => 'Manage support ticket status'],
        ['key' => 'manage_support_ticket_priority', 'name_ar' => 'إدارة أولوية تذكرة الدعم', 'name_en' => 'Manage support ticket priority'],
        ['key' => 'create_support_ticket_logs', 'name_ar' => 'إضافة سجل تذكرة الدعم', 'name_en' => 'Add support ticket log'],
        ['key' => 'delete_support_ticket_logs', 'name_ar' => 'حذف سجل تذكرة الدعم', 'name_en' => 'Delete support ticket log'],
        ['key' => 'restore_support_ticket_logs', 'name_ar' => 'استعادة سجل تذكرة الدعم', 'name_en' => 'Restore support ticket log'],
        ['key' => 'force_delete_support_ticket_logs', 'name_ar' => 'حذف نهائي لسجل تذكرة الدعم', 'name_en' => 'Force delete support ticket log'],

        // ——— Notifications (in-app + push audit) ———
        ['key' => 'view_notifications', 'name_ar' => 'عرض الإشعارات والوارد', 'name_en' => 'View notifications (inbox & log)'],
        ['key' => 'send_notifications', 'name_ar' => 'إرسال إشعارات لمستخدمين أو مسؤولين', 'name_en' => 'Send targeted notifications'],
        ['key' => 'view_notification_broadcasts', 'name_ar' => 'عرض سجل البث الموضوعي', 'name_en' => 'View notification broadcast log'],
        ['key' => 'send_notification_broadcasts', 'name_ar' => 'إرسال بث إشعارات (موضوع/FCM)', 'name_en' => 'Send topic / broadcast notifications'],
    ];

    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $activeKeys = array_column($this->permissions, 'key');

        Permission::query()
            ->where('guard_name', 'admin')
            ->whereNotIn('key', $activeKeys)
            ->delete();

        foreach ($this->permissions as $item) {
            Permission::query()->updateOrCreate(
                ['key' => $item['key'], 'guard_name' => 'admin'],
                ['name' => $item['key'], 'name_ar' => $item['name_ar'], 'name_en' => $item['name_en']],
            );
        }
    }
}
