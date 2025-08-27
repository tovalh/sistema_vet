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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nombre de la clínica
            $table->string('slug')->unique(); // URL slug para subdomain/path
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_url')->nullable();
            $table->json('settings')->nullable(); // Configuraciones personalizadas
            $table->enum('status', ['active', 'suspended', 'pending'])->default('pending');
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
