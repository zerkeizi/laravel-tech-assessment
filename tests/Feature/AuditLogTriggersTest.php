<?php

namespace Tests\Feature;

use App\Models\Payable;
use App\Models\Receivable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditLogTriggersTest extends TestCase
{
    use RefreshDatabase;

    public function test_payable_update_is_captured_by_trigger(): void
    {
        $payable = Payable::factory()->create(['description' => 'Original description']);
        $user = User::factory()->create();

        DB::statement('SET @current_user_id = ?', [$user->id]);
        $payable->update(['description' => 'Updated description']);

        $this->assertDatabaseCount('payables_log', 1);

        $log = DB::table('payables_log')->first();
        $data = json_decode($log->data, true);

        $this->assertSame($payable->id, $log->payable_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('Original description', $data['description']);
    }

    public function test_receivable_update_is_captured_by_trigger(): void
    {
        $receivable = Receivable::factory()->create(['description' => 'Original description']);
        $user = User::factory()->create();

        DB::statement('SET @current_user_id = ?', [$user->id]);
        $receivable->update(['description' => 'Updated description']);

        $this->assertDatabaseCount('receivables_log', 1);

        $log = DB::table('receivables_log')->first();
        $data = json_decode($log->data, true);

        $this->assertSame($receivable->id, $log->receivable_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('Original description', $data['description']);
    }

    public function test_user_update_is_captured_by_trigger_and_excludes_credentials(): void
    {
        $subject = User::factory()->create(['name' => 'Original Name']);
        $actor = User::factory()->create();

        DB::statement('SET @current_user_id = ?', [$actor->id]);
        $subject->update(['name' => 'Updated Name']);

        $this->assertDatabaseCount('users_log', 1);

        $log = DB::table('users_log')->first();
        $data = json_decode($log->data, true);

        $this->assertSame($subject->id, $log->subject_id);
        $this->assertSame($actor->id, $log->user_id);
        $this->assertSame('Original Name', $data['name']);
        $this->assertArrayNotHasKey('password', $data);
        $this->assertArrayNotHasKey('remember_token', $data);
    }

    public function test_direct_database_updates_default_to_user_one_across_all_logs(): void
    {
        $payable = Payable::factory()->create();
        $receivable = Receivable::factory()->create();
        $subject = User::factory()->create();

        DB::statement('SET @current_user_id = NULL');

        DB::table('payables')->where('id', $payable->id)->update(['description' => 'Edited directly']);
        DB::table('receivables')->where('id', $receivable->id)->update(['description' => 'Edited directly']);
        DB::table('users')->where('id', $subject->id)->update(['name' => 'Edited directly']);

        $this->assertSame(1, DB::table('payables_log')->value('user_id'));
        $this->assertSame(1, DB::table('receivables_log')->value('user_id'));
        $this->assertSame(1, DB::table('users_log')->value('user_id'));
    }
}
