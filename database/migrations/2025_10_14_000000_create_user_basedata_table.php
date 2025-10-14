<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_basedata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->string('full_name');
            $table->string('mothers_name');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->string('id_card_number')->unique();
            $table->string('social_security_number')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_basedata');
    }
};
