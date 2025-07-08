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
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_user')->constrained('users')->onUpdate('cascade');
            $table->foreignId('fk_action')->constrained('actions')->onUpdate('cascade')->onDelete('restrict');
            $table->string('name_table', 255);
            $table->integer('record_id');
            $table->text('description', 10000);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};