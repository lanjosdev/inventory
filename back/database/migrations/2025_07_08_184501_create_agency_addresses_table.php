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
        Schema::create('agency_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_agency')->constrained('agencies')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('fk_address')->constrained('addresses')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agency_addresses');
    }
};