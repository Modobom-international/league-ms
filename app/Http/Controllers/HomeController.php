<?php

namespace App\Http\Controllers;

use App\Enums\Group;
use App\Enums\Utility;
use App\Events\LiveScore;
use App\Jobs\UpdateLeagueRankingJob;
use App\Jobs\UpdateResultJob;
use App\Models\Partner;
use App\Models\Ranks;
use App\Models\UserLeague;
use App\Repositories\CategoryPostRepository;
use App\Repositories\GroupRepository;
use App\Repositories\GroupTrainingRepository;
use App\Repositories\LeagueRepository;
use App\Repositories\PostRepository;
use App\Repositories\UserLeagueRepository;
use App\Repositories\UserRepository;
use App\Repositories\GroupUserRepository;
use App\Repositories\MessageRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\ProductRepository;
use App\Repositories\RankingRepository;
use App\Repositories\ScheduleRepository;
use App\Repositories\RefereeRepository;
use App\Repositories\ResultRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Config;
use Illuminate\Support\Facades\DB;
use Session;

class HomeController extends Controller
{
    protected $userLeagueRepository;
    protected $leagueRepository;
    protected $userRepository;
    protected $groupRepository;
    protected $groupUserRepository;
    protected $messageRepository;
    protected $rankingRepository;
    protected $productRepository;
    protected $notificationRepository;
    protected $scheduleRepository;
    protected $utility;
    protected $groupTraining;
    protected $refereeRepository;
    protected $resultRepository;
    protected $postRepository;
    protected $categoryPostRepository;

    public function __construct(
        UserLeagueRepository $userLeagueRepository,
        LeagueRepository $leagueRepository,
        UserRepository $userRepository,
        GroupRepository $groupRepository,
        GroupUserRepository $groupUserRepository,
        MessageRepository $messageRepository,
        RankingRepository $rankingRepository,
        ProductRepository $productRepository,
        NotificationRepository $notificationRepository,
        ScheduleRepository $scheduleRepository,
        Utility $ultity,
        GroupTrainingRepository $groupTraining,
        RefereeRepository $refereeRepository,
        ResultRepository $resultRepository,
        PostRepository $postRepository,
        CategoryPostRepository $categoryPostRepository
    ) {
        $this->userLeagueRepository = $userLeagueRepository;
        $this->leagueRepository = $leagueRepository;
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
        $this->groupUserRepository = $groupUserRepository;
        $this->messageRepository = $messageRepository;
        $this->rankingRepository = $rankingRepository;
        $this->productRepository = $productRepository;
        $this->notificationRepository = $notificationRepository;
        $this->scheduleRepository = $scheduleRepository;
        $this->utility = $ultity;
        $this->groupTraining = $groupTraining;
        $this->refereeRepository = $refereeRepository;
        $this->resultRepository = $resultRepository;
        $this->postRepository = $postRepository;
        $this->categoryPostRepository = $categoryPostRepository;
    }

    public function viewHome()
    {
        $totalGroup = $this->groupRepository->count();
        $totalLeague = $this->leagueRepository->count();
        $totalView = strtotime(date('Y-m-d H:i:s')) / 1242222;
        $listLeague = $this->leagueRepository->listLeagueHomePage();
        $listRank = $this->rankingRepository->listRankHomePage();
        $listPosts = $this->postRepository->listPostLimit();


        return view('page.homepage', compact('totalGroup', 'totalLeague', 'totalView', 'listLeague', 'listRank', 'listPosts'));
    }

    public function viewSearch(Request $request)
    {
        $search = $request->get('search');
        $isList = false;
        $listLeagues = [];
        if ($search) {
            $listLeagues = $this->leagueRepository->getLeagueBySearch($search);
            if (count($listLeagues) > 0) {
                $isList = true;
            }
        }

        return view('page.search', compact('listLeagues', 'search', 'isList'));
    }

