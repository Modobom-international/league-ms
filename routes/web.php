<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryPostController;
use App\Http\Controllers\Admin\CategoryProductController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ExchangeController;
use App\Http\Controllers\SocialLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\LeagueController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\GroupController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['cache.notification'])->group(function () {
    Route::get('/', [HomeController::class, 'viewHome'])->name('home');
    Route::get('/tournament-leagues/', [HomeController::class, 'listLeague'])->name('list.league');
    Route::post('/search/', [HomeController::class, 'viewSearch'])->name('search.result');
    Route::get('/search/', [HomeController::class, 'viewSearch'])->name('search');
    Route::get('/about/', [HomeController::class, 'viewAbout'])->name('about');
    Route::get('/privacy/', [HomeController::class, 'viewPrivacy'])->name('privacy');
    Route::get('/term-and-conditions/', [HomeController::class, 'viewTermAndConditions'])->name('term.and.conditions');
    Route::get('/tournament-league/{slug}/', [HomeController::class, 'showInfo'])->name('league.info');
    Route::get('/tournament-league/{slug}/player/', [HomeController::class, 'showPlayer'])->name('leaguePlayer.info');
    Route::get('/tournament-league/{slug}/result/', [HomeController::class, 'showResult'])->name('leagueResult.info');
    Route::get('/tournament-league/{slug}/schedule/', [HomeController::class, 'showSchedule'])->name('leagueSchedule.info');
    Route::get('/tournament-league/{slug}/bracket/', [HomeController::class, 'showBracket'])->name('leagueResult.bracket');
    Route::get('/tournament-league/{slug}/fight-branch/', [HomeController::class, 'showFightBranch'])->name('leagueFightBranch.info');
    Route::get('/tournament-league/{slug}/list-register/', [HomeController::class, 'showListRegister'])->name('showListRegister.info');
    Route::get('/tournament-league/{slug}/general-news/', [HomeController::class, 'showGeneralNews'])->name('showGeneralNews.info');
    Route::get('/tournament-league/{slug}/rank/', [HomeController::class, 'showRank'])->name('showRank.info');
    Route::get('/list-teams/', [HomeController::class, 'listTeam'])->name('list.team');
    Route::get('/group/', [HomeController::class, 'listGroup'])->name('list.group');
    Route::get('/check-group-join', [HomeController::class, 'checkGroupJoin']);
    Route::get('/detail-group/', [HomeController::class, 'detailGroup'])->name('detail.group');
    Route::get('/ranking/', [HomeController::class, 'viewRanking'])->name('ranking');
    Route::get('/match-center/', [HomeController::class, 'viewMatch'])->name('match');
    Route::get('match-center/{slug}', [HomeController::class, 'live'])->name('league.live');
    Route::get('/news/{slug}', [HomeController::class, 'newsDetail'])->name('news-show');
    Route::get('/news', [HomeController::class, 'news'])->name('news');
    Route::get('/news/category/{slug}', [HomeController::class, 'newsCategory'])->name('newsCategory');
    Route::get('/search-news', [HomeController::class, 'searchNews'])->name('searchNews');
    Route::get('/search-league-tour', [HomeController::class, 'searchLeague'])->name('searchLeague');
    Route::get('/search-group', [HomeController::class, 'searchGroup'])->name('searchGroup');
    Route::get('/search-group-training', [HomeController::class, 'searchGroupTraining'])->name('searchGroupTraining');
});

//exchange
Route::get('exchange', [ExchangeController::class, 'index'])->name('exchange.home');
Route::get('exchange/about-us', [ExchangeController::class, 'aboutUs'])->name('exchange.aboutUs');
Route::get('exchange/privacy-policy', [ExchangeController::class, 'privacyPolicy'])->name('exchange.privacyPolicy');
Route::get('exchange/rule', [ExchangeController::class, 'rule'])->name('exchange.rule');
//product

Route::get('/post/{slug}', [ExchangeController::class, 'productDetail'])->name('exchange.productDetail');
Route::get('/category/{slug}', [ExchangeController::class, 'categoryDetail'])->name('exchange.categoryDetail');
Route::get('/search', [ExchangeController::class, 'search'])->name('products.search');
Route::get('/filter-by', [ExchangeController::class, 'filter'])->name('products.searchInProduct');
Route::get('/products/load-more', [ExchangeController::class, 'loadMore'])->name('exchange.loadMore');

