<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ConvertSlipToBase64 extends Command
{
    protected $signature = 'slip:convert-base64';
    protected $description = 'Convert slip_image path to base64';

    public function handle()
    {
        $orders = DB::table('mju_orders')
            ->whereNotNull('slip_image')
            ->where('slip_image', 'not like', 'data:image%')
            ->get();

        foreach ($orders as $order) {

            $filePath = storage_path('app/public/' . $order->slip_image);

            if (!file_exists($filePath)) {
                $this->error("File not found: {$filePath}");
                continue;
            }

            $imageData = file_get_contents($filePath);
            $mimeType = mime_content_type($filePath);

            $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($imageData);

            DB::table('mju_orders')
                ->where('id', $order->id)
                ->update([
                    'slip_image' => $base64
                ]);

            $this->info("Converted order ID {$order->id}");
        }

        $this->info('Done!');
    }
}
