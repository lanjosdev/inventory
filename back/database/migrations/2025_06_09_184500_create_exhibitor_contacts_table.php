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
        Schema::create('exhibitor_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_exhibitor')->constrained('exhibitors')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('fk_contact')->constrained('contacts')->onUpdate('cascade')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exhibitor_contacts');
    }
};