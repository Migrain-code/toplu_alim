<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Google dizin durumu adres başına tutulur.
 *
 * Eskiden yalnız seo_analyses satırları (skorlanan ~127 içerik) takip ediliyordu; site
 * haritasındaki il × hizmet sayfaları (~1000 adres) ne kontrol ediliyor ne bildiriliyordu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('index_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500)->unique();
            $table->string('google_index_status', 30)->nullable()->index();
            $table->text('google_index_detail')->nullable();
            $table->timestamp('index_checked_at')->nullable()->index();
            $table->timestamp('index_submitted_at')->nullable()->index();
            $table->unsignedSmallInteger('index_submit_count')->default(0);
            $table->string('index_submit_error', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('index_statuses');
    }
};
