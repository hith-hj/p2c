<?php

use App\Models\V1\Carrier;
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
        Schema::create('carrier_details', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Carrier::class)->unique();
            $table->string('plate_number');
            $table->string('brand');
            $table->string('model');
            $table->string('color');
            $table->string('year');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrier_details');
    }
};
