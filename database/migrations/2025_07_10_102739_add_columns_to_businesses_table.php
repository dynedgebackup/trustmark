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
            $table->integer('app_status_id')->nullable()->after('admin_status');
            $table->integer('app_canned_id')->nullable()->after('app_status_id');
            $table->dropColumn(['admin_approved_by', 'admin_approved_at', 'admin_returned_by', 'admin_returned_at']);
            $table->integer('admin_updated_by')->nullable()->after('app_canned_id');
            $table->timestamp('admin_updated_at')->nullable()->after('admin_updated_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('admin_approved_by')->nullable()->after('admin_status');
            $table->timestamp('admin_approved_at')->nullable()->after('admin_approved_by');
            $table->integer('admin_returned_by')->nullable()->after('admin_approved_at');
            $table->timestamp('admin_returned_at')->nullable()->after('admin_returned_by');

            // Drop newly added columns
            $table->dropColumn([
                'app_status_id',
                'app_canned_id',
                'admin_updated_by',
                'admin_updated_at',
            ]);
        });
    }
};
