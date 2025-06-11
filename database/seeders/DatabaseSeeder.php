<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\V1\Admin;
use App\Models\V1\Attr;
use App\Models\V1\Customer;
use App\Models\V1\Item;
use App\Models\V1\Order;
use App\Models\V1\Review;
use App\Models\V1\Role;
use App\Models\V1\Transportaion;
use App\Models\V1\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
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
        User::factory()->createManyQuietly([
            [
                'email' => 'carrier@user.com',
                'phone' => '0911111112',
                'role' => 'carrier',
            ],
            [
                'email' => 'producer@user.com',
                'phone' => '0911111111',
                'role' => 'producer',
            ],
            [
                'email' => 'carrier@maya.com',
                'phone' => '0993769517',
                'role' => 'carrier',
                'password' => Hash::make('Mm12345@@'),
                'firebase_token' => 'dhVqlMtKSjOgtDHgWBwt9j:APA91bEdLM6Q7GWYhCmBJ4ywZphROt57nAtDTwtlj5uPLIGriLMdOnSNurRJBpZAKzfv6OEACZsqs-d7IyfKi49r3su_eAyj-wdGf3CgcazS0I5XVSNP1RY',
            ],
        ]);

        Admin::factory()->create([
            'name' => 'name',
            'email' => 'admin@admin.com',
        ]);

        Customer::factory(2)->create();

        Order::factory(10)->create();
        Review::factory(10)->create();
        // Transportaion::factory(10)->create();
    }
}
