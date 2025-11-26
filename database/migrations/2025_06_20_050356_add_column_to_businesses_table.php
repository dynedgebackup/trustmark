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
        Schema::table('businesses', function (Blueprint $table) {
            $table->string('admin_remarks')->nullable()->after('is_active');
            $table->integer('admin_update_by')->nullable()->after('admin_remarks');
            $table->timestamp('admin_update_at')->nullable()->after('admin_update_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'admin_remarks',
                'admin_update_by',
                'admin_update_at'
            ]);
        });
    }
};
