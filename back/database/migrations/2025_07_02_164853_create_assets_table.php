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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('fk_store')->constrained('stores')->onUpdate('cascade');
            $table->foreignId('fk_sector')->constrained('stores')->onUpdate('cascade');
            $table->foreignId('fk_asset_type')->constrained('asset_types')->onUpdate('cascade');
            $table->foreignId('fk_status')->constrained('asset_types')->onUpdate('cascade');
            $table->string('observation', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};