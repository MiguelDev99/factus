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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
        
            // Nuevos campos para facturación
            $table->string('identification')->unique();
            $table->string('dv')->nullable();
            $table->string('company')->nullable();
            $table->string('trade_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('legal_organization_id')->nullable();
            $table->string('tribute_id')->nullable();
            $table->string('identification_document_id')->nullable();
            $table->string('municipality_id')->nullable();
        
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
