<?php
use App\Http\Controllers\Api\AgeRestrictionsController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ChaptersController;
use App\Http\Controllers\Api\CollectionsController;
use App\Http\Controllers\Api\CommentsController;
use App\Http\Controllers\Api\DraftsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ReadHistoryController;
use App\Http\Controllers\Api\ReadListsController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Api\WorksController;
use App\Http\Controllers\Api\WorksInProgressController;
use App\Http\Controllers\Api\GenresController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Psy\Readline\Interactive\Input\History;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/**
 * Routes for the works in progress
 */
Route::controller(WorksInProgressController::class)->group(function () {
    Route::get('/wips','index');
    Route::get('/wips/{id}','show');
    Route::post('/wips','store')->middleware('auth:sanctum');
    Route::put('/wips/{id}','update')->middleware('auth:sanctum');
    Route::delete('/wips/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/wips/{id}/user','getUser');
    Route::get('/wips/{id}/drafts','getDrafts');
    Route::get('/wips/{id}/work','getWork');
});


/**
 * Routes for Age Restrictions
 */
Route::controller(AgeRestrictionsController::class)->group(function(){
    Route::get('/restrictions','index');
    Route::get('/restrictions/{id}','show');
    Route::post('/restrictions','store')->middleware('auth:sanctum');
    Route::put('/restrictions/{id}','update')->middleware('auth:sanctum');
    Route::delete('/restrictions/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/restrictions/{id}/works','work');
});

/**
 * Routes for drafts
 */
Route::controller(DraftsController::class)->group(function(){
    Route::get('/drafts','index');
    Route::get('/drafts/{id}','show');
    Route::post('/drafts','store')->middleware('auth:sanctum');
    Route::put('/drafts/{id}','update')->middleware('auth:sanctum');
    Route::delete('/drafts/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/drafts/{id}/wip','workInProgress');
    Route::get('/drafts/{id}/chapter','chapter');
    Route::post('/drafts/content/store','storeContent');
    Route::post('/drafts/content','content');
});

/**
 * Routes for Genres
 */
Route::controller(GenresController::class)->group(function(){
    Route::get('/genres','index');
    Route::get('/genres/{id}','show');
    Route::post('/genres','store')->middleware('auth:sanctum');
    Route::put('/genres/{id}','update')->middleware('auth:sanctum');
    Route::delete('/genres/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/genres/{id}/works','work');
    Route::post('/genres/addwork','addWork')->middleware('auth:sanctum');
    Route::post('/genres/detachWork','detachWork')->middleware('auth:sanctum');
});

/**
 * Auth routes
 */
