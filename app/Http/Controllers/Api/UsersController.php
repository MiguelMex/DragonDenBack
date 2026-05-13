<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Following\FollowingRequest;
use App\Http\Resources\User\UserCollectionResource;
use App\Http\Resources\User\UserCommentsResource;
use App\Http\Resources\User\UserHistoryResource;
use App\Http\Resources\User\UserReadListsResource;
use App\Http\Resources\User\UserWorksResource;
use App\Http\Resources\User\UserFollowingResource;
use App\Http\Resources\User\UserFollowersResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserWipsCollection;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;
use Request;

class UsersController extends Controller
{
    public function index(){
        try{
            $users = User::all();
            return UserResource::collection($users);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    public function destroy(string $id){
        try{
            User::findOrFail($id)->delete();
            //TODO: Asegurarse de que el suario que llama a este metodo es administrador
            return response(['Exito'=>'Usuario eliminado correctamente']);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }


    // /**
    //  * Return the wips of an user
    //  * @return \Illuminate\Http\JsonResponse|UserWipsCollection
    //  */
    // public function getWips(string $id)
    // {
    //     try
    //     {
    //         $user = User::with('wipsRelation')->findOrFail($id);
    //         return new UserWipsCollection($user);
    //     }
    //     catch(Exception $ex)
    //     {
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // public function getOwnWips()
    // {
    //     try
    //     {
    //         $id = auth()->id();
    //         $user = User::with('wipsRelation')->findOrFail($id);
    //         return new UserWipsCollection($user);
    //     }
    //     catch(Exception $ex)
    //     {
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return the history of an user
    //  * @return \Illuminate\Http\JsonResponse|UserHistoryResource
    //  */
    // public function getHistories(string $id){
    //     try{
    //         $user = User::with('historiesRelation')->findOrFail($id);
    //         return new UserHistoryResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return the works of an user
    //  * @return \Illuminate\Http\JsonResponse|UserWorksResource
    //  */
    // public function getWorks(string $id){
    //     try{
    //         $user = User::with('worksRelation')->findOrFail($id);
    //         return new UserWorksResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // public function getOwnWorks(){
    //     try{
    //         $id = auth()->id();
    //         $user = User::with('worksRelation')->findOrFail($id);
    //         return new UserWorksResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return the lists of an user
    //  * @return \Illuminate\Http\JsonResponse|UserReadListsResource
    //  */
    // public function getReadList(string $id){
    //     try{
    //         $user = User::with('readListsRelation')->findOrFail($id);
    //         return new UserReadListsResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // public function getOwnReadLists(){
    //     try{
    //         $id = auth()->id();
    //         $user = User::with('readListsRelation')->findOrFail($id);
    //         return new UserReadListsResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return the comments of an user
    //  * @return \Illuminate\Http\JsonResponse|UserCommentsResource
    //  */
    // public function getComments(string $id){
    //     try{
    //         $user = User::with('commentsRelation')->findOrFail($id);
    //         return new UserCommentsResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return the collections of an user
    //  * @return \Illuminate\Http\JsonResponse|UserCollectionResource
    //  */
    // public function getCollections(string $id){
    //     try{
    //         $user = User::with('collectionsRelation')->findOrFail($id);
    //         return new UserCollectionResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // public function getOwnCollections(){
    //     try{
    //         $id = auth()->id();
    //         $user = User::with('collectionsRelation')->findOrFail($id);
    //         return new UserCollectionResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return all users being followed y the user with the given id
    //  * @return \Illuminate\Http\JsonResponse|UserFollowingResource
    //  */
    // public function getFollowing(string $id){
    //     try{
    //         $user = User::with('following')->findOrFail($id);
    //         return new UserFollowingResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }

    // /**
    //  * return all users following the user with the given id
    //  * @return \Illuminate\Http\JsonResponse|UserFollowersResource
    //  */
    // public function getFollowers(string $id){
    //     try{
    //         $user = User::with('followers')->findOrFail($id);
    //         return new UserFollowersResource($user);
    //     }
    //     catch(Exception $ex){
    //         return response()->json((["Error"=> $ex->getMessage()]));
    //     }
    // }

    // public function follow(FollowingRequest $request){
    //     try {
    //         $validated = $request->validated();
    //         $validated['follower_id'] = auth()->id();
    //         $follower = User::findOrFail($validated['follower_id']);
    //         $user = User::find($validated['user_id']);;
    //         $follower->following()->syncWithoutDetaching($user->user_id);
    //         $follower->load('following');
    //         return new UserFollowingResource($follower);
    //     } catch (Exception $ex) {
    //         return response()->json((["Error"=>$ex->getMessage()]));
    //     }
    // }
}
