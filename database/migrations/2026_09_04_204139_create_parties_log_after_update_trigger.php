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
            CREATE TRIGGER parties_log_after_update
            AFTER UPDATE ON parties
            FOR EACH ROW
            BEGIN
                INSERT INTO parties_log (party_id, user_id, data, created_at, updated_at)
                VALUES (
                    OLD.id,
                    COALESCE(@current_user_id, 1),
                    JSON_OBJECT(
                        'id', OLD.id,
                        'type', OLD.type,
                        'name', OLD.name,
                        'document', OLD.document,
                        'email', OLD.email,
                        'phone', OLD.phone,
                        'active', OLD.active,
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
        DB::unprepared('DROP TRIGGER IF EXISTS parties_log_after_update');
    }
};
