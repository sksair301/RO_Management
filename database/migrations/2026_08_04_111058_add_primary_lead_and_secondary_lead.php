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
            $table->foreignId('primary_lead_id')->nullable()->after('departments_id')->constrained('users')->nullOnDelete();
            $table->foreignId('secondary_lead_id')->nullable()->after('primary_lead_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['primary_lead_id']);
            $table->dropForeign(['secondary_lead_id']);

            $table->dropColumn(['primary_lead_id','secondary_lead_id']);
        });
    }
};
