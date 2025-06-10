<?php

namespace App\View\Composers;

use Illuminate\View\View;
use App\Repositories\CategoryProductRepository;

class CategoryComposer
{
    protected $categoryRepo;

    public function __construct(CategoryProductRepository $categoryRepo)
    {
        $this->categoryRepo = $categoryRepo;
    }

    public function compose(View $view)
    {
        // Chỉ load khi cần thiết
        $view->with('categories', $this->categoryRepo->index());
    }
}
