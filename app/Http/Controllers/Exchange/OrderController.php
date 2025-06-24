<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\CategoryProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    private $orderRepository;
    private $userRepository;
    private $categoryProductRepository;

    public function __construct(
        OrderRepository $orderRepository,
        CategoryProductRepository $categoryProductRepository,
        UserRepository $userRepository
    )
    {
        $this->orderRepository = $orderRepository;
        $this->categoryProductRepository = $categoryProductRepository;
        $this->userRepository = $userRepository;
    }

    public function transactionHistory()
    {
        $userId = Auth::guard('canstum')->user()->id;
        $history = $this->orderRepository->transactionHistory($userId);
        return view('exchange.order.transaction-history', compact('history'));
    }

    public function orderBuy()
    {
        $userId = Auth::guard('canstum')->user()->id;

        $purchases = $this->orderRepository->orderBuy($userId);

        return view('exchange.order.buy', compact('purchases'));
    }

    public function orderSell()
    {
        $userId = Auth::guard('canstum')->user()->id;

        $sales = $this->orderRepository->orderSell($userId);

        return view('exchange.order.sell', compact('sales'));
    }

}
