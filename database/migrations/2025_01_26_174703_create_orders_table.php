<?php

declare(strict_types=1);

use App\Models\V1\Branch;
use App\Models\V1\Carrier;
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
            $table->foreignIdFor(Carrier::class)->nullable();
            $table->foreignIdFor(Transportation::class)->nullable();
            $table->foreignIdFor(Producer::class)->nullable();
            $table->foreignIdFor(Branch::class)->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->string('customer_name')->default('customer');
            $table->tinyText('note')->nullable();
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
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('dte')->nullable();
            $table->integer('volume')->nullable();
            $table->json('dimensions')->nullable();
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
