<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Facades\App;

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

    public function productCategory($category)
    {
        return $this->model->with('categories', 'brands')->where('category', $category)->orderBy('created_at', 'desc')->paginate(10);
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

    public function productFilter($category, $filters = [])
    {
        $query = $category->products();

        if (!empty($filters['location'])) {
            $query->where('location', 'LIKE', "%{$filters['location']}%");
        }

        if (!empty($filters['condition'])) {
            $query->where('condition', $filters['condition']);
        }

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', "%{$filters['keyword']}%")
                    ->orWhere('description', 'LIKE', "%{$filters['keyword']}%");
            });
        }

        if (!empty($filters['sort'])) {
            if ($filters['sort'] === 'price_asc') {
                $query->orderBy('price', 'asc');
            } elseif ($filters['sort'] === 'price_desc') {
                $query->orderBy('price', 'desc');
            } else {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        return $query->paginate(12)->withQueryString();
    }

    public function productSearch( $keyword)
    {
        return $this->model
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
        return $this->model->with('categories', 'brands')
            ->where('is_sold', \App\Enums\Product::PRODUCT_IN_STOCK)
            ->where('status', \App\Enums\Product::STATUS_POST_ACCEPT)
            ->orderBy('created_at', 'desc') ->take(9)->get();
    }

    public function loadMore($page)
    {
        return $this->model->where('status', \App\Enums\Product::STATUS_POST_ACCEPT)
            ->orderBy('created_at', 'desc')
            ->paginate(6, ['*'], 'page', $page);
    }

    public function recommend($excludeIds)
    {
        return $this->model->with('categories', 'brands')->whereNotIn('id', $excludeIds)
            ->inRandomOrder()
            ->take(8)
            ->get();
    }

    public function productPosts($getPostsByStatus = null, $user, $keyword = null)
    {
        $query = $this->model->with('categories', 'brands')
            ->where('user_id', $user)
            ->orderBy('start_date', 'asc');

        // Lọc theo trạng thái
        if ($getPostsByStatus == 'accepted') {
            $query->where('status', 'accepted');
        } elseif ($getPostsByStatus == 'pending') {
            $query->where('status', 'pending');
        } elseif ($getPostsByStatus == 'rejected') {
            $query->where('status', 'rejected');
        } elseif ($getPostsByStatus == 'hidden') {
            $query->where('status', 'hidden');
        }
        else {
            $query->where('status', 'accepted');
        }

        // Lọc theo từ khóa (nếu có)
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        return $query->paginate(5); // Phân trang 5 sản phẩm mỗi trang
    }

    public function countProduct($user)
    {
        return $this->model->where('user_id', $user)
            ->selectRaw("
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = 'accepted' THEN 1 ELSE 0 END) as accept_count,
            SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as reject_count,
            SUM(CASE WHEN status = 'hidden' THEN 1 ELSE 0 END) as hidden_count
        ")
        ->first();
    }

}
