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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('address_id')
                  ->nullable()  // Add this explicitly
                  ->constrained('addresses')
                  ->nullOnDelete()
                  ->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign(['address_id']);
            // Then drop the column
            $table->dropColumn('address_id');
        });
    }
};