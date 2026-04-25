<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// যদি Seeder গুলো এই namespace-এ না থাকে, import করতে হবে

use Database\Seeders\DistrictSeeder;
use Database\Seeders\DivisionSeeder;
use Database\Seeders\ThanaSeeder;
use Database\Seeders\UnionSeeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
         $this->call([
            
             DistrictSeeder::class,
             DivisionSeeder::class,
             ThanaSeeder::class,
             UnionSeeder::class
         ]);
    }
}
