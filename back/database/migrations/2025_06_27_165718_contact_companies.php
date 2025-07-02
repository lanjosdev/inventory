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
        Schema::create('contact_companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_companie')->constrained('companies')->onUpdate('cascade');
            $table->foreignId('fk_contact')->constrained('contacts')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        
        DB::table('contact_companies')->insert([
            'fk_companie' => 1,
            'fk_contact' => 1,
        ]);
        
        DB::table('contact_companies')->insert([
            'fk_companie' => 2,
            'fk_contact' => 2,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_companies');
    }
};