Route::middleware(['auth:sanctum'])->group(function () {

});
//profile

Route::get('/login/', [AuthController::class, 'login'])->name('login');
Route::post('/custom-login/', [AuthController::class, 'customLogin'])->name('login.custom');
Route::post('/custom-login-mobile/', [AuthController::class, 'customLogin'])->name('login.custom-mobile');
Route::get('/auth/google/', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback/', [SocialLoginController::class, 'handleGoogleCallback']);
Route::get('/auth/facebook/', [SocialLoginController::class, 'redirectToFacebook'])->name('auth.facebook');
Route::get('/auth/facebook/callback/', [SocialLoginController::class, 'handleFacebookCallback']);
Route::get('/auth/line/', [SocialLoginController::class, 'redirectToLine'])->name('auth.line');
Route::get('/auth/line/callback/', [SocialLoginController::class, 'handleLineCallback']);
Route::get('/auth/apple/', [SocialLoginController::class, 'redirectToApple'])->name('auth.apple');
Route::post('/auth/apple/callback/', [SocialLoginController::class, 'handleAppleCallback']);
Route::get('/register/', [AuthController::class, 'registerUser'])->name('register_user');
Route::post('/register/', [AuthController::class, 'storeUser'])->name('storeUser');
Route::get('/setLocale/{locale}/', [HomeController::class, 'changeLocate'])->name('app.setLocale');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/tournament-league/{slug}/register', [HomeController::class, 'formRegisterLeague'])->name('league.formRegisterLeague');
    Route::get('/tournament-league/{slug}/register-league/', [HomeController::class, 'registerLeague'])->name('registerLeague.info');
    //profile
    Route::get('/profile/{nick_name}/', [ProfileController::class, 'show'])->name('profile.info');
    Route::get('/user-profile/', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/user-profile/{id}/', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/change-password/', [ProfileController::class, 'changePassword'])->name('change-password');
    Route::post('/change-password/', [ProfileController::class, 'updatePassword'])->name('update-password');

    //my league
    Route::get('/my-league/', [ProfileController::class, 'viewMyLeague'])->name('my.league');
    Route::get('/league-joined', [ProfileController::class, 'leagueJoin'])->name('league.leagueJoin');
    Route::get('/league-created', [ProfileController::class, 'leagueCreated'])->name('league.leagueCreate');
    Route::get('/my-league-detail/{slug}', [ProfileController::class, 'detailMyLeague'])->name('my.leagueDetail');
    Route::get('/my-league/{slug}/info/', [ProfileController::class, 'infoMyLeague'])->name('my.infoMyLeague');
    Route::post('/my-league/schedule-robin/{id}', [ProfileController::class, 'updateScheduleRobin'])->name('myLeague.schedule.updateScheduleRobin');
    Route::post('/my-league/schedule-knockout/{id}', [ProfileController::class, 'updateScheduleKnockout'])->name('myLeague.schedule.updateScheduleKnockout');
    Route::post('/update-my-league/{id}', [ProfileController::class, 'updateMyLeague'])->name('my.updateMyLeagueMyLeague');
    Route::get('/auto-create-schedule-league', [ProfileController::class, 'autoCreateMyLeague'])->name('auto.create.myLeague.schedule');
    Route::delete('delete/my-league/{id}', [ProfileController::class, 'deleteMyLeague'])->name('delete.myLeague');
    Route::get('/my-league/{slug}/setting', [ProfileController::class, 'leagueSetting'])->name('league.leagueSetting');
    Route::get('/my-league/{slug}/activity-history', [ProfileController::class, 'leagueActivity'])->name('league.leagueActivity');
    Route::get('/my-league/{slug}/config', [ProfileController::class, 'leagueConfig'])->name('league.leagueConfig');
    Route::get('/my-league/{slug}/activity-status', [ProfileController::class, 'leagueStatus'])->name('league.leagueStatus');
    Route::get('/my-league/{slug}/manager-player', [ProfileController::class, 'leagueManagerPlayer'])->name('league.leagueManagerPlayer');
    Route::get('/my-league/{slug}/manger-schedule', [ProfileController::class, 'leagueSchedule'])->name('league.leagueSchedule');

//my group
    Route::get('/my-group/', [ProfileController::class, 'viewMyGroup'])->name('my.group');
    Route::get('/my-group/{id}/active-user/', [ProfileController::class, 'myGroupActiveUser'])->name('my.myGroupActiveUser');
    Route::get('/my-group/{id}/info/', [ProfileController::class, 'infoMyGroup'])->name('my.infoMyGroup');
    Route::post('/update-my-group/{id}', [ProfileController::class, 'updateMyGroup'])->name('my.updateMyGroup');
    Route::delete('delete/my-group/{id}', [ProfileController::class, 'deleteMyGroup'])->name('delete.myGroup');

    Route::get('/group-joined', [ProfileController::class, 'groupJoin'])->name('group.groupJoin');
    Route::get('/group-created', [ProfileController::class, 'groupCreated'])->name('group.groupCreateByUser');

    Route::post('/register-league/', [HomeController::class, 'saveRegisterLeague'])->name('registerLeague');
    Route::post('/partner/', [HomeController::class, 'storePartnerAjax'])->name('user.create.partner');
    Route::get('/player/{id}/', [HomeController::class, 'viewInforPlayer'])->name('player.info');
    Route::get('/read-notifications/', [HomeController::class, 'readNotification'])->name('read.notification');
    Route::get('/profile/', [AuthController::class, 'profile'])->name('profile');
    Route::get('/join-group/', [AuthController::class, 'joinGroup'])->name('join.group');
    Route::post('/messages/', [AuthController::class, 'sendMessage'])->name('send.message');
    Route::get('/logout/', [AuthController::class, 'logout'])->name('logout');
    Route::get('/training/', [HomeController::class, 'detailGroupTraining'])->name('groupTrain.detail');
    Route::get('/group-training/', [HomeController::class, 'groupTraining'])->name('list.train');
    Route::get('/join-group-training/', [HomeController::class, 'joinGroupTraining'])->name('join.group.training');
    Route::get('/live-score/', [HomeController::class, 'liveScore'])->name('live.score');
    Route::post('/store-score', [HomeController::class, 'storeScore'])->name('store.score');

    //league
    Route::get('/list-league/', [LeagueController::class, 'index'])->name('league.index');
    Route::get('/create-league/', [LeagueController::class, 'create'])->name('league.create');
    Route::get('/get-league/{id}', [LeagueController::class, 'leagueById'])->name('league.leagueById');
    Route::post('/store-league/', [LeagueController::class, 'store'])->name('league.store');
    Route::get('/league/{slug}/', [LeagueController::class, 'show'])->name('league.show');
    Route::get('/edit-league/{slug}/', [LeagueController::class, 'edit'])->name('league.edit');
    Route::get('/delete/{slug}/', [LeagueController::class, 'destroy'])->name('league.delete');
    Route::post('/update-league/{id}/', [LeagueController::class, 'update'])->name('league.update');
    Route::post('/update-player-league', [LeagueController::class, 'updatePlayer'])->name('league.updatePlayer');
    Route::get('/delete-player-league/{id}/', [LeagueController::class, 'destroyPlayer'])->name('league.destroyPlayer');
    Route::get('/active-league/{id}', [LeagueController::class, 'activeLeague'])->name('activeLeague');

    Route::get('/create-tournament', [LeagueController::class, 'createLeague'])->name('league.createTour');
    Route::post('/store-tournament/', [LeagueController::class, 'storeLeagueTour'])->name('league.storeTour');

    //schedule
    Route::get('/list-schedule-league/', [ScheduleController::class, 'league'])->name('schedule.league');
    Route::get('/list-schedule/', [ScheduleController::class, 'index'])->name('schedule.index');
    Route::get('/create-schedule', [ScheduleController::class, 'create'])->name('schedule.create');
    Route::get('/create-schedule-league/{slug}', [ScheduleController::class, 'leagueSchedule'])->name('schedule.leagueSchedule');
    Route::post('/store-schedule/', [ScheduleController::class, 'store'])->name('schedule.store');
    Route::get('/schedule/{id}/', [ScheduleController::class, 'show'])->name('schedule.show');
    Route::get('/edit-schedule/{id}/', [ScheduleController::class, 'edit'])->name('schedule.edit');
    Route::post('/update-schedule/{id}/', [ScheduleController::class, 'updateSchedule'])->name('schedule.update');
    Route::post('/update-result/{id}/', [ScheduleController::class, 'updateResult'])->name('schedule.updateResult');
    Route::get('/result', [ScheduleController::class, 'result'])->name('schedule.result');
    Route::get('/export-schedule/{id}/', [ScheduleController::class, 'exportSchedule'])->name('schedule.export');
    Route::get('/auto-create-league', [ScheduleController::class, 'autoCreateLeague'])->name('auto.create.schedule');

    //group
    Route::get('/list-group/', [GroupController::class, 'index'])->name('group.index');
    Route::get('/group/{id}', [GroupController::class, 'show'])->name('group.show');
    Route::post('/store-group/', [GroupController::class, 'store'])->name('group.store');
    Route::get('/create-group/', [GroupController::class, 'create'])->name('group.create');
    Route::get('/edit-group/{id}/', [GroupController::class, 'edit'])->name('group.edit');
    Route::post('/update-group/{id}/', [GroupController::class, 'update'])->name('group.update');
    Route::get('/delete-group/{id}/', [GroupController::class, 'destroy'])->name('group.delete');
    Route::get('/active-group/{id}', [GroupController::class, 'activeGroup'])->name('activeGroup');
    Route::post('/store-group-training/', [GroupController::class, 'groupTraining'])->name('groupTraining.create');
    Route::get('/list-group-training/', [GroupController::class, 'listGroupTraining'])->name('list.groupTraining');
    Route::get('/edit-group-training/{id}', [GroupController::class, 'editGroupTraining'])->name('edit.groupTraining');
    Route::post('/update-group-training/{id}', [GroupController::class, 'updateGroupTraining'])->name('update.groupTraining');
    Route::get('/delete-group-training/{id}', [GroupController::class, 'deleteGroupTraining'])->name('delete.groupTraining');
    Route::get('/delete-account-apple/', [ProfileController::class, 'deleteAccount'])->name('delete.account.apple');
    Route::get('/user-join-group/{id}', [GroupController::class, 'dataGroup'])->name('group.userGroup');
    Route::post('/active-user-group', [GroupController::class, 'activeUserJoin'])->name('group.activeUserJoin');
    Route::get('/delete-user-group/{id}/', [GroupController::class, 'destroyUser'])->name('user.destroyUser');

    Route::get('/new-group/', [GroupController::class, 'createGroup'])->name('group.createGroup');
    Route::post('/store-new-group/', [GroupController::class, 'storeGroup'])->name('group.storeGroup');
    Route::get('group/{id}/create-group-training/', [GroupController::class, 'createGroupTraining'])->name('create.GroupTraining');
    Route::post('/store-training/', [GroupController::class, 'storeGroupTraining'])->name('store.GroupTraining');

    //category post
    Route::get('/list-category-post/', [CategoryPostController::class, 'index'])->name('categoryPost.index');
    Route::get('/create-category-post/', [CategoryPostController::class, 'create'])->name('categoryPost.create');
    Route::post('/store-category-post/', [CategoryPostController::class, 'store'])->name('categoryPost.store');
    Route::get('/category-post/{id}/', [CategoryPostController::class, 'show'])->name('categoryPost.show');
    Route::get('/edit-category-post/{id}/', [CategoryPostController::class, 'edit'])->name('categoryPost.edit');
    Route::post('/update-category-post/{id}/', [CategoryPostController::class, 'update'])->name('categoryPost.update');
    Route::get('/destroy-category-post/{id}/', [CategoryPostController::class, 'destroy'])->name('categoryPost.destroy');

    //post
    Route::get('/list-posts/', [PostController::class, 'index'])->name('post.index');
    Route::get('/create-post/', [PostController::class, 'create'])->name('post.create');
    Route::post('/store-post/', [PostController::class, 'store'])->name('post.store');
    Route::get('/post/{id}/', [PostController::class, 'show'])->name('post.show');
    Route::get('/edit-post/{id}/', [PostController::class, 'edit'])->name('post.edit');
    Route::post('/update-post/{id}/', [PostController::class, 'update'])->name('post.update');
    Route::get('/destroy/{id}/', [PostController::class, 'destroy'])->name('post.destroy');

    //shopping
    Route::get('/shop/', [ShopController::class, 'index'])->name('shop.index');

    //category product
    Route::get('/list-category-product/', [CategoryProductController::class, 'index'])->name('categoryProduct.index');
    Route::get('/create-category-product/', [CategoryProductController::class, 'create'])->name('categoryProduct.create');
    Route::post('/store-category-product/', [CategoryProductController::class, 'store'])->name('categoryProduct.store');
    Route::get('/category-product/{id}/', [CategoryProductController::class, 'show'])->name('categoryProduct.show');
    Route::get('/edit-category-product/{id}/', [CategoryProductController::class, 'edit'])->name('categoryProduct.edit');
    Route::post('/update-category-product/{id}/', [CategoryProductController::class, 'update'])->name('categoryProduct.update');
    Route::get('/destroy-category-product/{id}/', [CategoryProductController::class, 'destroy'])->name('categoryProduct.destroy');

    //brand
    Route::get('/list-brand/', [BrandController::class, 'index'])->name('brand.index');
    Route::get('/create-brand/', [BrandController::class, 'create'])->name('brand.create');
    Route::post('/store-brand/', [BrandController::class, 'store'])->name('brand.store');
    Route::get('/brand/{id}/', [BrandController::class, 'show'])->name('brand.show');
    Route::get('/edit-brand/{id}/', [BrandController::class, 'edit'])->name('brand.edit');
    Route::post('/update-brand/{id}/', [BrandController::class, 'update'])->name('brand.update');
    Route::get('/destroy-brand/{id}/', [BrandController::class, 'destroy'])->name('brand.destroy');
    Route::get('/get-brands/{category_id}', [BrandController::class, 'getBrandsByCategory']);
    Route::get('/get-all-brands', [BrandController::class, 'getAllBrands']);

    //product
    Route::get('/list-product/', [ProductController::class, 'index'])->name('product.index');
    Route::post('/store-product/', [ProductController::class, 'store'])->name('product.store');
    Route::get('/create-product/', [ProductController::class, 'create'])->name('product.create');
    Route::get('/edit-product/', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('/update-product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::get('/delete-product/', [ProductController::class, 'delete'])->name('product.delete');
    Route::delete('/delete-product-image/{id}', [ProductController::class, 'deleteProductImage']);

    Route::get('/accept-product', [ProductController::class, 'accept'])->name('product.accept');
    Route::get('/reject-product', [ProductController::class, 'reject'])->name('product.reject');

    //exchange
    Route::get('/user/profile', [ExchangeController::class, 'profile'])->name('exchange.profile');

    Route::get('/post-product', [ExchangeController::class, 'postProduct'])->name('exchange.postProduct');
    Route::get('/product/{slug}', [ExchangeController::class, 'editPostProduct'])->name('exchange.editPostProduct');

    Route::post('/update-post-product/{slug}', [ExchangeController::class, 'updatePostProduct'])->name('exchange.updatePostProduct');
    Route::delete('delete/post-product/{id}', [ExchangeController::class, 'destroy'])->name('product.destroy');

    Route::post('/profile/{id}/', [ExchangeController::class, 'update'])->name('exchange.update');
    Route::get('/me/change-password/', [ExchangeController::class, 'changePassword'])->name('exchange.changePassword');
    Route::post('/profile/update-password/', [ExchangeController::class, 'updatePassword'])->name('exchange.updatePassword');

    Route::get('/manager-posts', [ExchangeController::class, 'managerPosts'])->name('exchange.managerPosts');
    Route::post('/store-post-product/', [ExchangeController::class, 'storePostProduct'])->name('exchange.storePostProduct');

    //chatting
    Route::get('/chat/product/{product}', [ExchangeController::class, 'chatWithSeller'])->name('chat.withSeller');
    Route::get('/chat', [ExchangeController::class, 'listChat'])->name('chat.listChat');
    Route::get('/chat/{conversation}', [ExchangeController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/send', [ExchangeController::class, 'send'])->name('chat.send');



    Route::middleware(['admin'])->group(
        function () {
            Route::get('/dashboard/', [DashboardController::class, 'dashboard'])->name('dashboard');;
            Route::get('/set-title/{id}/', [UserController::class, 'setTitle'])->name('set.title');
            Route::post('/save-title/{id}/', [UserController::class, 'saveTitle'])->name('save.title');

            Route::get('/list-user/', [UserController::class, 'index'])->name('user.index');
            Route::get('/delete/{id}/', [UserController::class, 'destroy'])->name('user.delete');
            Route::get('/change-password/{id}/', [UserController::class, 'changePassword'])->name('user.changePassword');
            Route::post('/updatePassword/{id}/', [UserController::class, 'updatePassword'])->name('user.updatePassword');
        }
    );
});
