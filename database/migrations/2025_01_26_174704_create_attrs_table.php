<?php

use App\Models\V1\Attr;
use App\Models\V1\Order;
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
        Schema::create('attrs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('extra_cost_percent');
            $table->timestamps();
        });

        Schema::create('attr_order', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Order::class);
            $table->foreignIdFor(Attr::class);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attrs');
        Schema::dropIfExists('attr_order');
    }
};
