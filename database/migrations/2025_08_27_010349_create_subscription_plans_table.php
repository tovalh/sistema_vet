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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ej: Básico, Profesional, Premium
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Precio mensual
            $table->json('features'); // Lista de características incluidas
            $table->integer('max_users')->default(1); // Límite de usuarios
            $table->integer('max_patients')->nullable(); // Límite de pacientes (null = ilimitado)
            $table->boolean('has_inventory')->default(false);
            $table->boolean('has_reports')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->index(['active', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