    public function viewShop()
    {
        $products = $this->productRepository->get();
        return view('page.shop.index', compact('products'));
    }

    public function viewAbout()
    {
        $listPosts = $this->postRepository->listPostLimit();
        return view('page.about', compact('listPosts'));
    }

    public function viewPricing()
    {
        return view('page.pricing');
    }

    public function viewPrivacy()
    {
        return view('page.privacy');
    }

    public function viewTermAndConditions()
    {
        return view('page.term');
    }

    public function viewRanking(Request $request)
    {
        $ranking = $this->rankingRepository->getTop();
        $listRankings = $this->utility->paginate($ranking, 15);
        $listRank = $this->rankingRepository->listRankHomePage();

        return view('page.ranking.index', compact('ranking', 'listRankings', 'listRank'));
    }

    public function viewInforPlayer($id)
    {
        $user_id = $this->utility->decode_hash_id($id);
        $users = $this->userRepository->getById($user_id);

        if (empty($users)) {
            abort(404);
        }

        $user = $this->userRepository->getInformationUser($user_id);
        $groups = $this->groupUserRepository->getGroupByUserId($user_id);
        $leagues = $this->userLeagueRepository->getLeagueByUserId($user_id);

        foreach ($groups as $group) {
            $totalMembers = $this->groupUserRepository->countMembers($group->id);
            $group->totalMembers = $totalMembers;
        }

        return view('page.user.player', compact('user', 'groups', 'leagues'));
    }

    //league
    public function listLeague(Request $request)
    {
        $getLeagueByState = $request->get('state');
        $getLeague = $this->leagueRepository->getLeagueHome($getLeagueByState);
        $listLeagues = $this->utility->paginate($getLeague, 10);

        return view('page.league.index', compact('listLeagues'));
    }

    public function showInfo($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $dataLeague = $this->leagueRepository->show($slug);

        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }

        $countMatch = count($leagueInfor->schedule) ?? 0;
        $countPlayer = count($leagueInfor->userLeagues) ?? 0;
        $firstGroup = reset($groupSchedule);
        if (is_array($firstGroup)) {
            $firstThreeSchedules = array_slice($firstGroup, 0, 3);
        } else {
            $firstThreeSchedules = [];
        }

