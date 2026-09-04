<?php

namespace Tests\Feature;

use App\Models\Party;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PartyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_deactivates_instead_of_deleting(): void
    {
        $party = Party::factory()->create(['active' => true]);

        $this->actingAs(User::factory()->create())
            ->deleteJson("/api/parties/{$party->id}")
            ->assertNoContent();

        $this->assertDatabaseHas('parties', ['id' => $party->id, 'active' => false]);
        $this->assertDatabaseCount('parties', 1);
    }

    public function test_index_excludes_inactive_parties_by_default(): void
    {
        $active = Party::factory()->create(['active' => true]);
        $inactive = Party::factory()->create(['active' => false]);

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/parties');

        $response->assertJsonPath('data.0.id', $active->id);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_index_includes_inactive_parties_when_requested(): void
    {
        Party::factory()->create(['active' => true]);
        Party::factory()->create(['active' => false]);

        $response = $this->actingAs(User::factory()->create())
            ->getJson('/api/parties?with_inactive=1');

        $this->assertCount(2, $response->json('data'));
    }

    public function test_update_writes_a_full_snapshot_to_parties_log_via_trigger(): void
    {
        $party = Party::factory()->create(['name' => 'Original Name']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->putJson("/api/parties/{$party->id}", [
                'type' => $party->type->value,
                'name' => 'Updated Name',
                'document' => $party->document,
                'email' => $party->email,
                'phone' => $party->phone,
            ])
            ->assertOk();

        $this->assertDatabaseCount('parties_log', 1);

        $log = DB::table('parties_log')->first();
        $data = json_decode($log->data, true);

        $this->assertSame($party->id, $log->party_id);
        $this->assertSame($user->id, $log->user_id);
        $this->assertSame('Original Name', $data['name']);
        $this->assertSame('Updated Name', $party->fresh()->name);
    }

    public function test_update_without_changes_does_not_write_a_log_entry(): void
    {
        $party = Party::factory()->create();

        $this->actingAs(User::factory()->create())
            ->putJson("/api/parties/{$party->id}", [
                'type' => $party->type->value,
                'name' => $party->name,
                'document' => $party->document,
                'email' => $party->email,
                'phone' => $party->phone,
            ])
            ->assertOk();

        $this->assertDatabaseCount('parties_log', 0);
    }

    public function test_direct_database_update_is_captured_by_trigger(): void
    {
        $party = Party::factory()->create(['name' => 'Original Name']);

        // Simulate a change made outside the app: no request ever ran
        // SetDatabaseAuditUser, so @current_user_id was never set on this
        // connection. Reset it explicitly since the test connection is
        // reused across the suite and an earlier test may have set it.
        DB::statement('SET @current_user_id = NULL');

        DB::table('parties')->where('id', $party->id)->update(['name' => 'Edited Directly']);

        $this->assertDatabaseCount('parties_log', 1);

        $log = DB::table('parties_log')->first();
        $data = json_decode($log->data, true);

        $this->assertSame(1, $log->user_id);
        $this->assertSame('Original Name', $data['name']);
    }
}
