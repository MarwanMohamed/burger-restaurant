<?php

namespace App\Jobs;

use App\Mail\ProductStockIsLow;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CheckProductQuantityLimitAndSendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function handle(): void
    {
        $updateStartedAtAffected = DB::update('update products set stock_notification_started_at = ?
            where stock_notification_started_at is null and id = ?', [now(), $this->product->id]);

        if (1 !== $updateStartedAtAffected) {
            return;
        }

        Mail::to(env('MERCHANT_EMAIL'))->queue(new ProductStockIsLow($this->product));

        $this->product->update(['stock_notification_sent_at' => now()]);
    }
}
