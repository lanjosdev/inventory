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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_user')->constrained('users')->onUpdate('cascade');
            $table->foreignId('fk_role')->constrained('roles')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::table('user_roles')->insert([
            ['fk_user' => 1, 'fk_role' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_roles');
    }
};