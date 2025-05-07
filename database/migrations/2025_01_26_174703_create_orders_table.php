<?php

declare(strict_types=1);

use App\Models\V1\Branch;
use App\Models\V1\Carrier;
use App\Models\V1\Customer;
use App\Models\V1\Producer;
use App\Models\V1\Transportation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Customer::class)->nullable();
            $table->foreignIdFor(Producer::class)->nullable();
            $table->foreignIdFor(Branch::class)->nullable();
            $table->foreignIdFor(Carrier::class)->nullable();
            $table->foreignIdFor(Transportation::class)->nullable();
            $table->string('delivery_type');
            $table->integer('goods_price');
            $table->integer('distance');
            $table->integer('weight');
            $table->integer('cost');
            $table->float('src_long');
            $table->float('src_lat');
            $table->float('dest_long');
            $table->float('dest_lat');
            $table->tinyInteger('status')->default(0);
            $table->integer('volume')->nullable();
            $table->json('dimensions')->nullable();
            $table->tinyText('note')->nullable();
            $table->timestamp('dte')->nullable();
            $table->timestamp('picked_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
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
