<?php

namespace App\Http\Controllers\Exchange;

use App\Enums\Product;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Vinkla\Hashids\Facades\Hashids;


class UserController extends Controller
{
    private $userRepository;

    public function __construct(

        UserRepository $userRepository
    )
    {
        $this->userRepository = $userRepository;
    }

    public function profile()
    {
        $idUser = Auth::user()->id;
        $dataUser = $this->userRepository->showInfo($idUser);
        return view('exchange.profile.update-info', compact('dataUser'));
    }

    public function update(Request $request, $userIdHash)
    {
        if (empty($userIdHash)) {
            abort(404);
        }

        $input = $request->except(['_token']);
        $this->userRepository->update($input, $userIdHash);
        return back()->with('success', __('Information has been updated successfully!'));
    }

    public function changePassword()
    {
        $idUser = Auth::user()->id;
        $dataUser = $this->userRepository->showInfo($idUser);
        return view('exchange.profile.change-password', compact('dataUser'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed',
        ]);

        if (!Hash::check($request->old_password, auth()->user()->password)) {
            return back()->with("error", __("Old passwords do not match!"));
        }

        User::whereId(auth()->user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with("success", __("Password successfully changed!"));
    }

    public function profilePost($encodedId)
    {
        $id = Hashids::decode($encodedId)[0] ?? null;
        if (!$id) abort(404);

        $user = $this->userRepository->showInfo($id);

        $activeProducts = $user->products()->where('status', Product::STATUS_POST_ACCEPT)->get();
        $soldProducts = $user->products()->where('status', Product::STATUS_POST_HIDDEN)->get();

        return view('exchange.profile.info', compact('user', 'activeProducts', 'soldProducts'));
    }

}
