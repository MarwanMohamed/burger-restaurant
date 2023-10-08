<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function order(OrderRequest $request)
    {
        $order = $this->orderService->createOrder($request->get('products'));

        return response()->json([
            'message' => trans('messages.order_created'),
            'data' => new OrderResource($order)
        ], 201);
    }
}
