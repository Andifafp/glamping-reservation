<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::where('email', 'staff@demo.test')->value('id');

        $articles = [
            [
                'title' => 'Tips Memilih Tipe Kamar Glamping untuk Pemula',
                'slug' => 'tips-memilih-tipe-kamar-glamping-untuk-pemula',
                'excerpt' => 'Safari tent, kabin, atau dome? Kenali perbedaannya sebelum memesan agar liburan pertama terasa nyaman.',
                'content' => <<<'HTML'
<p>Glamping menggabungkan suasana alam dengan kenyamanan kamar hotel. Bagi yang baru pertama kali, memilih tipe kamar yang tepat akan sangat menentukan pengalaman menginap.</p>
<h2>1. Sesuaikan dengan jumlah tamu</h2>
<p>Periksa kapasitas setiap kamar. Safari tent standard ideal untuk pasangan, sedangkan kabin keluarga cocok untuk rombongan hingga enam orang.</p>
<h2>2. Perhatikan cuaca</h2>
<p>Di musim hujan atau saat suhu malam turun, kabin kayu dengan pemanas ruangan lebih nyaman dibandingkan tenda kanvas.</p>
<h2>3. Pilih pengalaman yang dicari</h2>
<p>Ingin melihat bintang? Pilih dome dengan atap transparan. Ingin suasana petualangan? Safari tent dengan teras menghadap hutan adalah pilihan tepat.</p>
<h2>4. Pertimbangkan asuransi pembatalan</h2>
<p>Jika rencana perjalanan belum pasti, tambahkan asuransi saat reservasi agar refund lebih besar bila harus membatalkan.</p>
HTML,
                'published_at' => now()->subDays(30),
            ],
            [
                'title' => 'Checklist Barang Bawaan Saat Glamping',
                'slug' => 'checklist-barang-bawaan-saat-glamping',
                'excerpt' => 'Fasilitas glamping sudah lengkap, tetapi ada beberapa barang pribadi yang tetap perlu dibawa.',
                'content' => <<<'HTML'
<p>Walaupun kasur, kamar mandi, dan sarapan sudah tersedia, beberapa barang berikut akan membuat menginap lebih nyaman.</p>
<h2>Pakaian</h2>
<ul><li>Jaket atau sweater untuk malam hari</li><li>Pakaian ganti yang mudah kering</li><li>Sandal dan sepatu yang nyaman untuk jalan di area alam</li></ul>
<h2>Perlengkapan pribadi</h2>
<ul><li>Obat-obatan pribadi</li><li>Losion anti nyamuk dan tabir surya</li><li>Botol minum isi ulang</li></ul>
<h2>Lainnya</h2>
<ul><li>Senter atau lampu kepala</li><li>Power bank</li><li>Kartu identitas dan kode booking untuk check-in</li></ul>
HTML,
                'published_at' => now()->subDays(18),
            ],
            [
                'title' => 'Glamping Minimalis: Liburan Nyaman dengan Barang Seperlunya',
                'slug' => 'glamping-minimalis-liburan-nyaman-dengan-barang-seperlunya',
                'excerpt' => 'Datang dengan satu tas saja dan nikmati alam tanpa repot. Begini caranya.',
                'content' => <<<'HTML'
<p>Salah satu kelebihan glamping adalah Anda tidak perlu membawa tenda, matras, atau alat masak. Manfaatkan hal ini untuk liburan yang lebih ringan.</p>
<h2>Bawa satu tas saja</h2>
<p>Pilih pakaian yang bisa dipadupadankan dan bawa secukupnya untuk jumlah malam menginap.</p>
<h2>Kurangi gawai</h2>
<p>Sisihkan waktu tanpa ponsel. Nikmati api unggun, suara alam, dan langit malam.</p>
<h2>Hormati alam</h2>
<p>Bawa kembali sampah pribadi, gunakan air secukupnya, dan jangan memetik tanaman di sekitar area glamping.</p>
HTML,
                'published_at' => now()->subDays(7),
            ],
        ];

        foreach ($articles as $article) {
            Article::updateOrCreate(
                ['slug' => $article['slug']],
                $article + ['author_id' => $authorId, 'is_published' => true]
            );
        }
    }
}
