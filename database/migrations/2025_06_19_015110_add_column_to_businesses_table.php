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
            $table->string('trustmark_id')->nullable()->after('certificate');
            $table->integer('category_id')->nullable()->after('franchise');
            $table->dateTime('date_issued')->nullable()->after('trustmark_id');
            $table->dateTime('expired_date')->nullable()->after('date_issued');
            $table->string('url_platform')->nullable()->after('expired_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn([
                'trustmark_id',
                'category_id',
                'date_issued',
                'expired_date',
                'url_platform'
            ]);
        });
    }
};
