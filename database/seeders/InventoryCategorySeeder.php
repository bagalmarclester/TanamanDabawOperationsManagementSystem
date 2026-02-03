<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventoryCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Softscapes (Living)',
                'description' => 'Live plants including trees, palms, sod, shrubs, and vines.',
            
            ],
            [
                'name' => 'Hardscapes',
                'description' => 'Natural stone (Araal/Mactan), pavers, bricks, and wall cladding.',
            ],
            [
                'name' => 'Aggregates',
                'description' => 'Loose materials like river sand, gravel, pebbles, and boulders.',
            ],
            [
                'name' => 'Soil & Media',
                'description' => 'Growing media like garden soil, compost, carbonized rice hull (CRH), and coco peat.',
            ],
            [
                'name' => 'Fertilizers & Chemicals',
                'description' => 'Nutrients and control agents like Urea, rooting hormones, and pesticides.',
            ],
            [
                'name' => 'Pots & Planters',
                'description' => 'Containers including clay (terracotta), plastic nursery bags, and cement pots.',
            ],
            [
                'name' => 'Water Features & Irrigation',
                'description' => 'Pumps, PVC pipes, sprinkler heads, and decorative jars.',
            ],
            [
                'name' => 'Garden Structures',
                'description' => 'Construction items like bamboo poles, trellises, and nipa roofing.',
            ],
            [
                'name' => 'Consumables',
                'description' => 'Disposable supplies like weed block fabrics, empty sacks, and tie wires.',
            ],
        ];

        $now = Carbon::now();

        foreach ($categories as $category) {
            DB::table('inventory_categories')->insert([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']), // e.g., 'softscapes-living'
                'description' => $category['description'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}