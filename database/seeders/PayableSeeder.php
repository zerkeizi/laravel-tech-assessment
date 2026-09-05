<?php

namespace Database\Seeders;

use App\Models\Party;
use App\Models\Payable;
use Illuminate\Database\Seeder;

class PayableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parties = Party::all();

        Payable::factory(3)->pending()->recycle($parties)->create();
        Payable::factory(3)->paid()->recycle($parties)->create();
        Payable::factory(3)->overdue()->recycle($parties)->create();
        Payable::factory(3)->cancelled()->recycle($parties)->create();
    }
}
