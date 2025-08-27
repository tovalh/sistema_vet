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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->string('name'); // Ej: Sucursal Centro, Sucursal Norte
            $table->string('code')->nullable(); // Código interno de la sucursal
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->json('settings')->nullable(); // Configuraciones específicas
            $table->boolean('is_main')->default(false); // Sucursal principal
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'is_main']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
