<?php
/**
 * Copyright (c) Since 2024 InnoShop - All Rights Reserved
 *
 * @link       https://www.innoshop.com
 * @author     InnoShop <team@innoshop.com>
 * @license    https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('plugin_code', 64)->nullable()->index();
            $table->string('text')->nullable();
            $table->string('url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('announcement_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id');
            $table->foreign('announcement_id', 'ann_trans_parent_fk')
                ->references('id')->on('announcements')->cascadeOnDelete();
            $table->string('locale', 10)->index('ann_trans_locale_idx');
            $table->string('text');
            $table->index('announcement_id', 'ann_trans_parent_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_translations');
        Schema::dropIfExists('announcements');
    }
};
