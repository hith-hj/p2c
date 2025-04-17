<?php

namespace Database\Seeders;

use App\Models\V1\Attr;
use App\Models\V1\Item;
use App\Models\V1\Role;
use App\Models\V1\Transportaion;
use App\Models\V1\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! Role::first()) {
            Role::factory()->producer()->create();
            Role::factory()->carrier()->create();
        }
        User::factory(10)->create();
        Attr::factory(5)->create();
        Item::factory(5)->create();
        User::factory()->create([
            'email'=>'carrier@user.com',
            'phone'=>'0911111112',
            'role'=>'carrier'
        ]);
        User::factory()->create([
            'email'=>'producer@user.com',
            'phone'=>'0911111111',
            'role'=>'producer'
        ]);
        // Transportaion::factory(10)->create();
    }
}
