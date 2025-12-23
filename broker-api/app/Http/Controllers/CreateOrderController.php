<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Jobs\MatchOrder;
use App\Models\Asset;
use App\Models\Order;
use App\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreateOrderController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'symbol' => 'required|string',
            'side' => 'required|in:buy,sell',
            'price' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            return $validated['side'] === 'sell' ?
                $this->handleSell($request, $validated) :
                $this->handleBuy($request, $validated);
        });
    }

    protected function handleBuy($request, $validated)
    {
        $user = $request->user();
        $total = $validated['price'] * $validated['amount'];

        if ($user->balance < $total) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        $user->decrement('balance', $total);

        $order = Order::create([
            'user_id' => $user->id,
            'symbol' => $validated['symbol'],
            'side' => 'buy',
            'price' => $validated['price'],
            'amount' => $validated['amount'],
            'status' => OrderStatus::Open,
        ]);

        MatchOrder::dispatch($order);

        return response()->json(OrderResource::make($order), 201);
    }

    protected function handleSell($request, $validated)
    {
        $user = $request->user();

        $asset = Asset::query()
            ->where('user_id', $user->id)
            ->where('symbol', $validated['symbol'])
            ->first();

        if (! $asset || $asset->amount < $validated['amount']) {
            return response()->json(['message' => 'Insufficient asset amount'], 400);
        }

        $asset->decrement('amount', $validated['amount']);
        $asset->increment('locked_amount', $validated['amount']);

        $order = Order::create([
            'user_id' => $user->id,
            'symbol' => $validated['symbol'],
            'side' => 'sell',
            'price' => $validated['price'],
            'amount' => $validated['amount'],
            'status' => OrderStatus::Open,
        ]);

        MatchOrder::dispatch($order);

        return response()->json(OrderResource::make($order), 201);
    }
}
