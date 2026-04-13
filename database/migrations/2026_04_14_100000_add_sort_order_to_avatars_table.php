<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avatars', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('image');
        });

        $ids = DB::table('avatars')->orderBy('id')->pluck('id');
        foreach ($ids as $index => $id) {
            DB::table('avatars')->where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    public function down(): void
    {
        Schema::table('avatars', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