        // ===== XỬ LÝ RANKING =====
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);

        return view('page.league.show', [
            'champion' => $rankingInfo['champion'],
            'topRank' => $rankingInfo['topRank'],
            'bottomRank' => $rankingInfo['bottomRank'],
            'ranking' => $rankingInfo['ranking'],
            'hasEnded' => $hasEnded,
            'firstThreeSchedules' => $firstThreeSchedules,
            'countPlayer' => $countPlayer,
            'countMatch' => $countMatch,
            'leagueInfor' => $leagueInfor,
            'getListLeagues' => $getListLeagues,
            'groupSchedule' => $groupSchedule,
            'dataLeague' => $dataLeague,
        ]);
    }

    public function showPlayer($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();
        //rank
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'listLeagues', 'getListLeagues'));
    }

    public function showResult($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }

        //rank
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'listLeagues', 'groupSchedule', 'getListLeagues'));
    }

    public function showBracket($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $listSchedules = $this->scheduleRepository->getScheduleByLeagueOrderByMatch($leagueInfor->id);
        $totalMembers = $this->userLeagueRepository->countTotalMembersInLeague($leagueInfor->id);
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $groupRound = $listSchedules->groupBy('round');

        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }

        //rank
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'listLeagues', 'groupSchedule', 'listSchedules', 'groupRound', 'getListLeagues'));
    }

    public function showSchedule($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }

        //ranking
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'listLeagues', 'groupSchedule', 'getListLeagues'));
    }

    public function showListRegister($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();

        //countRegister
        $registrations = $this->userLeagueRepository->countRegistration($leagueInfor->id);
        $pendingCount = $this->userLeagueRepository->pendingCount($leagueInfor->id);
        $acceptedCount = $this->userLeagueRepository->acceptedCount($leagueInfor->id);

        //ranking
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'listLeagues', 'getListLeagues', 'registrations','pendingCount', 'acceptedCount'));
    }

    public function showGeneralNews($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $countMatch = count($leagueInfor->schedule) ?? 0;
        $countPlayer = count($leagueInfor->userLeagues) ?? 0;
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();

        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }
        $firstGroup = reset($groupSchedule);
        $firstThreeSchedules = is_array($firstGroup) ? array_slice($firstGroup, 0, 3) : [];

        //ranking
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;
        // ===== XỬ LÝ RANKING =====

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        $topRank = $rankingInfo['topRank'];
        $bottomRank = $rankingInfo['bottomRank'];
        $ranking = $rankingInfo['ranking'];

        return view('page.league.show', compact(
            'champion',
            'topRank',
            'bottomRank',
            'hasEnded',
            'leagueInfor',
            'listLeagues',
            'getListLeagues',
            'countMatch',
            'countPlayer',
            'firstThreeSchedules',
            'ranking' // gửi ranking ra view
        ));
    }

    public function showRank($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $listLeagues = $this->leagueRepository->getLeagueHome();
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }

        // ===== XỬ LÝ RANKING =====
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;

        // ✅ Dùng helper mới
        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        $topRank = $rankingInfo['topRank'];
        $bottomRank = $rankingInfo['bottomRank'];
        $ranking = $rankingInfo['ranking'];

        return view('page.league.show', compact('champion','topRank', 'bottomRank','ranking','hasEnded','leagueInfor', 'listLeagues', 'groupSchedule', 'getListLeagues'));
    }

    public function registerLeague($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $dataLeague = $this->leagueRepository->show($slug);
        $user = $this->userRepository->showInfo(Auth::user()->id);
        $partners = $user->partner;
        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }
        $currentDate = now()->format('Y-m-d');
        $hasEnded = $currentDate > $leagueInfor->end_date;
        // Lấy nhà vô địch nếu giải đã kết thúc
        // ✅ Dùng helper mới

        $rankingInfo = getLeagueRankingInfo($leagueInfor, $hasEnded);
        $champion = $rankingInfo['champion'];
        return view('page.league.show', compact('champion','hasEnded','leagueInfor', 'getListLeagues', 'groupSchedule','dataLeague', 'partners'));
    }

    public function formRegisterLeague($slug)
    {
        $leagueInfor = $this->leagueRepository->showInfo($slug);
        $getListLeagues = $this->leagueRepository->getListLeagues();
        $dataLeague = $this->leagueRepository->show($slug);
        $user = $this->userRepository->userInfo(Auth::user()->id);
        $partners = $user->partnerInLeague;
        $groupSchedule = [];
        foreach ($leagueInfor->schedule as $schedule) {
            $groupSchedule[$schedule['round']][] = $schedule;
        }
        return view('page.league.register-league', compact('leagueInfor', 'getListLeagues', 'groupSchedule','dataLeague', 'partners'));
    }

    public function storePartnerAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
        ]);

        $input['created_by_user_id'] = Auth::user()->id;
        if (isset($input['avatar'])) {
            $img = $this->utility->saveAvatarPartner($input);
            if ($img) {
                $path = 'images/upload/partner/' . $input['avatar']->getClientOriginalName();
                $input['avatar'] = $path;
            }
        }

        $partner = Partner::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'avatar' => $input['avatar'] ?? "",
            'created_by_user_id' => $input['created_by_user_id']
            // thêm avatar nếu có
        ]);

        return back()->with('message', 'Create partner success');
    }

    public function saveRegisterLeague(Request $request)
    {
        $input = $request->except(['_token']);
        $league = $this->leagueRepository->getLeagueById($input['league_id']);

        if ($league->type_of_league == 'doubles')
        {

           $user = $this->userRepository->showInfo($input['user_id']);
           if(empty($user->partner))
           {
               return back()->with('error', 'You must have a partner, please create one');
           }

            // Kiểm tra đã tồn tại bản ghi với partner cho cùng giải chưa
            $hasPartnerInThisLeague = $this->userLeagueRepository->hasPartnerInThisLeague($input['user_id'], $input['league_id']);
            if ($hasPartnerInThisLeague) {
                return back()->with('error', 'You already have a partner registered for this tournament.');
            }

            if($input['partner_id'] == null)
            {
                return back()->with('error', 'You have not chosen a partner yet for this tournament.');
            }
        }

        $startDate = strtotime($request['start_date']);
        $dateCurrent =  strtotime(date("Y-m-d"));

        if ($dateCurrent >= $startDate) {
            abort(404);
        }
        $checkExistRegister = $this->userLeagueRepository->checkRegister($input['league_id'], $input['user_id']);
        if($checkExistRegister) {
            return back()->with('error', 'You have registered for the tournament. ');
        }
        $this->userLeagueRepository->store($input);

        return redirect()->route('showListRegister.info', ['slug' => $league->slug])->with('message', 'You have registered for the tournament success.');
//        return re('page.league.show', compact('hasEnded','leagueInfor', 'getListLeagues', 'groupSchedule','dataLeague', 'partners'))->with('message', 'You have registered for the tournament success.');
    }

    public function readNotification()
    {
        if (!Auth::check()) {
            abort(405);
        }

        $user_id = Auth::user()->id;
        $this->notificationRepository->updateReadNotification($user_id);
        $key = 'notification_next_match_' . $user_id;
        $getNotification = $this->notificationRepository->getNotificationByUser($user_id);
        Cache::set($key, $getNotification);
    }

    public function searchLeague(Request $request)
    {
        $query = $request->input('query');
        $sort = $request->input('sort');
        $format = $request->input('format');

        $getLeague = $this->leagueRepository->searchLeague($query, $sort, $format);
        $listLeagues = $this->utility->paginate($getLeague, 10);

        return view('page.league.search-result', compact('listLeagues'));
    }

    public function viewMatch()
    {
        $getLeaguesMatch = $this->leagueRepository->getLeagueMath();
        $listMatches = $this->utility->paginate($getLeaguesMatch, 5);

        return view('page.match-center.index', compact('listMatches'));
    }

    public function live($slug)
    {
        $league = $this->leagueRepository->getLiveLeague($slug);
        $hours = date('H:i');
        $date = date('Y-m-d');
        $listSchedules = [];
        foreach ($league->schedule as $schedule) {
            if (strtotime($schedule->date) == strtotime($date)) {
                $listSchedules[] = $schedule;
            }
        }

        $listSchedules = collect($listSchedules)->sortBy('match');
        return view('page.match-center.show-live', compact('league', 'listSchedules'));
    }

    public function liveScore(Request $request)
    {
        $scheduleId = $request->get('s_i');
        if (empty($scheduleId)) {
            abort(404);
        }

        $decode = $this->utility->decode_hash_id($scheduleId);
        $getSchedule = $this->scheduleRepository->getScheduleById($decode);

        if (empty($getSchedule)) {
            abort(404);
        }
        $getReferee = $this->refereeRepository->getRefereeByUserId($getSchedule->id, Auth::user()->id);
        if (empty($getReferee)) {
            $listTitle = explode(',', Auth::user()->title);
            if (in_array('referee', $listTitle)) {
                $dataReferee = [
                    'user_id' => Auth::user()->id,
                    'schedule_id' => $getSchedule->id,
                ];
                $this->refereeRepository->create($dataReferee);
            } else {
                abort(403);
            }
        }

        if ($getSchedule->result_team_1 == 2 || $getSchedule->result_team_2 == 2) {
            if ($getSchedule->result_team_1 > $getSchedule->result_team_2) {
                $getSchedule->winner_team_id = $getSchedule->player1_team_1;
            } elseif ($getSchedule->result_team_2 > $getSchedule->result_team_1) {
                $getSchedule->winner_team_id = $getSchedule->player1_team_2;
            }

            $getSchedule->save();
            $typeLive = 'end';
            $setLive = null;
            $scoreT1Live = null;
            $scoreT2Live = null;
        } else {
            $typeLive = 'live';
            $result = $getSchedule->result_team_1 . '-' . $getSchedule->result_team_2;
            switch ($result) {
                case '1-0':
                    $setLive = 2;
                    $scoreT1Live = $getSchedule->set_2_team_1;
                    $scoreT2Live = $getSchedule->set_2_team_2;
                    break;
                case '0-1':
                    $setLive = 2;
                    $scoreT1Live = $getSchedule->set_2_team_1;
                    $scoreT2Live = $getSchedule->set_2_team_2;
                    break;
                case '1-1':
                    $setLive = 3;
                    $scoreT1Live = $getSchedule->set_3_team_1;
                    $scoreT2Live = $getSchedule->set_3_team_2;
                    break;
                default:
                    $setLive = 1;
                    $scoreT1Live = $getSchedule->set_1_team_1;
                    $scoreT2Live = $getSchedule->set_1_team_2;
            }
        }

            return view('page.match-center.live-score', compact('getSchedule', 'typeLive', 'setLive', 'scoreT1Live', 'scoreT2Live'));
    }

    public function storeScore(Request $request)
    {
        $type = $request->get('type');
        $score = $request->get('score');
        $team = $request->get('team');
        $set = $request->get('set');
        $s_i = $request->get('s_i');
        $result = $request->get('result');

        if (empty($type)) {
            abort(404);
        }

        $decode = $this->utility->decode_hash_id($s_i);
        $checkReference = $this->refereeRepository->getRefereeByUserId(Auth::user()->id, $decode);
        if (empty($checkReference)) {
            abort(404);
        }

        $getSchedule = $this->scheduleRepository->getScheduleById($decode);

        if (empty($getSchedule)) {
            abort(404);
        }

        $currentTeam = explode('-', $team);
        $columnSet = 'set_' . $set . '_team_' . $currentTeam[1];
        $dataUpdate = [
            $columnSet => $score,
        ];

        if ($result == 'end') {
            $resultT1 = $getSchedule->result_team_1;
            $resultT2 = $getSchedule->result_team_2;
            if (empty($resultT1)) {
                $resultT1 = 0;
            }

            if (empty($resultT2)) {
                $resultT2 = 0;
            }

            if ($team == 'team-1') {
                $resultT1 = $resultT1 + 1;
            } else {
                $resultT2 = $resultT2 + 1;
            }

            $dataUpdate['result_team_1'] = $resultT1;
            $dataUpdate['result_team_2'] = $resultT2;

            broadcast(new LiveScore($getSchedule->id, $team, $score, $set, $resultT1, $resultT2));
        } else {
            broadcast(new LiveScore($getSchedule->id, $team, $score, $set));
        }


        UpdateResultJob::dispatch($decode, $type, $score, $set, $this->scheduleRepository, $this->resultRepository, $request->get('new_score_player'), $request->get('player'))->onQueue('update-result');

        $this->scheduleRepository->updateScoreLiveById($getSchedule->id, $dataUpdate);

        return 'success';
    }

