<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\CategoryProductRepository;
use App\Repositories\OrderRepository;
use App\Repositories\UserRepository;


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
        $userId = auth()->id();
        $history = $this->orderRepository->transactionHistory($userId);
        return view('exchange.order.transaction-history', compact('history'));
    }

    public function orderBuy()
    {
        $userId = auth()->id();

        $purchases = $this->orderRepository->orderBuy($userId);

        return view('transactions.order.buy', compact('purchases'));
    }

    public function orderSell()
    {
        $userId = auth()->id();

        $sales = $this->orderRepository->orderSell($userId);

        return view('transactions.order.sell', compact('sales'));
    }

}
