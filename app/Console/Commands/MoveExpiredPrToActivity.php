<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MoveExpiredPrToActivity extends Command
{
    protected $signature = 'news:move-expired';
    protected $description = 'Move expired PR news to activity news automatically';

    public function handle()
    {
        DB::transaction(function () {

            $expired = DB::table('mju_pr_news')
                ->whereNotNull('event_date')
                ->where('event_date', '<', now()->toDateString())
                ->get();

            foreach ($expired as $row) {
                DB::table('mju_activity_news')->insert([
                    'title'       => $row->title,
                    'description' => $row->description,
                    'image1'      => $row->image1,
                    'image2'      => $row->image2,
                    'image3'      => $row->image3,
                    'image4'      => $row->image4,
                    'image5'      => $row->image5,
                    'created_at'  => $row->created_at,
                    'updated_at'  => now(),
                ]);

                DB::table('mju_pr_news')
                    ->where('id', $row->id)
                    ->delete();
            }
        });

        $this->info('Expired PR news moved successfully.');
    }
}
