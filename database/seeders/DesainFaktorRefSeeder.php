<?php

namespace Database\Seeders;

use App\Models\DesignFaktor;
use App\Models\DesignFaktorKomponen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DesainFaktorRefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama' => 'Growth/Acquisition',
                'deskripsi'=>'Growth/Acquisition',
                'baseline'=>3,
                'design_faktor_id'=>DesignFaktor::where('kode','DF1')->first()->id,
            ],
            [
                'nama' => 'Innovation/Differentiation',
                'deskripsi'=>'Innovation/Differentiation',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Cost Leadership',
                'deskripsi'=>'Cost Leadership',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'Client Service/Stability',
                'deskripsi'=>'Client Service/Stability',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF1')->first()->id,
            ],
            [
                'nama' => 'EG01—Portfolio of competitive products and services',
                'deskripsi'=>'Perusahaan memiliki portofolio produk dan layanan yang kompetitif',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG02—Managed business risk',
                'deskripsi'=>'Perusahaan mampu mengelola risiko bisnis',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG03—Compliance with external laws and regulations',
                'deskripsi'=>'Perusahaan mematuhi hukum dan peraturan eksternal (perundang-undangan) yang berlaku',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG04—Quality of financial information',
                'deskripsi'=>'Perusahaan memiliki laporan informasi keuangan yang berkualitas',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG05—Customer-oriented service culture',
                'deskripsi'=>'Perusahaan memiliki budaya layanan yang berorientasi kepada pelanggan',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG06—Business-service continuity and availability',
                'deskripsi'=>'Perusahaan mampu untuk menjaga kesinambungan dan ketersediaan layanan bisnis',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG07—Quality of management information',
                'deskripsi'=>'Perusahaan memiliki informasi manajemen yang berkualitas',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG08—Optimization of internal business process functionality',
                'deskripsi'=>'Perusahaan memiliki fungsi proses bisnis internal yang optimal',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG09—Optimization of business process costs',
                'deskripsi'=>'Perusahaan memiliki beban biaya proses bisnis yang optimal',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG10—Staff skills, motivation and productivity',
                'deskripsi'=>'Perusahaan memiliki staff dengan keterampilan, motivasi, dan produktivitas yang tinggi',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG11—Compliance with internal policies',
                'deskripsi'=>'Perusahaan mematuhi aturan dan kebijakan internal',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG12—Managed digital transformation programs',
                'deskripsi'=>'Perusahaan mampu mengelola program transformasi digital',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            [
                'nama' => 'EG13—Product and business innovation',
                'deskripsi'=>'Perusahaan memiliki inovasi produk dan bisnis.',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF2')->first()->id,
            ],
            //DF 3
            [
                'nama' => 'IT investment decision making, portfolio definition & maintenance',
                'deskripsi'=>'Dampak risiko dalam pengambilan keputusan terkait investasi TI, penentuan portofolio TI, dan pemeliharaan TI (contoh: kesalahan dalam memilih software, kegagalan investasi TI untuk mendukung strategi digital, ketidaksesuaian investasi TI dengan prioritas bisnis dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Program & projects life cycle management',
                'deskripsi'=>'Dampak risiko terkait pengelolaan siklus proyek dan program (misalnya: pembengkakan anggaran proyek TI, keterlambatan proyek TI dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'IT cost & oversight',
                'deskripsi'=>'Dampak risiko terkait beban, biaya dan pengawasan TI (misalnya: kekurangan anggaran investasi TI, persyaratan yang tidak memadai, kegagalan SLA, atau kelebihan biaya dalam proses pengadaan TI, dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'IT expertise, skills & behavior',
                'deskripsi'=>'Dampak risiko terkait keahlian TI, skill TI, dan perilaku karyawan (contoh: ketergantungan terhadap karyawan tertentu, kekurangan pelatihan TI, ketidakmampuan untuk merekrut atau mempertahankan talenta digital, dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Enterprise/IT architecture ',
                'deskripsi'=>'Dampak risiko terkait Enterprise/IT architecture (contoh: inefisiensi atau duplikasi aplikasi TI karena tidak adanya arsitektur TI perusahaan, kegagalan dalam mengadopsi dan mengeksploitasi teknologi baru, kegagalan dalam menyingkirkan aplikasi yang telah obsolete dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'IT operational infrastructure incidents',
                'deskripsi'=>'Dampak risiko terkait insiden dalam operasional infrastruktur TI (contoh: error pada konfigurasi hardware, kegagalan dalam proses back-up, kecelakaan yang menyebabkan rusaknya peralatan TI, dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Unauthorized actions',
                'deskripsi'=>'Dampak risiko terkait tindakan-tindakan yang tidak terotorisasi (contoh: manipulasi software yang mengakibatkan kesalahan data dan tindakan penipuan, akses ke data dan aplikasi yang tidak sesuai penugasan dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Software adoption/usage problems',
                'deskripsi'=>'Dampak risiko terkait masalah dalam adopsi dan penggunaan software (contoh: tidak ada pengguna software baru, software baru tidak membantu pengerjaan tugas operasional dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Hardware incidents',
                'deskripsi'=>'Dampak risiko terkait insiden hardware (contoh: kegagalan sistem menangani kenaikan volume trafik transaksi, kerusakan akibat masalah kelistrikan, kerusakan karena overheat dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Software failures',
                'deskripsi'=>'Dampak risiko terkait kerusakan software (contoh: glitches, bugs, software malfunction, kegagalan untuk roll-back ke versi sebelumnya, obsolete, dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Logical attacks (hacking, malware, etc.)',
                'deskripsi'=>'Dampak risiko terkait serangan siber (contoh: hacking, malware, industrial espionage, DDOS attack dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Third-party/supplier incidents',
                'deskripsi'=>'Dampak risiko terkait insiden dari pihak ketiga, vendor, atau supplier (contoh: kurangnya support, keterlambatan delivery layanan, ketidakpatuhan terhadap SLA, ketergantungan terhadap vendor tertentu, atau gugatan hak paten dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Noncompliance',
                'deskripsi'=>'Dampak risiko terkait ketidakpatuhan terhadap aturan (contoh: kendala operasional yang disebabkan oleh regulasi baru, kurangnya kesadaran terhadap regulasi baru, temuan audit, gugatan hukum dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Geopolitical Issues',
                'deskripsi'=>'Dampak risiko terkait geopolitik (contoh: perang dagang, adanya campur tangan pemerintah dalam mekanisme pasar, insiden di cabang luar negeri dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Industrial action',
                'deskripsi'=>'Dampak risiko terkait area atau domain industri (contoh: pemogokan serikat kerja, isu pembebasan lahan, merger akuisisi oleh kompetitor bisnis, masuknya pemain global, dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Acts of nature',
                'deskripsi'=>'Dampak risiko terkait bencana Alam (contoh: banjir, gempa bumi, gunung meletus yang berdampak pada infrastruktur penting dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Technology-based innovation ',
                'deskripsi'=>'Dampak risiko terkait inovasi teknologi (contoh: kegagalan untuk mengidentifikasi, mengadopsi, atau mengeksplorasi teknologi baru secara tepat waktu, kegagalan untuk menyediakan teknologi dalam mendukung model bisnis baru dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Environmental',
                'deskripsi'=>'Dampak risiko terkait lingkungan Hidup (contoh: konsumsi energi yang menggunakan bahan bakar fosil, penggunaan plastik yang berlebihan dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            [
                'nama' => 'Data & information management',
                'deskripsi'=>'Dampak risiko terkait pengelolaan data dan informasi (contoh: rendahnya kualitas data, kebocoran data pelanggan, kebocoran data penting perusahaan, penggunaan sosial media untuk transfer data perusahaan dan sebagainya)',
                'baseline'=>9,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF3')->first()->id,
            ],
            // DF4
            [
                'nama' => 'Frustration between different IT entities across the organization because of a perception of low contribution to business value',
                'deskripsi'=>'Frustrasi/kegagalan antara entitas TI yang berbeda di seluruh organisasi karena persepsi kontribusi yang rendah terhadap nilai bisnis',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Frustration between business departments (i.e., the IT customer) and the IT department because of failed initiatives or a perception of low contribution to business value',
                'deskripsi'=>'Frustrasi/kegagalan antara departemen bisnis (yaitu, pelanggan TI) dan departemen TI karena inisiatif yang gagal atau persepsi kontribusi yang rendah terhadap nilai bisnis',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Significant IT-related incidents, such as data loss, security breaches, project failure and application errors, linked to IT',
                'deskripsi'=>'Insiden signifikan terkait I&T, seperti kehilangan data, pelanggaran keamanan, kegagalan proyek, dan kesalahan aplikasi, terkait dengan TI',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Service delivery problems by the IT outsourcer(s) ',
                'deskripsi'=>'Masalah delivery layanan oleh pihak ketiga, vendor, dan supplier',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Failures to meet IT-related regulatory or contractual requirements ',
                'deskripsi'=>'Kegagalan untuk memenuhi persyaratan peraturan atau kontrak terkait TI',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Regular audit findings or other assessment reports about poor IT performance or reported IT quality or service problems',
                'deskripsi'=>'Temuan audit reguler atau laporan penilaian lainnya tentang kinerja TI yang buruk atau masalah kualitas atau layanan TI yang berdasarkan laporan assessment atau temuan audit',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Substantial hidden and rogue IT spending, that is, IT spending by user departments outside the control of the normal IT investment decision mechanisms and approved budgets',
                'deskripsi'=>'Pengeluaran atau biaya TI yang tersembunyi berupa pengeluaran I&T oleh departemen pengguna di luar kendali mekanisme keputusan investasi I&T normal dan anggaran yang disetujui',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Duplications or overlaps between various initiatives, or other forms of wasted resources',
                'deskripsi'=>'Duplikasi atau tumpang tindih antara berbagai inisiatif, atau bentuk lain dari sumber daya yang terbuang',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Insufficient IT resources, staff with inadequate skills or staff burnout/dissatisfaction',
                'deskripsi'=>'Kurangnya sumber daya TI, karyawan dengan skill yang tidak memadai, atau isu-isu lain berkaitan dengan sumber daya manusia',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'IT-enabled changes or projects frequently failing to meet business needs and delivered late or over budget',
                'deskripsi'=>'Inisiatif perubahan atau proyek yang didorong oleh TI sering gagal memenuhi kebutuhan bisnis, terlambat, atau melebihi anggaran',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Reluctance by board members, executives or senior management to engage with IT, or a lack of committed business sponsorship for IT',
                'deskripsi'=>'Keengganan board member, eksekutif, atau top-level management untuk terlibat dengan TI, atau kurangnya sponsor bisnis yang berkomitmen terhadap TI',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Complex IT operating model and/or unclear decision mechanisms for IT-related decisions',
                'deskripsi'=>'Model operasi TI yang rumit dan / atau mekanisme pengambilan keputusan terkait TI yang tidak jelas',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Excessively high cost of IT',
                'deskripsi'=>'Beban biaya TI yang terlalu tinggi',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Obstructed or failed implementation of new initiatives or innovations caused by the current IT architecture and systems',
                'deskripsi'=>'Implementasi inisiatif atau inovasi baru yang terhambat atau gagal yang disebabkan oleh arsitektur dan sistem TI saat ini',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Gap between business and technical knowledge, which leads to business users and information and/or technology specialists speaking different languages',
                'deskripsi'=>'Kesenjangan pengetahuan antara entitas bisnis dan entitas TI di perusahaan yang menyebabkan kegagalan komunikasi, terutama dalam penyampaian dan pemahaman kebutuhan dan usulan solusi',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Regular issues with data quality and integration of data across various sources',
                'deskripsi'=>'Masalah kualitas data dan integrasi data yang berasal dari berbagai sumber di perusahaan',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'High level of end-user computing, creating (among other problems) a lack of oversight and quality control over the applications that are being developed and put in operation',
                'deskripsi'=>'High level of end-user computing, creating (among other problems) a lack of oversight and quality control over the applications that are being developed and put in operation',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Business departments implementing their own information solutions with little or no involvement of the enterprise IT department (related to end-user computing, which often stems from dissatisfaction with IT solutions and services)',
                'deskripsi'=>'Departemen bisnis menerapkan solusi informasi mereka sendiri dengan sedikit atau tanpa keterlibatan departemen TI perusahaan (terkait dengan komputasi pengguna akhir, yang sering kali berasal dari ketidakpuasan dengan solusi dan layanan TI)',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Ignorance of and/or noncompliance with privacy regulations',
                'deskripsi'=>'Ketidakpedulian atau ketidakpatuhan terhadap aturan, terutama aturan perlindungan data pribadi',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            [
                'nama' => 'Inability to exploit new technologies or innovate using I&T',
                'deskripsi'=>'Ketidakmampuan untuk mengeksploitasi teknologi baru atau berinovasi menggunakan teknologi dan informasi',
                'baseline'=>2,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF4')->first()->id,
            ],
            // DF5
            [
                'nama' => 'High',
                'deskripsi'=>'High',
                'baseline'=>33,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF5')->first()->id,
            ],
            [
                'nama' => 'Normal',
                'deskripsi'=>'Normal',
                'baseline'=>67,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF5')->first()->id,
            ],
            //DF6
            [
                'nama' => 'High',
                'deskripsi'=>'High',
                'baseline'=>0,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF6')->first()->id,
            ],
            [
                'nama' => 'Normal',
                'deskripsi'=>'Normal',
                'baseline'=>100,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF6')->first()->id,
            ],
            [
                'nama' => 'Low',
                'deskripsi'=>'Low',
                'baseline'=>0,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF6')->first()->id,
            ],
            //DF7
            [
                'nama' => 'Support',
                'deskripsi'=>'Sebagai Support',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF7')->first()->id,
            ],
            [
                'nama' => 'Factory',
                'deskripsi'=>'Sebagai Factory',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF7')->first()->id,
            ],
            [
                'nama' => 'Turnaround',
                'deskripsi'=>'Sebagai Turnaround',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF7')->first()->id,
            ],
            [
                'nama' => 'Strategic',
                'deskripsi'=>'Sebagai Strategic',
                'baseline'=>3,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF7')->first()->id,
            ],
            //DF8
            [
                'nama' => 'Outsourcing',
                'deskripsi'=>'Outsourcing',
                'baseline'=>33,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF8')->first()->id,
            ],
            [
                'nama' => 'Cloud',
                'deskripsi'=>'Cloud',
                'baseline'=>33,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF8')->first()->id,
            ],
            [
                'nama' => 'Insourced',
                'deskripsi'=>'Insourced',
                'baseline'=>34,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF8')->first()->id,
            ],
            //DF9
            [
                'nama' => 'Agile',
                'deskripsi'=>'Agile',
                'baseline'=>15,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF9')->first()->id,
            ],
            [
                'nama' => 'DevOps',
                'deskripsi'=>'DevOps',
                'baseline'=>10,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF9')->first()->id,
            ],
            [
                'nama' => 'Traditional',
                'deskripsi'=>'Traditional (Waterfall)',
                'baseline'=>75,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF9')->first()->id,
            ],
            //DF10
            [
                'nama' => 'First mover',
                'deskripsi'=>'First mover',
                'baseline'=>15,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF10')->first()->id,
            ],
            [
                'nama' => 'Follower',
                'deskripsi'=>'Follower',
                'baseline'=>70,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF10')->first()->id,
            ],
            [
                'nama' => 'Slow adopter',
                'deskripsi'=>'Slow adopter',
                'baseline'=>15,
                'design_faktor_id' => DesignFaktor::where('kode', 'DF10')->first()->id,
            ],
        ];

        foreach ($data as $key=>$item) {
            DesignFaktorKomponen::create([
                'nama' => $item['nama'],
                'deskripsi'=>$item['deskripsi'],
                'baseline'=>$item['baseline'],
                'design_faktor_id' => $item['design_faktor_id'],
                'urutan'=>$key
            ]);
        }
    }
}
