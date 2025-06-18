<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository extends BaseRepository
{
    public function model()
    {
        return Order::class;
    }

    public function transactionHistory($userId)
    {
        return $this->model->where('buyer_id', $userId)
            ->orWhere('seller_id', $userId)
            ->with('product')
            ->latest()
            ->get();
    }

    public function orderBuy($userId)
    {
        return $this->model->where('buyer_id', $userId)
            ->latest()
            ->get();
    }

    public function orderSell($userId)
    {
        return $this->model->where('seller_id', $userId)
            ->latest()
            ->get();

    }



}
