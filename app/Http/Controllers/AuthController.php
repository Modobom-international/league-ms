<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Enums\Utility;
use App\Models\User;
use App\Enums\Title;
use App\Enums\Group;
use App\Events\MessageSent;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use App\Repositories\GroupUserRepository;
use App\Repositories\GroupRepository;
use App\Repositories\RankingRepository;
use App\Repositories\UserLeagueRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Hash;
use Session;

class  AuthController extends Controller
{
    protected $groupUserRepository;
    protected $groupRepository;
    protected $rankingRepository;
    protected $userLeagueRepository;
    protected $utility;

    public function __construct(
        GroupUserRepository $groupUserRepository,
        GroupRepository $groupRepository,
        RankingRepository $rankingRepository,
        UserLeagueRepository $userLeagueRepository,
        Utility $ultity
    ) {
        $this->groupUserRepository = $groupUserRepository;
        $this->groupRepository = $groupRepository;
        $this->rankingRepository = $rankingRepository;
        $this->userLeagueRepository = $userLeagueRepository;
        $this->utility = $ultity;
    }

    public function login()
    {
        return view('auth.login');
    }

    public function customLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->put('email', $credentials['email']);
            if (Auth::user()->role == Role::ADMIN) {
                return redirect('dashboard');
            } else {
                if ($request->get('return_url')) {
                    $return_url = $request->get('return_url');
                    return redirect($return_url);
                }

                return redirect('/');
            }
        } else {
            return back()->withErrors([
                'custom' => __('Email or Password is wrong!')
            ]);
        }
    }

    public function logout()
    {
        Session::flush();

        Auth::guard('web')->logout();
        return redirect('login');
    }

    public function registerUser()
    {
        return view('auth.registerUser');
    }

    public function storeUser(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => Role::USER,
            'title' => Title::USER,
        ]);

        $carbonNow = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $timeEnd = $carbonNow->addMinutes(60)->format('Y-m-d H:i:s');


        $dataRanking = [
            'user_id' => $user->id,
            'points' => 0,
            'places' => 0,
            'places_old' => 0
        ];
        $this->rankingRepository->create($dataRanking);

        Auth::loginUsingId($user->id);
        if ($user->role == 'user') {
            return \redirect()->route('home');
        } else {
            return \redirect()->route('dashboard');
        }
    }

    public function showLoginForm()
    {
        return view('exchange.login');
    }

    public function exchangeLogin(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');
        if (Auth::guard('canstum')->attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Đảm bảo session hoạt động ổn định

            if ($request->filled('return_url')) {
                return redirect($request->get('return_url'));
            }

            $user = Auth::guard('canstum')->user();

            return redirect()->route('exchange.home')->with("success", __("Login successfully!"));
        }

        return back()->withErrors([
            'custom' => __('Email or Password is wrong!')
        ])->withInput($request->only('email', 'remember'));
    }



    public function showRegisterForm()
    {
        return view('exchange.register');
    }

    public function exchangeRegister(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'role' => Role::USER,
            'title' => Title::USER,
        ]);

        $carbonNow = Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $timeEnd = $carbonNow->addMinutes(60)->format('Y-m-d H:i:s');


        $dataRanking = [
            'user_id' => $user->id,
            'points' => 0,
            'places' => 0,
            'places_old' => 0
        ];
        $this->rankingRepository->create($dataRanking);

        Auth::loginUsingId($user->id);
        if ($user->role == 'user') {
            return \redirect()->route('exchange.home');
        } else {
            return \redirect()->route('dashboard');
        }
    }

    public function profile()
    {
        return view('page.user.profile');
    }

    public function joinGroup(Request $request)
    {
        $dataGroup = $request->get('g_i');
        if (empty($dataGroup)) {
            return 'fail';
        }

        $getGroup = $this->groupRepository->getGroupById($dataGroup);
        if (empty($getGroup)) {
            return 'fail';
        }

        $user = Auth::user();
        if ($getGroup->status == Group::STATUS_PRIVATE) {
            $group_users = $this->groupUserRepository->checkJoinedGroup($user->id, $getGroup->id);
            if (empty($group_users)) {
                $data = [
                    'group_id' => $getGroup->id,
                    'user_id' => $user->id,
                    'status_request' => Group::STATUS_WAITING
                ];
                $this->groupUserRepository->create($data);

                return 'wait';
            } else {
                if ($group_users->status_request == Group::STATUS_REJECTED) {
                    return 'reject';
                }
            }
        } else {
            if (empty($group_users)) {
                $data = [
                    'group_id' => $getGroup->id,
                    'user_id' => $user->id,
                    'status_request' => Group::STATUS_ACCEPTED
                ];
                $this->groupUserRepository->create($data);

                return 'success';
            } else {
                if ($group_users->status_request == Group::STATUS_REJECTED) {
                    return 'reject';
                }
            }
        }

        return 'success';
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
        $message = $request->get('message');
        $group_id = $request->get('g_i');

        if ($message == null or $group_id == null) {
            abort(403);
        }

        $isJoined = $this->groupUserRepository->checkJoinedGroup($user->id, $group_id);
        if (empty($isJoined)) {
            abort(403);
        }

        $message = $user->messages()->create([
            'message' => $message,
            'group_id' => $group_id
        ]);

        broadcast(new MessageSent($user, $message, $group_id))->toOthers();

        return ['status' => 'sent'];
    }


}
