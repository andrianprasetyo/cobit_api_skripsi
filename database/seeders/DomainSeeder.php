<?php

namespace Database\Seeders;

use App\Models\Domain;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$data = [
            [
                'kode' => 'EDM01',
            ],
            [
                'kode' => 'EDM02',
            ],
            [
                'kode' => 'EDM03',
            ],
            [
                'kode' => 'EDM04',
            ],
            [
                'kode' => 'EDM05',
            ],
        ];*/
        $data=[
            [
                'kode'=>'EDM01',
                'ket'=>'Ensured Governance Framework Setting & Maintenance'
            ],
            [
                'kode'=>'EDM02',
                'ket'=>'Ensured Benefits Delivery'
            ],
            [
                'kode'=>'EDM03',
                'ket'=>'Ensured Risk Optimization'
            ],
            [
                'kode'=>'EDM04',
                'ket'=>'Ensured Resource Optimization'
            ],
            [
                'kode'=>'EDM05',
                'ket'=>'Ensured Stakeholder Engagement'
            ],
            [
                'kode'=>'APO01',
                'ket'=>'Managed I&T Management Framework'
            ],
            [
                'kode'=>'APO02',
                'ket'=>'Managed Strategy'
            ],
            [
                'kode'=>'APO03',
                'ket'=>'Managed Enterprise Architecture'
            ],
            [
                'kode'=>'APO04',
                'ket'=>'Managed Innovation'
            ],
            [
                'kode'=>'APO05',
                'ket'=>'Managed Portfolio'
            ],
            [
                'kode'=>'APO06',
                'ket'=>'Managed Budget & Costs'
            ],
            [
                'kode'=>'APO07',
                'ket'=>'Managed Human Resources'
            ],
            [
                'kode'=>'APO08',
                'ket'=>'Managed Relationships'
            ],
            [
                'kode'=>'APO09',
                'ket'=>'Managed Service Agreements'
            ],
            [
                'kode'=>'APO10',
                'ket'=>'Managed Vendors'
            ],
            [
                'kode'=>'APO11',
                'ket'=>'Managed Quality'
            ],
            [
                'kode'=>'APO12',
                'ket'=>'Managed Risk'
            ],
            [
                'kode'=>'APO13',
                'ket'=>'Managed Security'
            ],
            [
                'kode'=>'APO14',
                'ket'=>'Managed Data'
            ],
            [
                'kode'=>'BAI01',
                'ket'=>'Managed Programs'
            ],
            [
                'kode'=>'BAI02',
                'ket'=>'Managed Requirements Definition'
            ],
            [
                'kode'=>'BAI03',
                'ket'=>'Managed Solutions Identification & Build'
            ],
            [
                'kode'=>'BAI04',
                'ket'=>'Managed Availability & Capacity'
            ],
            [
                'kode'=>'BAI05',
                'ket'=>'Managed Organizational Change'
            ],
            [
                'kode'=>'BAI06',
                'ket'=>'Managed IT Changes'
            ],
            [
                'kode'=>'BAI07',
                'ket'=>'Managed IT Change Acceptance and Transitioning'
            ],
            [
                'kode'=>'BAI08',
                'ket'=>'Managed Knowledge'
            ],
            [
                'kode'=>'BAI09',
                'ket'=>'Managed Assets'
            ],
            [
                'kode'=>'BAI10',
                'ket'=>'Managed Configuration'
            ],
            [
                'kode'=>'BAI11',
                'ket'=>'Managed Projects'
            ],
            [
                'kode'=>'DSS01',
                'ket'=>'Managed Operations'
            ],
            [
                'kode'=>'DSS02',
                'ket'=>'Managed Service Requests & Incidents'
            ],
            [
                'kode'=>'DSS03',
                'ket'=>'Managed Problems'
            ],
            [
                'kode'=>'DSS04',
                'ket'=>'Managed Continuity'
            ],
            [
                'kode'=>'DSS05',
                'ket'=>'Managed Security Services'
            ],
            [
                'kode'=>'DSS06',
                'ket'=>'Managed Business Process Controls'
            ],
            [
                'kode'=>'MEA01',
                'ket'=>'Managed Performance and Conformance Monitoring'
            ],
            [
                'kode'=>'MEA02',
                'ket'=>'Managed System of Internal Control'
            ],
            [
                'kode'=>'MEA03',
                'ket'=>'Managed Compliance with External Requirements'
            ],
            [
                'kode'=>'MEA04',
                'ket'=>'Managed Assurance'
            ],
        ];

        foreach ($data as $key=>$item) {
            Domain::create([
                'kode' => $item['kode'],
                'urutan'=>$key
            ]);
        }
    }
}
