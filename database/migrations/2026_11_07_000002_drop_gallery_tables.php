<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

/**
 * Galeri modülü montaj fotoğrafları içindi; toplu alım sitesinde karşılığı yok.
 * Tablolar düşürülür. Geri dönüş: 2026_09_20_000005 migration'ının up() içeriği.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('gallery_categories');
    }

    public function down(): void
    {
        // Bilinçli olarak boş: modül kodu kaldırıldı, tabloyu geri kurmak tek başına anlamsız.
    }
};
