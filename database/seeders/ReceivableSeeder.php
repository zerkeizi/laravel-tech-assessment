<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\Receivable;
use Illuminate\Database\Seeder;

class ReceivableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parties = Party::all();

        Receivable::factory(3)->pending()->recycle($parties)->create();
        Receivable::factory(3)->received()->recycle($parties)->create();
        Receivable::factory(3)->overdue()->recycle($parties)->create();
        Receivable::factory(3)->cancelled()->recycle($parties)->create();
    }
}
