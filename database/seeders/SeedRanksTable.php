<?php

namespace Database\Seeders;

use App\Models\Rank;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeedRanksTable extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $ranksArray = Rank::RANKS_ARRAY;
      foreach ($ranksArray as $key => $rank) {
        DB::table('ranks')->insert([
          'rank_name' => $rank,
          'rank_key' => str_replace(' ', '_', strtolower($rank)),
          'created_by' => '1', // ToDo: replace with admin id
          'created_at' => Carbon::now()
        ]);
      }
    }
}
