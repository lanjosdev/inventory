<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('contact_stores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fk_store')->constrained('stores')->onUpdate('cascade');
            $table->foreignId('fk_contact')->constrained('contacts')->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_stores');
    }
};