<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function model()
    {
        return Product::class;
    }

    public function index()
    {
        return $this->model->with('categories', 'brands')->orderBy('created_at', 'desc')->get();
    }

    public function productDetail($slug)
    {
        return $this->model->with( 'categories', 'productImages', 'users')->where('slug', $slug)->first();
    }

    public function store($input)
    {
        return $this->model->create($input);
    }

    public function show($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function update($id, $input)
    {
        return $this->model->where('id', $id)->update($input);
    }

    public function updateBySlug($input, $slug)
    {
        return $this->model->where('slug', $slug)->update($input);
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }

    public function productSearch($location, $keyword)
    {
        return $this->model
            ->when($location, function ($query, $location) {
                return $query->where('location', 'LIKE', "%{$location}%");
            })
            ->when($keyword, function ($query, $keyword) {
                return $query->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function getFilteredProducts($filters)
    {
        $query = $this->model->newQuery(); // Khởi tạo query builder từ model

// Lọc theo khu vực
        if (!empty($filters['location'])) {
            $query->where('location', $filters['location']);
        }

// Lọc theo danh mục
        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

// Lọc theo giá
        if (!empty($filters['min_price']) && !empty($filters['max_price'])) {
            $query->whereBetween('price', [$filters['min_price'], $filters['max_price']]);
        } elseif (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        } elseif (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

// Tìm kiếm theo từ khóa
        if (!empty($filters['q'])) {
            $query->where('name', 'LIKE', '%' . $filters['q'] . '%');
        }

        // Lọc theo khu vực
        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

// Phân trang kết quả
        return $query->paginate(12);

    }

    public function homeExchange()
    {
        return $this->model->with('categories', 'brands')->where('status', 'accepted')->orderBy('created_at', 'desc')->paginate(6);
    }

    public function productNews($getNewsByStatus = null, $user)
    {

        if ($getNewsByStatus == 'accepted') {
            return $this->model->with('categories', 'brands')->where('status', 'accepted')
            ->where('user_id', $user)
            ->orderBy('start_date', 'asc')->get();
        } elseif ($getNewsByStatus == 'pending') {
            return $this->model->with('categories', 'brands')->where('status', 'pending')
                ->where('user_id', $user)
                ->orderBy('start_date', 'asc')->get();
        } elseif ($getNewsByStatus == 'rejected') {
            return $this->model->with('categories', 'brands')->where('status', 'rejected')
                ->where('user_id', $user)
                ->orderBy('start_date', 'asc')->get();
        }
        return $this->model->with('categories', 'brands')->where('status', 'accepted')
            ->where('user_id', $user)
            ->orderBy('start_date', 'asc')->get();

    }

    public function countProduct($user)
    {
        return $this->model->where('user_id', $user)
            ->selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accept_count,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as reject_count
        ")
        ->first();
    }

}
