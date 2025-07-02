<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('status', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('status')->insert([
           ['name' => "Ativo", 'created_at' => now(), 'updated_at' => now()], 
           ['name' => "Inativo", 'created_at' => now(), 'updated_at' => now()], 
           ['name' => "Pendente", 'created_at' => now(), 'updated_at' => now()], 
           ['name' => "Em andamento", 'created_at' => now(), 'updated_at' => now()], 
           ['name' => "Finalizado", 'created_at' => now(), 'updated_at' => now()], 
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('status');
    }
};