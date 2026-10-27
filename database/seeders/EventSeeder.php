<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::firstOrCreate(
            ['name' => 'Festival Teknologi Kampus'],
            [
                'description' => 'Acara teknologi dan inovasi mahasiswa.',
                'quota' => 300,
            ]
        );
    }
}