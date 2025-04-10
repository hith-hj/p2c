<?php

use App\Models\V1\Branch;
use App\Models\V1\Carrier;
use App\Models\V1\Producer;
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
            $table->foreignIdFor(Producer::class, 'producer_id');
            $table->foreignIdFor(Branch::class, 'branch_id');
            $table->foreignIdFor(Carrier::class, 'carrier_id');
            $table->string('customer_name')->default('customer');
            $table->float('src_long');
            $table->float('src_lat');
            $table->float('dist_long');
            $table->float('dist_lat');
            $table->integer('distance');
            $table->string('delivery_type');
            $table->integer('weight');
            $table->integer('cost');
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
