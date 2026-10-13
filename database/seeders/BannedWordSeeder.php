<?php

namespace Database\Seeders;

use App\Models\BannedWord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannedWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $block = ['เหี้ย', 'fuck', 'สัส', 'แม่ง'];

        $review = ['โคตร', 'ชิบหาย', 'เฮงซวย', 'สัด'];

        foreach ($block as $word) {
            BannedWord::updateOrCreate(['word' => $word], ['type' => 'block']);
        }

        foreach ($review as $word) {
            BannedWord::updateOrCreate(['word' => $word], ['type' => 'review']);
        }
    }
}
