<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class ArticlesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('articles')->insert([
            ['title' => 'First Article', 'content' => 'Content of the first article', 'created_at' => now()],
            ['title' => 'Second Article', 'content' => 'Content of the second article', 'created_at' => now()],
            ['title' => 'Third Article', 'content' => 'Content of the third article', 'created_at' => now()],
            ['title' => 'Fourth Article', 'content' => 'Content of the fourth article', 'created_at' => now()],
            ['title' => 'Fifth Article', 'content' => 'Content of the fifth article', 'created_at' => now()],
            ['title' => 'Sixth Article', 'content' => 'Content of the sixth article', 'created_at' => now()],
            ['title' => 'Seventh Article', 'content' => 'Content of the seventh article', 'created_at' => now()],
            ['title' => 'Eighth Article', 'content' => 'Content of the eighth article', 'created_at' => now()],
            ['title' => 'Ninth Article', 'content' => 'Content of the ninth article', 'created_at' => now()],
            ['title' => 'Tenth Article', 'content' => 'Content of the tenth article', 'created_at' => now()],
        ]);
    }
}
