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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('phone', 255);
            $table->string('observation', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('contacts')->insert([
            [
                'name' => 'João Carlos Sponchiado',
                'email' => 'joao.carlos@savegnago.com.br',
                'phone' => '16 99143-8263',
                'observation' => 'Responsável pela áera de Retail Media',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'João Carlos Sponchiado',
                'email' => 'joao.carlos@savegnago.com.br',
                'phone' => '17 99143-8263',
                'observation' => 'Responsável pela áera de Retail Media',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};