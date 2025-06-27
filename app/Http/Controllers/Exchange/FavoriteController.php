<?php

namespace App\Http\Controllers\Exchange;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Product $product)
    {
        $user = Auth::guard('canstum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->hasFavorited($product->id)) {
            $user->favorites()->detach($product->id);
            return response()->json(['favorited' => false]);
        } else {
            $user->favorites()->attach($product->id);
            return response()->json(['favorited' => true]);
        }
    }

    public function index()
    {
        $user = Auth::guard('canstum')->user();
        $favoriteProducts = $user->favorites()->latest()->paginate(10);

        return view('exchange.favorites.index', compact('favoriteProducts'));
    }
}
