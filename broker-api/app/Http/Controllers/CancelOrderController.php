<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Asset;
use App\Models\Order;
use App\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CancelOrderController extends Controller
{
    public function __invoke(Request $request, Order $order)
    {
        if ($order->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($order->status !== OrderStatus::Open) {
            return response()->json(['message' => 'Order cannot be cancelled'], 400);
        }

        return DB::transaction(function () use ($request, $order) {
            return $order->side === 'buy' ?
                $this->cancelBuyOrder($request, $order) :
                $this->cancelSellOrder($request, $order);
        });
    }

    protected function cancelBuyOrder($request, Order $order)
    {
        $total = $order->price * $order->amount;

        $order->user->increment('balance', $total);

        $order->update(['status' => OrderStatus::Cancelled]);

        return response()->json(OrderResource::make($order));
    }

    protected function cancelSellOrder($request, Order $order)
    {
        $asset = Asset::query()
            ->where('user_id', $order->user_id)
            ->where('symbol', $order->symbol)
            ->first();

        $asset->decrement('locked_amount', $order->amount);
        $asset->increment('amount', $order->amount);

        $order->update(['status' => OrderStatus::Cancelled]);

        return response()->json(OrderResource::make($order));

    }
}
