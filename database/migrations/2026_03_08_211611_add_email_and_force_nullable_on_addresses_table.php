<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('addresses', 'email')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->string('email')->nullable();
            });
        }

        // Force nullability on user_id for Postgres if change() failed silently
        try {
            DB::statement('ALTER TABLE addresses ALTER COLUMN user_id DROP NOT NULL');
            DB::statement('ALTER TABLE orders ALTER COLUMN user_id DROP NOT NULL');
            DB::statement('ALTER TABLE addresses ALTER COLUMN postal_code DROP NOT NULL');
        } catch (\Exception $e) {
            // Might fail if not Postgres or already done
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
};