//    private function updateRoundRobinRanking($leagueId)
//    {
//        // Lấy danh sách các đội tham gia giải đấu này
//        $teams = $this->scheduleRepository->getTeamsByLeagueId($leagueId);
//
//        // Tính điểm cho từng đội dựa trên kết quả các trận đấu
//        foreach ($teams as $team) {
//            // Tính điểm, số trận thắng, thua, hòa, và các thống kê khác
//            $wins = $this->scheduleRepository->getWinsForTeam($team->id, $leagueId);
//            $losses = $this->scheduleRepository->getLossesForTeam($team->id, $leagueId);
//            $points = $wins * 3 + $losses * 0; // 3 điểm cho mỗi trận thắng
//
//            // Cập nhật điểm vào bảng xếp hạng
//            $this->rankRepository->updateOrCreate(
//                ['team_id' => $team->id, 'league_id' => $leagueId],
//                ['points' => $points]
//            );
//        }
//    }
//
//// Cập nhật bảng xếp hạng cho vòng loại
//    private function updateKnockoutRanking($leagueId, $winnerTeamId)
//    {
//        // Lấy thông tin đội thắng
//        $winnerTeam = $this->scheduleRepository->getTeamById($winnerTeamId);
//
//        // Cập nhật đội thắng vào bảng xếp hạng (có thể sắp xếp theo thứ tự)
//        $this->rankRepository->updateOrCreate(
//            ['team_id' => $winnerTeam->id, 'league_id' => $leagueId],
//            ['rank' => 'Winner', 'points' => 100] // Ví dụ: 100 điểm cho đội vô địch
//        );
//    }

    //group
    public function listGroup()
    {
        $getGroup = $this->groupRepository->getGroupWithStatus();
        $listGroup = $this->utility->paginate($getGroup, 10);

        return view('page.group.index', compact('listGroup'));
    }

    public function changeLocate($locale)
    {
        if (in_array($locale, Config::get('app.locales'))) {
            Session::put('locale', $locale);
        }

        return redirect()->back();
    }

    public function checkGroupJoin(Request $request)
    {
        $nameGroup = $request->input('group');
        if (empty($nameGroup)) {
            abort(404);
        }

        $getGroup = $this->groupRepository->getGroupByName($nameGroup);

        if (empty($getGroup)) {
            abort(404);
        }

        $userId = Auth::id(); // Lấy user đang đăng nhập

        $isJoined = DB::table('group_users')
            ->where('group_id', $getGroup->id)
            ->where('user_id', $userId)
            ->where('status_request', Group::STATUS_ACCEPTED)
            ->exists(); // Kiểm tra user có trong nhóm không

        return response()->json(['joined' => $isJoined]);
    }

    public function detailGroup(Request $request)
    {
        $nameGroup = $request->get('g_i');
        if (empty($nameGroup)) {
            abort(404);
        }

        $getGroup = $this->groupRepository->getGroupByName($nameGroup);
        if (empty($getGroup)) {
            abort(404);
        }

        $isJoined = false;
        $members = $this->groupUserRepository->getMembersByGroupId($getGroup->id);
        $listId = [];
        foreach ($members as $member) {
            $listId[] = $member->user_id;
        }

        $listRankings = $this->rankingRepository->getRankingListUsers($listId);

        if (Auth::check()) {
            $user = Auth::user();

            $checkJoined = $this->groupUserRepository->checkJoinedGroup($user->id, $getGroup->id);
            if (!empty($checkJoined)) {
                $isJoined = true;
            }
            $messages = $this->messageRepository->getMessagesByGroupId($getGroup->id);

            return view('page.group.detail', compact('getGroup', 'messages', 'members', 'isJoined', 'listRankings'));
        }

        return view('page.group.detail', compact('getGroup', 'members', 'isJoined', 'listRankings'));
    }

    public function groupTraining(Request $request)
    {
        $nameGroup = $request->get('g_i');
        if (empty($nameGroup)) {
            abort(404);
        }
        $listTrainings = $this->groupRepository->getGroupByName($nameGroup);
        if (empty($listTrainings)) {
            abort(404);
        }

        foreach ($listTrainings->group_trainings as $trainings) {
            $listId = json_decode($trainings->members);
            $trainings->isJoin = false;
            $trainings->totalMembers = 0;
            if (!empty($listId)) {
                if (in_array(Auth::user()->id, $listId)) {
                    $trainings->isJoin = true;
                }
                $trainings->totalMembers = count($listId);
            }
        }

        return view('page.group.group-training', compact('listTrainings'));
    }

    public function detailGroupTraining(Request $request)
    {
        $nameGroupTraining = $request->get('g_t');
        if (empty($nameGroupTraining)) {
            abort(404);
        }

        $groupTrainingDetail = $this->groupTraining->getGroupTrainByName($nameGroupTraining);
        if (empty($groupTrainingDetail)) {
            abort(404);
        }

        $listId = json_decode($groupTrainingDetail->members);
        if (!empty($listId)) {
            $listMembers = $this->userRepository->getListMembers($listId);
        } else {
            $listMembers = [];
        }

        return view('page.group.detail-group-train', compact('groupTrainingDetail', 'listMembers'));
    }

    public function joinGroupTraining(Request $request)
    {
        $idTraining = $request->get('g_t');
        if (empty($idTraining)) {
            abort(404);
        }

        $getMembers = $this->groupTraining->getMembersById($idTraining);

        if (empty($getMembers->members)) {
            $dataMembers = [
                'members' => json_encode([Auth::user()->id])
            ];

            $this->groupTraining->updateMembers($idTraining, $dataMembers);
        } else {
            $members = json_decode($getMembers->members, true);
            if (!in_array(Auth::user()->id, $members)) {
                $members[] = Auth::user()->id;
            }

            $dataMembers = [
                'members' => json_encode($members)
            ];

            $this->groupTraining->updateMembers($idTraining, $dataMembers);
        }

        return redirect('training?g_t=' . $getMembers->name);
    }

    public function searchGroup(Request $request)
    {
        $query = $request->input('query');
        $sort = $request->input('sort');
        $status= $request->input('status');

        $getGroup = $this->groupRepository->searchGroup($query, $sort, $status);
        $listGroup = $this->utility->paginate($getGroup, 10);

        return view('page.group.search-result-group', compact('listGroup'));
    }

    public function searchGroupTraining(Request $request)
    {
        $group = $request->get('group');
        $listTrainings = $this->groupRepository->getGroupByName($group);

        if (empty($listTrainings)) {
            abort(404);
        }

        foreach ($listTrainings->group_trainings as $trainings) {
            $listId = json_decode($trainings->members);
            $trainings->isJoin = false;
            $trainings->totalMembers = 0;
            if (!empty($listId)) {
                if (in_array(Auth::user()->id, $listId)) {
                    $trainings->isJoin = true;
                }
                $trainings->totalMembers = count($listId);
            }
        }
        $query = $request->input('query');
        $sort = $request->input('sort');

        $getGroup = $this->groupTraining->searchGroup($query, $sort);
        $listGroup = $this->utility->paginate($getGroup, 10);

        return view('page.group.search-result-group-training', compact('listGroup', 'listTrainings'));
    }

    //news
    public function news()
    {
        $listNews = $this->postRepository->index();
        $categories = $this->categoryPostRepository->index();
        return view('page.post.list', compact('listNews', 'categories'));
    }

    public function newsDetail($slug)
    {
        $newData = $this->postRepository->detailPost($slug);
        $categories = $this->categoryPostRepository->index();

        // Lấy các bài viết liên quan không cần cùng category, chỉ loại trừ bài hiện tại
        $relatedPosts = $this->postRepository->relatedPosts($newData->id);

        return view('page.post.detail', compact('newData', 'categories', 'relatedPosts'));
    }

    public function newsCategory($slug)
    {
        $postCategory = $this->categoryPostRepository->postCategory($slug);
        $categories = $this->categoryPostRepository->index();
        return view('page.post.category-post', compact('postCategory', 'categories'));

    }

    public function searchNews(Request $request)
    {
        $query = $request->input('query');
        $sort = $request->input('sort');
        $categories = $this->categoryPostRepository->index();
        $getPosts = $this->postRepository->searchNews($query, $sort);
        $listNews = $this->utility->paginate($getPosts, 10);

        return view('page.post.search-result', compact('listNews', 'categories'));
    }

}
