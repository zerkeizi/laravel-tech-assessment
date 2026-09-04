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
        // password and remember_token are deliberately excluded from the
        // snapshot: they carry credential material, and a hashed/rotated
        // value has no audit value that would justify storing it.
        DB::unprepared(<<<'SQL'
            CREATE TRIGGER users_log_after_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO users_log (subject_id, user_id, data, created_at, updated_at)
                VALUES (
                    OLD.id,
                    COALESCE(@current_user_id, 1),
                    JSON_OBJECT(
                        'id', OLD.id,
                        'name', OLD.name,
                        'email', OLD.email,
                        'email_verified_at', OLD.email_verified_at,
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
        DB::unprepared('DROP TRIGGER IF EXISTS users_log_after_update');
    }
};
