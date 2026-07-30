<?php

namespace Database\Seeders;

use App\Models\Regency;
use App\Models\District;
use App\Models\Village;
use Illuminate\Database\Seeder;

class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $regency = Regency::create([
            'kode' => '32.72',
            'nama' => 'Kota Sukabumi',
            'is_active' => true,
        ]);

        $districts = [
            ['kode' => '32.72.01', 'nama' => 'Baros'],
            ['kode' => '32.72.02', 'nama' => 'Lembursitu'],
            ['kode' => '32.72.03', 'nama' => 'Cikole'],
            ['kode' => '32.72.04', 'nama' => 'Citamiang'],
            ['kode' => '32.72.05', 'nama' => 'Warudoyong'],
            ['kode' => '32.72.06', 'nama' => 'Gunungpuyuh'],
            ['kode' => '32.72.07', 'nama' => 'Cibeureum'],
        ];

        $villagesByDistrict = [
            'Baros' => [
                ['kode' => '32.72.01.1001', 'nama' => 'Baros'],
                ['kode' => '32.72.01.1002', 'nama' => 'Jayamekar'],
                ['kode' => '32.72.01.1003', 'nama' => 'Jayaraksa'],
                ['kode' => '32.72.01.1004', 'nama' => 'Sudajaya Hilir'],
            ],
            'Lembursitu' => [
                ['kode' => '32.72.02.1001', 'nama' => 'Lembursitu'],
                ['kode' => '32.72.02.1002', 'nama' => 'Cikundul'],
                ['kode' => '32.72.02.1003', 'nama' => 'Sindangsari'],
                ['kode' => '32.72.02.1004', 'nama' => 'Situmekar'],
            ],
            'Cikole' => [
                ['kode' => '32.72.03.1001', 'nama' => 'Cikole'],
                ['kode' => '32.72.03.1002', 'nama' => 'Cisarua'],
                ['kode' => '32.72.03.1003', 'nama' => 'Kebonjati'],
                ['kode' => '32.72.03.1004', 'nama' => 'Selabatu'],
                ['kode' => '32.72.03.1005', 'nama' => 'Subangjaya'],
            ],
            'Citamiang' => [
                ['kode' => '32.72.04.1001', 'nama' => 'Citamiang'],
                ['kode' => '32.72.04.1002', 'nama' => 'Cikondang'],
                ['kode' => '32.72.04.1003', 'nama' => 'Gedongpanjang'],
                ['kode' => '32.72.04.1004', 'nama' => 'Nanggeleng'],
                ['kode' => '32.72.04.1005', 'nama' => 'Tipar'],
            ],
            'Warudoyong' => [
                ['kode' => '32.72.05.1001', 'nama' => 'Warudoyong'],
                ['kode' => '32.72.05.1002', 'nama' => 'Benteng'],
                ['kode' => '32.72.05.1003', 'nama' => 'Dayeuhluhur'],
                ['kode' => '32.72.05.1004', 'nama' => 'Nyomplong'],
                ['kode' => '32.72.05.1005', 'nama' => 'Sukakarya'],
            ],
            'Gunungpuyuh' => [
                ['kode' => '32.72.06.1001', 'nama' => 'Gunungpuyuh'],
                ['kode' => '32.72.06.1002', 'nama' => 'Karamat'],
                ['kode' => '32.72.06.1003', 'nama' => 'Karang Tengah'],
                ['kode' => '32.72.06.1004', 'nama' => 'Sriwidari'],
            ],
            'Cibeureum' => [
                ['kode' => '32.72.07.1001', 'nama' => 'Cibeureum'],
                ['kode' => '32.72.07.1002', 'nama' => 'Babakan'],
                ['kode' => '32.72.07.1003', 'nama' => 'Cibeureum Hilir'],
                ['kode' => '32.72.07.1004', 'nama' => 'Limusnunggal'],
                ['kode' => '32.72.07.1005', 'nama' => 'Sindangpalay'],
            ],
        ];

        foreach ($districts as $d) {
            $district = District::create([
                'regency_id' => $regency->id,
                'kode' => $d['kode'],
                'nama' => $d['nama'],
            ]);

            foreach ($villagesByDistrict[$d['nama']] as $v) {
                Village::create([
                    'district_id' => $district->id,
                    'kode' => $v['kode'],
                    'nama' => $v['nama'],
                ]);
            }
        }
    }
}
