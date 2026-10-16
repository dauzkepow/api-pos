<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Makanan Ringan',
                'description' => 'Berbagai macam camilan seperti kripik, biskuit, wafer, dan kacang-kacangan.',

            ],
            [
                'name' => 'Minuman',
                'description' => 'Air mineral, teh kemasan, kopi instan, jus, soda, dan minuman energi.',

            ],
            [
                'name' => 'Kebutuhan Dapur & Bahan Pokok',
                'description' => 'Minyak goreng, beras, gula, mi instan, bumbu dapur, dan tepung.',

            ],
            [
                'name' => 'Susu & Olahan Susu',
                'description' => 'Susu UHT, susu bubuk, susu formula anak, keju, dan yogurt.',

            ],
            [
                'name' => 'Perawatan Diri',
                'description' => 'Sabun mandi, sampo, pembersih wajah, pasta gigi, dan perawatan kulit.',

            ],
            [
                'name' => 'Kebutuhan Ibu & Bayi',
                'description' => 'Popok bayi, tisu basah, makanan bayi, dan perlengkapan mandi bayi.',

            ],
            [
                'name' => 'Pembersih & Rumah Tangga',
                'description' => 'Detergen, pembersih lantai, sabun cuci piring, pengharum ruangan, dan obat nyamuk.',

            ],
            [
                'name' => 'Sembako',
                'description' => 'Kebutuhan pokok sehari-hari seperti beras, gula, minyak goreng, dan telur'
            ],
            [
                'name' => 'Kesehatan & Obat-obatan',
                'description' => 'Obat bebas (P3K), vitamin, minyak kayu putih, dan plester luka.',

            ],
            [
                'name' => 'Roti & Kue',
                'description' => 'Roti tawar, roti manis, dan aneka kue basah kemasan.',

            ],
            [
                'name' => 'Es Krim & Makanan Beku',
                'description' => 'Anak es krim, nugget, sosis, dan makanan olahan beku lainnya.',

            ],
            [
                'name' => 'Rokok',
                'description' => 'Berbagai merk rokok dan produk tembakau'
            ],
        ];

        DB::table('product_categories')->insert($categories);
    }
}