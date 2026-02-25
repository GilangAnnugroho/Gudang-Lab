<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemMaster;

class ItemMasterSeeder extends Seeder
{
    public function run(): void
    {
        $items = [

            // ======================
            // 1) REAGEN (category_id = 1)
            // ======================
            [
                'item_code'     => 'REG-001',
                'item_name'     => 'Reagen Glukosa',
                'category_id'   => 1,
                'base_unit'     => 'btl',
                'warning_stock' => 3,
                'storage_temp'  => '2–8°C',
                'size'          => '4 x 50 mL',
                'warnings'      => 'Simpan pada suhu 2–8°C. Jangan dibekukan. Lindungi dari cahaya langsung dan jangan digunakan setelah melewati tanggal kedaluwarsa.',
            ],
            [
                'item_code'     => 'REG-002',
                'item_name'     => 'Reagen Kolesterol',
                'category_id'   => 1,
                'base_unit'     => 'btl',
                'warning_stock' => 2,
                'storage_temp'  => '2–8°C',
                'size'          => '4 x 50 mL',
                'warnings'      => 'Simpan 2–8°C, botol harus tertutup rapat. Jangan dibekukan dan hindari paparan panas berlebih.',
            ],
            [
                'item_code'     => 'REG-003',
                'item_name'     => 'Buffer pH 7.00',
                'category_id'   => 1,
                'base_unit'     => 'btl',
                'warning_stock' => 1,
                'storage_temp'  => 'Room Temp',
                'size'          => '500 mL',
                'warnings'      => 'Simpan pada suhu ruang yang stabil. Hindari kontaminasi silang dan tutup botol segera setelah digunakan.',
            ],
            [
                'item_code'     => 'REG-004',
                'item_name'     => 'Reagen Hematologi',
                'category_id'   => 1,
                'base_unit'     => 'btl',
                'warning_stock' => 4,
                'storage_temp'  => '2–8°C',
                'size'          => '20 L (bulk)',
                'warnings'      => 'Simpan 2–8°C. Kocok sebelum digunakan jika diperlukan. Jangan digunakan bila warna berubah atau terdapat presipitasi.',
            ],
            [
                'item_code'     => 'REG-005',
                'item_name'     => 'Reagen Urinalisa',
                'category_id'   => 1,
                'base_unit'     => 'btl',
                'warning_stock' => 2,
                'storage_temp'  => 'Room Temp',
                'size'          => '100 strip / botol',
                'warnings'      => 'Simpan pada suhu ruang kering, tutup botol rapat. Jangan menyentuh area reagen dengan tangan basah.',
            ],

            // ======================
            // 2) BHP (category_id = 2)
            // ======================
            [
                'item_code'     => 'BHP-001',
                'item_name'     => 'Sarung Tangan Latex',
                'category_id'   => 2,
                'base_unit'     => 'box',
                'warning_stock' => 5,
                'storage_temp'  => 'Room Temp',
                'size'          => '100 pcs / box',
                'warnings'      => 'Simpan di tempat sejuk dan kering. Lindungi dari sinar matahari langsung. Produk sekali pakai, jangan digunakan ulang.',
            ],
            [
                'item_code'     => 'BHP-002',
                'item_name'     => 'Masker Bedah 3 Ply',
                'category_id'   => 2,
                'base_unit'     => 'box',
                'warning_stock' => 10,
                'storage_temp'  => 'Room Temp',
                'size'          => '50 pcs / box',
                'warnings'      => 'Simpan di tempat kering. Masker sekali pakai, tidak boleh dicuci atau digunakan kembali.',
            ],
            [
                'item_code'     => 'BHP-003',
                'item_name'     => 'Kapas Non Steril 500gr',
                'category_id'   => 2,
                'base_unit'     => 'pak',
                'warning_stock' => 3,
                'storage_temp'  => 'Room Temp',
                'size'          => '500 gram / pak',
                'warnings'      => 'Simpan di tempat tertutup, kering, dan bebas debu. Hindari kontak langsung dengan permukaan terkontaminasi.',
            ],
            [
                'item_code'     => 'BHP-004',
                'item_name'     => 'Alkohol Swab 70%',
                'category_id'   => 2,
                'base_unit'     => 'box',
                'warning_stock' => 5,
                'storage_temp'  => 'Room Temp',
                'size'          => '100 swab / box',
                'warnings'      => 'Bahan mudah terbakar, jauhkan dari sumber api. Simpan pada suhu ruang dan jangan gunakan pada area mata atau mukosa.',
            ],
            [
                'item_code'     => 'BHP-005',
                'item_name'     => 'Vacutainer 3 mL',
                'category_id'   => 2,
                'base_unit'     => 'pcs',
                'warning_stock' => 100,
                'storage_temp'  => 'Room Temp',
                'size'          => '3 mL / tube',
                'warnings'      => 'Simpan di tempat kering, hindari tekanan berlebih yang dapat merusak tube. Buang sebagai limbah medis setelah digunakan.',
            ],

            // ======================
            // 3) ATK (category_id = 3)
            // ======================
            [
                'item_code'     => 'ATK-001',
                'item_name'     => 'Ballpoint Biru',
                'category_id'   => 3,
                'base_unit'     => 'pcs',
                'warning_stock' => 30,
                'storage_temp'  => null,
                'size'          => '1 pcs',
                'warnings'      => 'Simpan di tempat kering, jauhkan dari suhu panas ekstrem agar tinta tidak cepat mengering.',
            ],
            [
                'item_code'     => 'ATK-002',
                'item_name'     => 'Pensil 2B',
                'category_id'   => 3,
                'base_unit'     => 'pcs',
                'warning_stock' => 20,
                'storage_temp'  => null,
                'size'          => '1 pcs',
                'warnings'      => 'Simpan di tempat kering. Hindari benturan yang dapat mematahkan isi pensil.',
            ],
            [
                'item_code'     => 'ATK-003',
                'item_name'     => 'Spidol Permanen',
                'category_id'   => 3,
                'base_unit'     => 'pcs',
                'warning_stock' => 15,
                'storage_temp'  => null,
                'size'          => '1 pcs',
                'warnings'      => 'Tutup rapat setelah digunakan untuk mencegah tinta mengering. Jauhkan dari anak-anak.',
            ],
            [
                'item_code'     => 'ATK-004',
                'item_name'     => 'Buku Tulis A5',
                'category_id'   => 3,
                'base_unit'     => 'buku',
                'warning_stock' => 40,
                'storage_temp'  => null,
                'size'          => 'A5 · 40 lembar',
                'warnings'      => 'Simpan di tempat kering dan bebas lembap agar kertas tidak berjamur.',
            ],
            [
                'item_code'     => 'ATK-005',
                'item_name'     => 'Map Plastik Folio',
                'category_id'   => 3,
                'base_unit'     => 'pcs',
                'warning_stock' => 25,
                'storage_temp'  => null,
                'size'          => 'Folio / A4',
                'warnings'      => 'Hindari panas berlebih yang dapat melengkungkan atau melelehkan plastik.',
            ],

            // ======================================
            // 4) Kebutuhan Rumah Tangga (category_id = 4)
            // ======================================
            [
                'item_code'     => 'HHP-001',
                'item_name'     => 'Cairan Pembersih Lantai',
                'category_id'   => 4,
                'base_unit'     => 'btl',
                'warning_stock' => 3,
                'storage_temp'  => 'Room Temp',
                'size'          => '1 L / botol',
                'warnings'      => 'Simpan jauh dari jangkauan anak-anak. Jangan dicampur dengan bahan kimia lain seperti pemutih.',
            ],
            [
                'item_code'     => 'HHP-002',
                'item_name'     => 'Detergen Cair',
                'category_id'   => 4,
                'base_unit'     => 'btl',
                'warning_stock' => 5,
                'storage_temp'  => 'Room Temp',
                'size'          => '1 L / botol',
                'warnings'      => 'Hindari kontak dengan mata. Simpan jauh dari makanan dan minuman.',
            ],
            [
                'item_code'     => 'HHP-003',
                'item_name'     => 'Kantong Sampah 50L',
                'category_id'   => 4,
                'base_unit'     => 'roll',
                'warning_stock' => 8,
                'storage_temp'  => 'Room Temp',
                'size'          => '20 lembar / roll · 50L',
                'warnings'      => 'Simpan di tempat kering. Gunakan sesuai kapasitas untuk mencegah robek.',
            ],
            [
                'item_code'     => 'HHP-004',
                'item_name'     => 'Tisu Gulung',
                'category_id'   => 4,
                'base_unit'     => 'roll',
                'warning_stock' => 15,
                'storage_temp'  => 'Room Temp',
                'size'          => '1 roll (±20 m)',
                'warnings'      => 'Simpan di tempat kering dan tertutup untuk menjaga kebersihan.',
            ],
            [
                'item_code'     => 'HHP-005',
                'item_name'     => 'Disinfektan Umum',
                'category_id'   => 4,
                'base_unit'     => 'btl',
                'warning_stock' => 4,
                'storage_temp'  => 'Room Temp',
                'size'          => '1 L / botol',
                'warnings'      => 'Gunakan di area berventilasi baik. Jangan dihirup langsung, jauhkan dari api dan anak-anak.',
            ],

        ];

        foreach ($items as $data) {
            ItemMaster::updateOrCreate(
                ['item_code' => $data['item_code']],
                $data
            );
        }
    }
}
