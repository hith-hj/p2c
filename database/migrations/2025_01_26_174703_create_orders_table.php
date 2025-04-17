<?php

use App\Models\V1\Branch;
use App\Models\V1\Transportation;
use App\Models\V1\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class, 'producer_id');
            $table->foreignIdFor(Branch::class, 'branch_id');
            $table->foreignIdFor(User::class, 'carrier_id')->nullable();
            $table->foreignIdFor(Transportation::class, 'transportation_id');
            $table->string('customer_name')->default('customer');
            $table->string('delivery_type');
            $table->float('src_long');
            $table->float('src_lat');
            $table->float('dest_long');
            $table->float('dest_lat');
            $table->integer('distance');
            $table->integer('weight');
            $table->integer('cost');
            $table->integer('goods_price');
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('deliverd_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
