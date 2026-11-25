<?php

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
        Schema::create('map_requests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('seed');
            $table->bigInteger('origin_x');
            $table->bigInteger('origin_y');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->unsignedBigInteger('hits')->default(0);
            $table->timestamp('created_at');
            $table->timestamp('last_requested_at')->nullable();

            $table->unique(['seed', 'origin_x', 'origin_y', 'width', 'height']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('map_requests');
    }
};