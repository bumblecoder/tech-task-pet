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
            ['title' => 'First Article', 'content' => 'Content of the first article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Second Article', 'content' => 'Content of the second article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Third Article', 'content' => 'Content of the third article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Fourth Article', 'content' => 'Content of the fourth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Fifth Article', 'content' => 'Content of the fifth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Sixth Article', 'content' => 'Content of the sixth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Seventh Article', 'content' => 'Content of the seventh article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Eighth Article', 'content' => 'Content of the eighth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Ninth Article', 'content' => 'Content of the ninth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
            ['title' => 'Tenth Article', 'content' => 'Content of the tenth article', 'created_at' => now(), 'image' => 'https://d15ywwv3do91l7.cloudfront.net/medium.webp'],
        ]);
    }
}
