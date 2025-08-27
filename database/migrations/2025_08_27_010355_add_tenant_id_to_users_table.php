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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->constrained()->onDelete('cascade')->after('id');
            $table->boolean('is_super_admin')->default(false)->after('tenant_id');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->enum('status', ['active', 'inactive', 'pending'])->default('active')->after('address');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            
            $table->index(['tenant_id', 'status']);
            $table->index('is_super_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropIndex(['tenant_id', 'status']);
            $table->dropIndex('users_is_super_admin_index');
            $table->dropColumn([
                'tenant_id', 
                'is_super_admin', 
                'phone', 
                'address', 
                'status', 
                'last_login_at'
            ]);
        });
    }
};
