<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\SeoKeyword;
use App\Support\TurkishText;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Blog kategorileri + başlangıç rehberleri.
 *
 * Rehberler kelime havuzundaki SORU kalıplarını ("…nereye satabilirim", "…kime
 * satabilirim") karşılar; işlem niyetli kelimeler hizmet sayfalarındadır, yazılar
 * onlarla yarışmaz. Metinlerde istatistik, fiyat, yıl gibi uydurma bilgi yoktur.
 *
 * Var olan yazının üzerine YAZILMAZ (slug ile firstOrCreate): panelden düzenlenen
 * metin korunur. Yazının hedef kelimesi boşta ise o yazıya bağlanır.
 */
class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Toplu Satış Rehberi', 'Elinizdeki ürünleri toplu satarken nelere dikkat etmeli, teklif için hangi bilgiler gerekir.'],
            ['Telefon ve Tablet', 'Telefon ve tabletlerin toplu satışı, hesap kilitleri, veri silme ve fiyatı etkileyen durumlar.'],
            ['Bilgisayar ve IT', 'Laptop, masaüstü, sunucu ve IT ekipmanlarının toplu satışı ve veri güvenliği.'],
            ['Robot Süpürge ve Ev Elektroniği', 'Robot süpürge, dikey süpürge ve küçük ev elektroniğinin toplu satışı.'],
            ['Stok ve Depo Yönetimi', 'Stok fazlası, depo boşaltma, iade ve teşhir ürünlerini değerlendirme yolları.'],
            ['Kurumsal ve Tasfiye', 'Şirket demirbaşları, mağaza kapanışı ve tasfiye süreçlerinde elektronik satışı.'],
        ];

        foreach ($categories as $i => [$name, $description]) {
            BlogCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => $description,
                    'is_active' => true,
                    'auto_generate' => true,
                    'sort_order' => $i + 1,
                ]
            );
        }

        $this->seedPosts();
    }

    private function seedPosts(): void
    {
        $categoryIds = BlogCategory::query()->pluck('id', 'name');

        foreach (glob(__DIR__.'/data/blog_posts_*.php') ?: [] as $file) {
            foreach ((array) require $file as $data) {
                $post = Blog::query()->firstOrCreate(
                    ['slug' => $data['slug']],
                    [
                        'blog_category_id' => $categoryIds[$data['category']] ?? null,
                        'title' => $data['title'],
                        'excerpt' => $data['excerpt'],
                        'body_html' => $data['body_html'],
                        'meta_title' => $data['meta_title'],
                        'meta_description' => $data['meta_description'],
                        'primary_keyword' => $data['primary_keyword'],
                        'faqs' => $data['faqs'],
                        'status' => Blog::STATUS_PUBLISHED,
                        'publish_at' => now(),
                        'source' => Blog::SOURCE_MANUAL,
                    ]
                );

                // Hedef kelime havuzda yoksa bilgi amaçlı olarak eklenir; boştaysa bu
                // yazıya bağlanır (tek sahip kuralı: başka sayfa/yazıya aitse dokunulmaz).
                $keyword = SeoKeyword::query()->firstOrCreate(
                    ['keyword_hash' => md5(TurkishText::lower($data['primary_keyword']))],
                    [
                        'keyword' => $data['primary_keyword'],
                        'search_intent' => 'informational',
                        'keyword_type' => 'BLOG_PRIMARY',
                        'priority' => 1,
                        'status' => true,
                        'note' => 'Başlangıç rehberi',
                    ]
                );

                if (! $keyword->owner_blog_id && ! $keyword->target_id) {
                    $keyword->forceFill(['owner_blog_id' => $post->id, 'assignment_status' => SeoKeyword::ASSIGN_ACTIVE])->save();
                }
            }
        }
    }
}
