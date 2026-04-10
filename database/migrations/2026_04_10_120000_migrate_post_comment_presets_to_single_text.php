<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('post_comment_presets') || ! Schema::hasColumn('post_comment_presets', 'text_ar')) {
            return;
        }

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->string('text', 500)->default('—')->after('id');
        });

        foreach (DB::table('post_comment_presets')->orderBy('id')->get() as $row) {
            $ar = isset($row->text_ar) ? trim((string) $row->text_ar) : '';
            $en = isset($row->text_en) ? trim((string) $row->text_en) : '';
            $merged = $ar !== '' ? $ar : ($en !== '' ? $en : '—');

            DB::table('post_comment_presets')->where('id', $row->id)->update(['text' => $merged]);
        }

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->dropIndex(['is_active', 'sort_order']);
        });

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->dropColumn(['text_ar', 'text_en', 'sort_order']);
        });

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('post_comment_presets') || Schema::hasColumn('post_comment_presets', 'text_ar')) {
            return;
        }

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->string('text_ar')->after('id');
            $table->string('text_en')->after('text_ar');
            $table->unsignedInteger('sort_order')->default(0)->after('text_en');
        });

        DB::table('post_comment_presets')->orderBy('id')->each(function (object $row): void {
            $t = isset($row->text) ? (string) $row->text : '';
            DB::table('post_comment_presets')->where('id', $row->id)->update([
                'text_ar' => $t,
                'text_en' => $t,
                'sort_order' => 0,
            ]);
        });

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->dropColumn('text');
        });

        Schema::table('post_comment_presets', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order']);
        });
    }
};
