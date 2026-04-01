<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        DB::table('permissions')->update([
            'name' => DB::raw('`key`'),
        ]);

        DB::table('roles')->update([
            'name' => DB::raw('`name_en`'),
        ]);

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->unique(['name', 'guard_name'], 'permissions_name_guard_name_unique');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }

    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropUnique('permissions_name_guard_name_unique');
            $table->dropColumn('name');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique('roles_name_guard_name_unique');
            $table->dropColumn('name');
        });
    }
};
