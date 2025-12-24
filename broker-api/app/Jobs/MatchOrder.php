<?php

namespace App\Jobs;

use App\Events\OrderMatched;
use App\Models\Asset;
use App\Models\Order;
use App\Models\Trade;
use App\OrderStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class MatchOrder implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(): void
    {
        if ($this->order->status !== OrderStatus::Open) {
            return;
        }

        DB::transaction(function () {
            if ($this->order->side === 'buy') {
                $matchingOrder = Order::query()
                    ->where('status', OrderStatus::Open)
                    ->where('side', 'sell')
                    ->where('symbol', $this->order->symbol)
                    ->where('price', '<=', $this->order->price)
                    ->where('amount', $this->order->amount)
                    ->oldest()
                    ->first();

                if ($matchingOrder) {
                    $this->executeTrade($this->order, $matchingOrder);
                }
            }

            if ($this->order->side === 'sell') {
                $matchingOrder = Order::query()
                    ->where('status', OrderStatus::Open)
                    ->where('side', 'buy')
                    ->where('symbol', $this->order->symbol)
                    ->where('price', '>=', $this->order->price)
                    ->where('amount', $this->order->amount)
                    ->where('id', '!=', $this->order->id)
                    ->oldest()
                    ->first();

                if ($matchingOrder) {
                    $this->executeTrade($matchingOrder, $this->order);
                }
            }
        });
    }

    protected function executeTrade(Order $buyOrder, Order $sellOrder): void
    {
        $totalValue = $buyOrder->price * $buyOrder->amount;
        $commission = $totalValue * 0.015;
        $sellerReceives = $totalValue - $commission;

        $buyOrder->update(['status' => OrderStatus::Filled]);
        $sellOrder->update(['status' => OrderStatus::Filled]);

        $trade = Trade::create([
            'buying_order_id' => $buyOrder->id,
            'selling_order_id' => $sellOrder->id,
        ]);

        $buyerAsset = Asset::firstOrCreate(
            [
                'user_id' => $buyOrder->user_id,
                'symbol' => $buyOrder->symbol,
            ],
            [
                'amount' => 0,
                'locked_amount' => 0,
            ]
        );

        $buyerAsset->increment('amount', $buyOrder->amount);

        $sellOrder->user->increment('balance', $sellerReceives);

        $sellerAsset = Asset::query()
            ->where('user_id', $sellOrder->user_id)
            ->where('symbol', $sellOrder->symbol)
            ->first();

        $sellerAsset->decrement('locked_amount', $sellOrder->amount);

        OrderMatched::dispatch($trade);
    }
}
