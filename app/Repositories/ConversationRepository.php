<?php

namespace App\Repositories;

use App\Models\Conversation;

class ConversationRepository extends BaseRepository
{
    public function model()
    {
        return Conversation::class;
    }

    public function listChat($user)
    {
        return $this->model->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with(['buyer', 'seller', 'messages' => function ($q) {
                $q->latest()->limit(1); // chỉ lấy tin nhắn gần nhất cho preview
            }])
            ->orderByDesc(function ($query) {
                $query->select('created_at')
                    ->from('chattings')
                    ->whereColumn('conversation_id', 'conversations.id')
                    ->latest()
                    ->limit(1);
            })
            ->get();
    }

    public function getConversation($product, $user)
    {
        return $this->model->where('product_id', $product->id)
            ->where(function ($q) use ($user) {
                $q->where('buyer_id', $user->id)
                    ->orWhere('seller_id', $user->id);
            })
            ->first();
    }

    public function createConversation($data)
    {
        return $this->model->create($data);
    }

    public function getByUser($user)
    {
        return $this->model->where('buyer_id', $user->id)
            ->orWhere('seller_id', $user->id)
            ->with([
                'buyer',
                'seller',
                'product',
                'messages' => fn($q) => $q->latest()->limit(1)
            ])
            ->get()
            ->sortByDesc(function ($conv) {
                return optional($conv->messages->first())->created_at ?? $conv->created_at;
            })
            ->values();
    }

    public function show($id)
    {
        return $this->model->where('is', $id)->first();
    }


}