Route::post('/register',[AuthController::class,'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user',[AuthController::class,'user']);

/**
 * Routes to pull user's resources
 */
Route::controller(UsersController::class)->group(function(){
    Route::get('/users','index')->middleware('auth:sanctum');
    Route::get('/users/{id}','show')->middleware('auth:sanctum');
    Route::post('/users','store')->middleware('auth:sanctum');
    Route::put('/users/{id}','update')->middleware('auth:sanctum');
    Route::delete('/users/{id}','destroy')->middleware('auth:sanctum');
});

/**
 * Routes for profiles
 */
Route::controller(ProfileController::class)->group(function(){
    Route::get('/profiles','index');
    Route::post('/profiles','store');
    Route::get('/profiles/{id}','show');
    Route::put('/profiles/{id}','update')->middleware('auth:sanctum');
    Route::post('/self/avatar','updateOwnAvatar')->middleware('auth:sanctum');
    Route::delete('/profiles/{id}','destroy');
    Route::get('/profiles/{id}/wips','getWips');
    Route::get('/self/wips','getOwnWips')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/histories','getHistories')->middleware('auth:sanctum');
    Route::get('/self/recent_histories','getOwnLatestHistories')->middleware('auth:sanctum');
    Route::get('/self/histories','getOwnHistories')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/works','getWorks');   
    Route::get('/self/works','getOwnWorks')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/lists','getReadList');
    Route::get('/self/lists','getOwnReadLists')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/comments','getComments');
    Route::get('/profiles/{id}/collections','getCollections');
    Route::get('/self/collections','getOwnCollections')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/following','getFollowing');
    Route::get('/self/following','getOwnFollowing')->middleware('auth:sanctum');
    Route::get('/profiles/{id}/followers','getFollowers');
    Route::get('self/followers','getOwnFollowers')->middleware('auth:sanctum');
    Route::post('/profiles/follow','follow')->middleware('auth:sanctum');
    Route::post('/profiles/unfollow','unFollow')->middleware(('auth:sanctum'));
});

/**
 * Routes protected by tokens
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::put('/password', [AuthController::class, 'updatePassword']);
    //Route::put('avatar',[AuthController::class,'updateAvatar']);
    Route::get('/admin', [AuthController::class, 'adminCheck']);
});

/**
 * Routes for the chapter controller
 */
Route::controller(ChaptersController::class)->group(function(){
    Route::get('/chapters','index');
    Route::get('/chapters/{id}','show');
    Route::post('/chapters','store')->middleware('auth:sanctum');
    Route::put('/chapters/{id}','update')->middleware('auth:sanctum');
    Route::delete('/chapters/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/chapters/{id}/work','getWork');
    Route::get('/chapters/{id}/histories','getReadHistory');
    Route::get('/chapters/{id}/comments','getComments');
    Route::get('/chapters/{id}/draft','getDraft');
    Route::post('/chapters/content','content');
    Route::post('/chapters/updateContent','updateContent');
    Route::post('chapters/cover','storeCover')->middleware(('auth:sanctum'));
});

/**
 * Routes for the works
 */
Route::controller(WorksController::class)->group(function(){
    Route::get('/works','index');
    Route::get('works/latest','latestWorks');
    Route::get('/works/{id}','show');
    Route::post('/works','store')->middleware('auth:sanctum');
    Route::post('/works/storeCover','storeCover')->middleware('auth:sanctum');
    Route::put('/works/{id}','update')->middleware('auth:sanctum');
    Route::delete('/works/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/works/{id}/author','getAuthor');
    Route::get('/works/{id}/chapters','getChapter');    
    Route::get('/works/{id}/chapterCount','getNumChapters');
    Route::get('/works/{id}/restrictions','getAgeRestriction');
    Route::get('/works/{id}/comments','getComment');
    Route::get('/works/{id}/genres','getGenres');
    Route::get('/works/{id}/hitories','getReadHistory');
    Route::get('/works/{id}/lists','getReadList');
    Route::get('/works/{id}/collections','getCollection');
    Route::get('/works/{id}/wip','getWips');
    Route::post('/works/addGenre','addGenre')->middleware('auth:sanctum');
    Route::post('/works/updateGenres','updateGenres')->middleware('auth:sanctum');
});

/**
 * Routes for collections
 */
Route::controller(CollectionsController::class)->group(function(){
    Route::get('/collections','index');
    Route::get('/collections/{id}','show');
    Route::post('/collections','store')->middleware('auth:sanctum');
    Route::put('/collections/{id}','update')->middleware('auth:sanctum');
    Route::delete('/collections/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/collections/{id}/works','works');
    Route::get('/collections/{id}/user','user');
    Route::post('/collections/addwork','addWork')->middleware('auth:sanctum');
    Route::post('/collections/removeWork','removeWork')->middleware('auth:sanctum');
});

/**
 * Routes for read lists
 */
Route::controller(ReadListsController::class)->group(function(){
    Route::get('/lists','index');
    Route::get('/lists/{id}','show');
    Route::post('/lists','store')->middleware('auth:sanctum');
    Route::put('/lists/{id}','update')->middleware('auth:sanctum');
    Route::delete('/lists/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/lists/{id}/works','getWork');
    Route::post('/lists/addWork','workAttach')->middleware('auth:sanctum');
    Route::post('/lists/removeWork','workRemove')->middleware('auth:sanctum');
    Route::get('/lists/{id}/user','getUser');
});

/**
 * Routes for comments
 */
Route::controller(CommentsController::class)->group(function(){
    Route::get('/comments','index');
    Route::get('/comments/{id}','show');
    Route::post('/comments','store')->middleware('auth:sanctum');
    Route::put('/comments/{id}','update')->middleware('auth:sanctum');
    Route::delete('/comments/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/comments/{id}/chapter','chapter');
    Route::get('/comments/{id}/user','user');
    Route::get('/comments/{id}/parent','parent');
    Route::get('/comments/{id}/children','children');
});

/**
 * Routes for history
 */
Route::controller(ReadHistoryController::class)->group(function(){
    Route::get('/history','index');
    Route::get('/history/{id}','show');
    Route::post('/history','store')->middleware('auth:sanctum');
    Route::put('/history/{id}','update')->middleware('auth:sanctum');
    Route::delete('/history/{id}','destroy')->middleware('auth:sanctum');
    Route::get('/history/{id}/user','getUser')->middleware('auth:sanctum');
    Route::get('/history/{id}/chapter','getChapter')->middleware('auth:sanctum');
    Route::get('/history/{id}/work','getWork')->middleware('auth:sanctum');
});