<?php

namespace App\Repositories;

use App\Models\CategoryProduct;

class CategoryProductRepository extends BaseRepository
{
    public function model()
    {
        return CategoryProduct::class;
    }

    public function index()
    {
        return $this->model->orderBy('created_at', 'desc')->get();
    }

    public function store($input)
    {
        return $this->model->create($input);
    }

    public function show($id)
    {
        return $this->model->where('id', $id)->first();
    }

    public function update($input, $id)
    {
        return $this->model->where('id', $id)->update($input);
    }

    public function destroy($id)
    {
        return $this->model->where('id', $id)->delete();
    }


    public function productCategory($slug)
    {
        return $this->model
            ->where('slug', $slug)
            ->with(['products' => function ($query) {
                $query->where('status', 'accepted');
            }])
            ->firstOrFail();
    }


}
