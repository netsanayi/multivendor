<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Blogs\Models\Blog;
use App\Modules\Users\Models\User;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin ve editor kullanıcılarını al
        $authors = User::role(['admin', 'editor'])->get();
        
        if ($authors->isEmpty()) {
            $this->command->warn('Blog yazarı bulunamadı. Önce kullanıcıları oluşturun.');
            return;
        }
        
        $blogs = [
            [
                'title' => 'E-Ticaret Trendleri 2025',
                'description' => '<h2>2025 Yılında E-Ticaret Dünyasını Şekillendirecek Trendler</h2>
                    <p>E-ticaret dünyası sürekli evrim geçiriyor ve 2025 yılı da bu değişimin hızlandığı bir yıl olacak. İşte dikkat etmeniz gereken başlıca trendler:</p>
                    <h3>1. Yapay Zeka ve Kişiselleştirme</h3>
                    <p>Yapay zeka destekli öneri sistemleri, müşteri deneyimini daha da kişiselleştirecek. Alışveriş alışkanlıklarına göre özel teklifler ve ürün önerileri sunulacak.</p>
                    <h3>2. Sosyal Medya Ticareti</h3>
                    <p>Instagram, TikTok ve diğer sosyal medya platformları üzerinden doğrudan satış yapmak daha da yaygınlaşacak.</p>
                    <h3>3. Sürdürülebilir Alışveriş</h3>
                    <p>Çevre dostu ürünler ve sürdürülebilir paketleme çözümleri müşterilerin tercihi olacak.</p>',
                'meta_title' => 'E-Ticaret Trendleri 2025 - En Güncel Bilgiler',
                'meta_description' => '2025 yılında e-ticaret sektörünü şekillendirecek trendler ve yenilikler hakkında detaylı bilgi.',
                'meta_keywords' => 'e-ticaret, trendler, 2025, yapay zeka, sosyal medya ticareti',
                'view_count' => rand(100, 1000),
                'status' => true,
                'published_at' => now()->subDays(rand(1, 30)),
            ],
            [
                'title' => 'Online Alışverişte Güvenlik İpuçları',
                'description' => '<h2>Güvenli Online Alışveriş İçin 10 Altın Kural</h2>
                    <p>İnternet üzerinden alışveriş yaparken güvenliğinizi sağlamak için dikkat etmeniz gereken önemli noktalar:</p>
                    <ol>
                        <li><strong>Güvenilir Sitelerden Alışveriş Yapın:</strong> SSL sertifikası olan ve tanınmış sitelerden alışveriş yapın.</li>
                        <li><strong>Güçlü Şifreler Kullanın:</strong> Her site için farklı ve güçlü şifreler oluşturun.</li>
                        <li><strong>Kişisel Bilgilerinizi Koruyun:</strong> Gereksiz bilgi paylaşmaktan kaçının.</li>
                        <li><strong>Güvenli Ödeme Yöntemlerini Tercih Edin:</strong> 3D Secure özellikli kartlar kullanın.</li>
                        <li><strong>Şüpheli E-postalara Dikkat Edin:</strong> Phishing saldırılarına karşı dikkatli olun.</li>
                    </ol>',
                'meta_title' => 'Online Alışveriş Güvenliği - 10 Önemli İpucu',
                'meta_description' => 'İnternet üzerinden güvenli alışveriş yapmak için bilmeniz gereken tüm detaylar.',
                'meta_keywords' => 'online alışveriş, güvenlik, e-ticaret güvenliği, güvenli ödeme',
                'view_count' => rand(200, 1500),
                'status' => true,
                'published_at' => now()->subDays(rand(5, 15)),
            ],
            [
                'title' => 'Kış Modası 2025: En Trend Parçalar',
                'description' => '<h2>Bu Kışın Olmazsa Olmaz Parçaları</h2>
                    <p>2025 kış sezonunda gardırobunuzda bulunması gereken trend parçaları sizin için derledik.</p>
                    <h3>Oversize Paltolar</h3>
                    <p>Rahat ve şık görünümüyle oversize paltolar bu sezonun favorisi. Özellikle karamel ve bej tonları öne çıkıyor.</p>
                    <h3>Triko Elbiseler</h3>
                    <p>Hem rahat hem şık triko elbiseler, botlarla kombinlendiğinde mükemmel bir görünüm sunuyor.</p>
                    <h3>Deri Detaylar</h3>
                    <p>Deri ceketler, pantolonlar ve aksesuarlar kış gardırobunun vazgeçilmezi olmaya devam ediyor.</p>',
                'meta_title' => 'Kış Modası 2025 - En Trend Parçalar',
                'meta_description' => '2025 kış sezonunun en trend parçaları ve kombinleri hakkında öneriler.',
                'meta_keywords' => 'kış modası, 2025 trendleri, moda, kış kombinleri',
                'view_count' => rand(300, 2000),
                'status' => true,
                'published_at' => now()->subDays(rand(2, 10)),
            ],
            [
                'title' => 'Cilt Bakımında Doğru Bilinen Yanlışlar',
                'description' => '<h2>Cilt Bakımı Hakkında Yanlış Bilinen 7 Şey</h2>
                    <p>Cilt bakımı konusunda doğru bilinen birçok yanlış bulunuyor. İşte en yaygın yanlış inanışlar:</p>
                    <h3>1. Yağlı Ciltler Nemlendirici Kullanmamalı</h3>
                    <p>YANLIŞ! Yağlı ciltler de nem dengesini korumak için hafif, yağsız nemlendiriciler kullanmalıdır.</p>
                    <h3>2. SPF Sadece Yazın Kullanılır</h3>
                    <p>YANLIŞ! Güneş koruyucu tüm yıl boyunca, bulutlu havalarda bile kullanılmalıdır.</p>
                    <h3>3. Pahalı Ürünler Daha Etkilidir</h3>
                    <p>YANLIŞ! Ürünün etkinliği fiyatıyla değil, içeriğiyle ilgilidir.</p>',
                'meta_title' => 'Cilt Bakımı Yanlışları - Doğruları Öğrenin',
                'meta_description' => 'Cilt bakımı hakkında doğru bilinen yanlışlar ve gerçekler.',
                'meta_keywords' => 'cilt bakımı, güzellik, yanlış bilgiler, doğru cilt bakımı',
                'view_count' => rand(500, 2500),
                'status' => true,
                'published_at' => now()->subDays(rand(7, 20)),
            ],
            [
                'title' => 'Sürdürülebilir Yaşam İçin 10 Öneri',
                'description' => '<h2>Çevre Dostu Yaşam İçin Basit Adımlar</h2>
                    <p>Sürdürülebilir bir yaşam tarzı benimsemek düşündüğünüz kadar zor değil. İşte başlayabileceğiniz 10 basit adım:</p>
                    <ol>
                        <li>Tek kullanımlık plastik ürünlerden kaçının</li>
                        <li>Alışverişlerinizde bez çanta kullanın</li>
                        <li>Su tüketiminizi azaltın</li>
                        <li>Geri dönüşüme önem verin</li>
                        <li>Yerel ve mevsiminde ürünler tüketin</li>
                        <li>Enerji tasarruflu cihazlar kullanın</li>
                        <li>Toplu taşıma araçlarını tercih edin</li>
                        <li>İkinci el ürünlere şans verin</li>
                        <li>Kompost yapın</li>
                        <li>Minimalist yaşam tarzını benimseyin</li>
                    </ol>',
                'meta_title' => 'Sürdürülebilir Yaşam Rehberi - 10 Pratik Öneri',
                'meta_description' => 'Çevre dostu ve sürdürülebilir bir yaşam için uygulanabilir öneriler.',
                'meta_keywords' => 'sürdürülebilir yaşam, çevre dostu, geri dönüşüm, minimalizm',
                'view_count' => rand(100, 800),
                'status' => true,
                'published_at' => now()->subDays(rand(3, 12)),
            ],
            [
                'title' => 'Taslak Blog Yazısı',
                'description' => '<p>Bu yazı henüz tamamlanmamıştır...</p>',
                'meta_title' => 'Taslak',
                'view_count' => 0,
                'status' => true,
                'published_at' => null, // Yayınlanmamış
            ],
        ];
        
        foreach ($blogs as $blogData) {
            $blogData['author_id'] = $authors->random()->id;
            Blog::create($blogData);
        }
        
        $this->command->info('Blog yazıları oluşturuldu.');
    }
}
