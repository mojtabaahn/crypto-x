<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\OrderStatus;
use Illuminate\Http\Request;

class ProfileOrderController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return OrderResource::collection(
            Order::query()
                ->where('user_id', $request->user()->id)
                ->when($request->input('symbol'), fn($query, $symbol) => $query->where('symbol', $symbol))
                ->when($request->input('side'), fn($query, $side) => $query->where('side', $side))
                ->when(
                    $request->input('status') && in_array(ucfirst($request->input('status')), [
                        OrderStatus::Open->name,
                        OrderStatus::Filled->name,
                        OrderStatus::Cancelled->name,
                    ]),
                    fn($query, $status) => $query->where('status', OrderStatus::{ucfirst($status)}->value)
                )
                ->get()
        );
    }
}
