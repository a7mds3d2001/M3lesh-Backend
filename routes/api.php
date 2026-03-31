<?php

use App\Http\Controllers\Api\Admin\Auth\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\Auth\DeviceController as AdminDeviceController;
use App\Http\Controllers\Api\Admin\Auth\PermissionController as AdminPermissionController;
use App\Http\Controllers\Api\Admin\Auth\RoleController as AdminRoleController;
use App\Http\Controllers\Api\Admin\ContentPage\ContentPageController as AdminContentPageController;
use App\Http\Controllers\Api\Admin\Notifications\NotificationBroadcastController as AdminNotificationBroadcastController;
use App\Http\Controllers\Api\Admin\Notifications\NotificationController as AdminNotificationController;
use App\Http\Controllers\Api\Admin\SupportTicket\SupportTicketController as AdminSupportTicketController;
use App\Http\Controllers\Api\Admin\User\UserController as AdminUserController;
use App\Http\Controllers\Api\User\Auth\AuthController as UserAuthController;
use App\Http\Controllers\Api\User\Auth\DeviceController as UserDeviceController;
use App\Http\Controllers\Api\User\ContentPage\ContentPageController as UserContentPageController;
use App\Http\Controllers\Api\User\Notifications\NotificationController as UserNotificationController;
use App\Http\Controllers\Api\User\SupportTicket\SupportTicketController as UserSupportTicketController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function (): void {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [AdminAuthController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('devices', [AdminDeviceController::class, 'index']);

        Route::get('roles', [AdminRoleController::class, 'index']);
        Route::post('roles', [AdminRoleController::class, 'store']);
        Route::get('roles/{role}', [AdminRoleController::class, 'show']);
        Route::put('roles/{role}', [AdminRoleController::class, 'update']);
        Route::delete('roles/{role}', [AdminRoleController::class, 'destroy']);

        Route::get('permissions', [AdminPermissionController::class, 'index']);
        Route::get('permissions/{permission}', [AdminPermissionController::class, 'show']);

        Route::get('users', [AdminUserController::class, 'index']);
        Route::post('users', [AdminUserController::class, 'store']);
        Route::get('users/{user}', [AdminUserController::class, 'show']);
        Route::put('users/{user}', [AdminUserController::class, 'update']);
        Route::delete('users/{user}', [AdminUserController::class, 'destroy']);

        Route::get('notifications', [AdminNotificationController::class, 'index']);
        Route::get('notifications/unread-count', [AdminNotificationController::class, 'unreadCount']);
        Route::get('notifications/{notification}', [AdminNotificationController::class, 'show']);
        Route::post('notifications/send', [AdminNotificationController::class, 'send']);
        Route::post('notifications/{notification}/read', [AdminNotificationController::class, 'markRead']);
        Route::post('notifications/read-all', [AdminNotificationController::class, 'readAll']);

        Route::get('notification-broadcasts', [AdminNotificationBroadcastController::class, 'index']);
        Route::get('notification-broadcasts/{broadcast}', [AdminNotificationBroadcastController::class, 'show']);
        Route::post('notification-broadcasts/send', [AdminNotificationBroadcastController::class, 'send']);

        Route::get('support-tickets', [AdminSupportTicketController::class, 'index']);
        Route::post('support-tickets', [AdminSupportTicketController::class, 'store']);
        Route::get('support-tickets/{support_ticket}', [AdminSupportTicketController::class, 'show']);
        Route::put('support-tickets/{support_ticket}', [AdminSupportTicketController::class, 'update']);
        Route::delete('support-tickets/{support_ticket}', [AdminSupportTicketController::class, 'destroy']);
        Route::put('support-tickets/{support_ticket}/status', [AdminSupportTicketController::class, 'updateStatus']);
        Route::put('support-tickets/{support_ticket}/priority', [AdminSupportTicketController::class, 'updatePriority']);
        Route::post('support-tickets/{support_ticket}/logs', [AdminSupportTicketController::class, 'storeLog']);

        Route::prefix('content-pages')->group(function (): void {
            Route::get('/', [AdminContentPageController::class, 'index']);
            Route::post('/', [AdminContentPageController::class, 'store']);
            Route::get('{content_page}', [AdminContentPageController::class, 'show']);
            Route::put('{content_page}', [AdminContentPageController::class, 'update']);
            Route::delete('{content_page}', [AdminContentPageController::class, 'destroy']);
        });
    });
});

Route::prefix('user')->group(function (): void {
    Route::post('register', [UserAuthController::class, 'register']);
    Route::post('login', [UserAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('me', [UserAuthController::class, 'me']);
        Route::put('me', [UserAuthController::class, 'update']);
        Route::delete('me', [UserAuthController::class, 'destroy']);
        Route::post('change-password', [UserAuthController::class, 'changePassword']);
        Route::post('logout', [UserAuthController::class, 'logout']);
        Route::get('devices', [UserDeviceController::class, 'index']);

        Route::get('notifications', [UserNotificationController::class, 'index']);
        Route::get('notifications/unread-count', [UserNotificationController::class, 'unreadCount']);
        Route::get('notifications/{notification}', [UserNotificationController::class, 'show']);
        Route::post('notifications/{notification}/read', [UserNotificationController::class, 'markRead']);
        Route::post('notifications/read-all', [UserNotificationController::class, 'readAll']);

        Route::get('support-tickets', [UserSupportTicketController::class, 'index']);
        Route::get('support-tickets/{support_ticket}', [UserSupportTicketController::class, 'show']);
        Route::post('support-tickets/{support_ticket}/logs', [UserSupportTicketController::class, 'storeLog']);
    });

    Route::post('support-tickets', [UserSupportTicketController::class, 'store'])->middleware('optional.sanctum');

    Route::prefix('content-pages')->group(function (): void {
        Route::get('/', [UserContentPageController::class, 'index']);
        Route::get('{content_page}', [UserContentPageController::class, 'show']);
    });
});

Route::prefix('content-pages')->group(function (): void {
    Route::get('/', [UserContentPageController::class, 'index']);
    Route::get('{content_page}', [UserContentPageController::class, 'show']);
});
