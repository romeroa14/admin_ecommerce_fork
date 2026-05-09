<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Fix status CHECK constraint — add 'shipped' and 'delivered'
        $this->fixStatusCheckConstraint();

        // 2. Fix user_id nullable with proper FK handling
        $this->makeUserIdNullable();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safety checks
        if (DB::table('orders')->whereNull('user_id')->exists()) {
            throw new \Exception(
                'Cannot rollback: orders with null user_id exist. ' .
                'Assign a user_id to all orders or delete guest orders before rolling back.'
            );
        }

        if (DB::table('orders')->whereIn('status', ['shipped', 'delivered'])->exists()) {
            throw new \Exception(
                'Cannot rollback: orders with status "shipped" or "delivered" exist. ' .
                'Change their status to a pre-migration value before rolling back.'
            );
        }

        // 1. Revert user_id to NOT NULL
        $this->revertUserIdNotNull();

        // 2. Revert status CHECK constraint to original 5 values
        $this->revertStatusCheckConstraint();
    }

    /**
     * Drop existing CHECK constraint and recreate with all 7 status values.
     * Only runs on PostgreSQL (SQLite does not enforce CHECK constraints on ENUM).
     */
    private function fixStatusCheckConstraint(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $constraintName = $this->getStatusConstraintName();

        DB::statement("ALTER TABLE orders DROP CONSTRAINT \"{$constraintName}\"");
        DB::statement(
            "ALTER TABLE orders ADD CONSTRAINT \"{$constraintName}\" " .
            "CHECK (status::text = ANY (ARRAY[" .
            "'pending'::text, 'processing'::text, 'confirmed'::text, " .
            "'shipped'::text, 'delivered'::text, 'cancelled'::text, 'refunded'::text]))"
        );
    }

    /**
     * Drop existing CHECK constraint and recreate with original 5 status values.
     * Only runs on PostgreSQL.
     */
    private function revertStatusCheckConstraint(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        $constraintName = $this->getStatusConstraintName();

        DB::statement("ALTER TABLE orders DROP CONSTRAINT \"{$constraintName}\"");
        DB::statement(
            "ALTER TABLE orders ADD CONSTRAINT \"{$constraintName}\" " .
            "CHECK (status::text = ANY (ARRAY[" .
            "'pending'::text, 'processing'::text, 'confirmed'::text, " .
            "'cancelled'::text, 'refunded'::text]))"
        );
    }

    /**
     * Query pg_constraint dynamically for the status CHECK constraint name.
     * Falls back to 'orders_status_check' (Laravel default naming) if query returns no row.
     */
    private function getStatusConstraintName(): string
    {
        $row = DB::selectOne(
            "SELECT conname FROM pg_constraint " .
            "WHERE conrelid = 'orders'::regclass " .
            "AND contype = 'c' " .
            "AND pg_get_constraintdef(oid) LIKE '%status%'"
        );

        return $row->conname ?? 'orders_status_check';
    }

    /**
     * Drop FK, make column nullable, recreate FK.
     */
    private function makeUserIdNullable(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Drop FK, make column NOT NULL, recreate FK.
     * Caller must verify no null user_ids exist before calling.
     */
    private function revertUserIdNotNull(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
