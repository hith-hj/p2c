<?php

declare(strict_types=1);

use App\Models\V1\Transportation;
use App\Models\V1\User;
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
        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Transportation::class);
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('rate');
            $table->boolean('is_valid')->default(false);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_available')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carriers');
    }
};
