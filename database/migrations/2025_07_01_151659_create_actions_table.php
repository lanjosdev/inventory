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
        Schema::create('actions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('description', 255);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('actions')->insert([
            ['name' => 'Criou', 'description' => 'Adicionou um registro na base de dados', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Editou', 'description' => 'Editou um registro da base de dados', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Removeu', 'description' => 'Removeu um registro na base de dados', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Conectou', 'description' => 'O usuário entrou na aplicação', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Desconectou', 'description' => 'O usuário saiu na aplicação', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actions');
    }
};