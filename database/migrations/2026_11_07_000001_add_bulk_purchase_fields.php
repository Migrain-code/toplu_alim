<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Toplu elektronik alımına geçiş:
 *  - services.group: "category" (telefon, tablet…) veya "condition" (stok fazlası, arızalı…).
 *    İl × hizmet sayfaları yalnız kategori hizmetleri için üretilir.
 *  - quote_requests: alım talebinin özü — ürün türü, yaklaşık adet, ürün durumu.
 *  - brands.category_service_id: markanın ait olduğu ürün grubu (Apple → telefon).
 *    Mevcut service_id'den AYRI: service_id dolu marka kendi sayfasını kaybeder
 *    (Brand::hasOwnPage); bu kolon yalnız ilişkilendirme/iç link içindir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('group', 20)->default('category')->index()->after('slug');
        });

        Schema::table('quote_requests', function (Blueprint $table) {
            $table->string('product_type', 120)->nullable()->after('service_id');
            $table->string('quantity', 60)->nullable()->after('product_type');
            $table->string('condition', 40)->nullable()->after('quantity');
        });

        Schema::table('brands', function (Blueprint $table) {
            $table->foreignId('category_service_id')->nullable()->after('service_id')->constrained('services')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_service_id');
        });

        Schema::table('quote_requests', function (Blueprint $table) {
            $table->dropColumn(['product_type', 'quantity', 'condition']);
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->dropColumn('group');
        });
    }
};
