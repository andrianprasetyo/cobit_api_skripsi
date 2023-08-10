<?php

namespace Database\Seeders;

use App\Models\DesignFaktor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesainFaktorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'kode'=>'DF1',
                'nama' => 'Design Factor 1: Enterprise Strategy (Strategi Perusahaan)',
                'deskripsi'=>'Berdasarkan framework COBIT 2019, terdapat 4 value yang diukur pada area strategi perusahaan, yaitu:
1. Value Growth/Acquisition, strategi perusahaan berfokus pada pertumbuhan revenue.
2. Value Innovation/Differentiation, strategi perusahaan berfokus menawarkan produk dan layanan yang berbeda dan/atau inovatif kepada klien.
3. Value Cost Leadership, strategi perusahaan berfokus pada minimalisasi biaya, terutama dalam jangka pendek.
4. Value Client Service / Stability, strategi perusahaan berfokus pada menyediakan layanan yang stabil dan berorientasi pada klien.'
            ],
            [
                'kode' => 'DF2',
                'nama'=>'Design Factor 2: Enterprise Goals (Tujuan Perusahaan)',
                'deskripsi'=>'Berdasarkan framework COBIT 2019, terdapat 13 tujuan perusahaan yang dapat digunakan untuk merealisasikan strategi perusahaan, yaitu:
1. Perusahaan memiliki portofolio produk dan layanan yang kompetitif
2. Perusahaan mampu mengelola risiko bisnis
3. Perusahaan mematuhi hukum dan peraturan eksternal (perundang-undangan) yang berlaku
4. Perusahaan memiliki laporan informasi keuangan yang berkualitas
5. Perusahaan memiliki budaya layanan yang berorientasi kepada pelanggan
6. Perusahaan mampu untuk menjaga kesinambungan dan ketersediaan layanan bisnis
7. Perusahaan memiliki informasi manajemen yang berkualitas
8. Perusahaan memiliki fungsi proses bisnis internal yang optimal
9. Perusahaan memiliki beban biaya proses bisnis yang optimal
10. Perusahaan memiliki staff dengan keterampilan, motivasi, dan produktivitas yang tinggi
11. Perusahaan mematuhi aturan dan kebijakan internal
12. Perusahaan mampu mengelola program transformasi digital
13. Perusahaan memiliki inovasi produk dan bisnis.'
            ],
            [
                'kode' => 'DF3',
                'nama' => 'Design Factor 3: Risk Profile (Profil Risiko)',
                'deskripsi' => 'Design factor profil risiko merupakan area yang mengidentifikasi jenis risiko terkait I&T yang dihadapi perusahaan saat ini dan menunjukkan area risiko mana yang masih dapat diterima perusahaan terutama dalam rangka pencapaian tujuan perusahaan.'
            ],
            [
                'kode' => 'DF4',
                'nama' => 'Design Factor 4: I&T Related Issues (Isu-Isu Terkait IT)',
                'deskripsi' => 'Design factor ini terkait dengan faktor risiko sebelumnya, hanya difokusnya pada isu-isu yang terkait IT. Penilaian terhadap risiko IT bagi perusahaan perlu mempertimbangkan masalah terkait IT mana yang saat ini dihadapi, atau risiko terkait IT yang telah terjadi'
            ],
            [
                'kode' => 'DF5',
                'nama' => 'Design Factor 5: Threat Landscape',
                'deskripsi' => 'Design factor ini mempertimbangkan lanskap ancaman di mana perusahaan beroperasi yang dapat mempengaruhi tata kelola IT perusahaan. Ancaman dapat berupa kondisi geopolitik ataupun iklim kompetisi.'
            ],
            [
                'kode' => 'DF6',
                'nama' => 'Design Factor 6: Persyaratan Kepatuhan (Compliance Requirements)',
                'deskripsi' => 'Design factor ini mempertimbangkan level dari persyaratan kepatuhan dalam mempengaruhi tata kelola TI perusahaan. Persyaratan kepatuhan adalah dokumen atau serangkaian bukti yang harus disediakan oleh perusahaan untuk menunjukkan bahwa perusahaan dijalankan sesuai dengan aturan dan kebijakan baik internal (KD, PD, dll) dan eksternal (UU ataupun persyaratan Sarbanes Oxley Act) yang berlaku.'
            ],
            [
                'kode' => 'DF7',
                'nama' => 'Design Factor 7: Role of IT (Peran IT Di Perusahaan)',
                'deskripsi' => 'Berdasarkan framework COBIT 2019, 4 peran IT dalam perusahaan dibagi atas: Support, Factory, Turnaround, dan Strategic.
1. Sebagai Support berarti IT tidak terlalu penting dalam memastikan kelancaran dan kelangsungan proses bisnis dan layanan, maupun untuk inovasi.
2. Sebagai Factory berarti IT mempunyai dampak langsung pada jalannya dan kesinambungan pada bisnis proses dan layanan, namun dalam IT tidak berperan dalam upaya berinovasi dalam bisnis proses dan layanan.
3. Sebagai Turnaround berarti IT merupakan pendorong dalam inovasi bisnis proses dan layanan, namun tidak ada ketergantungan terhadap IT dalam memastikan keberlangsungan proses bisnis dan layanan perusahaan.
4. Sebagai Strategic berarti IT berperan penting baik dalam memastikan keberlangsungan dan kelancaran serta inovasi terhadap proses bisnis dan layanan perusahaan.'
            ],
            [
                'kode' => 'DF8',
                'nama' => 'Design Factor 8: Sourcing Model for IT (Model Sumber untuk IT)',
                'deskripsi' => 'Pada design factor ini, model pengadaan IT dipertimbangkan dalam mempengaruhi desain tata Kelola IT perusahaan. Model pengadaan IT yang didefinisikan dalam COBIT 2019 dapat berupa outsourcing, cloud, in-house, atau hybrid (kombinasi dari ketiga model sebelumnya).'
            ],
            [
                'kode' => 'DF9',
                'nama' => 'Design Faktor 9: Metode Implementasi IT',
                'deskripsi' => 'Pada design factor ini, metode implementasi IT dipertimbangkan dalam mempengaruhi desain tata Kelola IT perusahaan. Metode implementasi IT yang didefinisikan dalam COBIT 2019 dapat berupa Agile, DevOps, Traditional (waterfall)'
            ],
            [
                'kode' => 'DF10',
                'nama' => 'DesignFaktor 10: Strategi Adopsi Teknologi',
                'deskripsi' => 'Pada design factor ini, strategi perusahaan dalam mengadopsi teknologi dipertimbangkan dalam mempengaruhi desain tata Kelola TI perusahaan. Strategi adopsi teknologi yang didefinisikan dalam COBIT 2019 yaitu first mover (early adopter), follower, dan slow mover (laggard).'
            ],
        ];

        foreach ($data as $item) {
            DesignFaktor::create([
                'kode' => $item['kode'],
                'nama' => $item['nama'],
                'deskripsi' => $item['deskripsi'],
            ]);
        }
    }
}
