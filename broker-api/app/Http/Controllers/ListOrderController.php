<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\OrderStatus;
use Illuminate\Http\Request;

class ListOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return OrderResource::collection(
            Order::query()
                ->where('status', OrderStatus::Open)
                ->when($request->input('symbol'), fn ($query, $symbol) => $query->where('symbol', $symbol))
                ->when($request->input('side'), fn ($query, $side) => $query->where('side', $side))
                ->get()
        );
    }
}
