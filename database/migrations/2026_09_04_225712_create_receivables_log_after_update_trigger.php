<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER receivables_log_after_update
            AFTER UPDATE ON receivables
            FOR EACH ROW
            BEGIN
                INSERT INTO receivables_log (receivable_id, user_id, data, created_at, updated_at)
                VALUES (
                    OLD.id,
                    COALESCE(@current_user_id, 1),
                    JSON_OBJECT(
                        'id', OLD.id,
                        'party_id', OLD.party_id,
                        'description', OLD.description,
                        'amount', OLD.amount,
                        'issue_date', OLD.issue_date,
                        'due_date', OLD.due_date,
                        'receipt_date', OLD.receipt_date,
                        'status', OLD.status,
                        'created_at', OLD.created_at,
                        'updated_at', OLD.updated_at
                    ),
                    NOW(),
                    NOW()
                );
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS receivables_log_after_update');
    }
};
