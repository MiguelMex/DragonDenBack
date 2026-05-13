<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Following\FollowingRequest;
use App\Http\Requests\Profiles\StoreProfileRequest;
use App\Http\Requests\Profiles\UpdateProfileRequest;
use App\Http\Resources\Profiles\ProfileResource;
use App\Http\Resources\User\UserCollectionResource;
use App\Http\Resources\User\UserCommentsResource;
use App\Http\Resources\User\UserFollowersResource;
use App\Http\Resources\User\UserFollowingResource;
use App\Http\Resources\User\UserHistoryResource;
use App\Http\Resources\User\UserReadListsResource;
use App\Http\Resources\User\UserWorksResource;
use App\Http\Resources\UserWipsCollection;
use App\Models\profile;
use Exception;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Str;

class ProfileController extends Controller
{
    public function index(){
        try{
            $profiles = profile::all();
            return ProfileResource::collection($profiles);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    public function store(StoreProfileRequest $request){
        try{
            $validated = $request->validated();
            $validated['user_id'] = auth()->id();
            $profile = profile::create($validated);
            return new ProfileResource($profile);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    public function update(string $id, UpdateProfileRequest $request){
        try{
            $validated = $request->validated();
            $profile = profile::findOrFail($id);
            $profile->update($validated);
            $profile->touch();
            return new ProfileResource($profile);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    public function show(string $id){
        try{
            $profile = profile::findOrFail($id);
            return new ProfileResource($profile);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    public function updateOwnAvatar(Request $request){
        try{
            $request->validate([
                'image'=>'required|file|max:2048'
            ]);

            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();

            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();

            //Delete if exists
            if($profile->avatar && Storage::disk('public')->exists($profile->avatar) && $profile->avatar !== 'avatars/default.jpeg'){
                Storage::disk('public')->delete($profile->avatar);
            }

            //unique file name
            $name = Str::uuid().'.'.$extension;
            $path = 'avatars/'.$name;

            $stored = $image->storeAs('',$path,'public');
            if(!$stored){
                return response()->json(['Error'=>'Error al guardar avatar'],500);
            }

            $profile->avatar = $path;
            $profile->save();
            $profile->touch();

            return new ProfileResource($profile);
        } catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    public function destroy(string $id){
        try{
            profile::findOrFail($id)->delete();
            return response()->json(['Exito'=>'Profile borrado con exito'],500);
        } catch (Exception $ex){
            return response()->json(['Error: '=>$ex->getMessage()],500);
        }
    }

    //____________________________________AREA OF METHODS FOR DATA RETRIEVAL_____________________________________________________

    /**
     * Return the wips of an user
     * @return \Illuminate\Http\JsonResponse|UserWipsCollection
     */
    public function getWips(string $id)
    {
        try
        {
            $user = profile::with('wipsRelation')->findOrFail($id);
            return new UserWipsCollection($user);
        }
        catch(Exception $ex)
        {
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return the history of an user
     * @return \Illuminate\Http\JsonResponse|UserHistoryResource
     */
    public function getHistories(string $id){
        try{
            $user = profile::with('historiesRelation')->findOrFail($id);
            return new UserHistoryResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return the works of an user
     * @return \Illuminate\Http\JsonResponse|UserWorksResource
     */
    public function getWorks(string $id){
        try{
            $user = profile::with('worksRelation')->findOrFail($id);
            return new UserWorksResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return the lists of an user
     * @return \Illuminate\Http\JsonResponse|UserReadListsResource
     */
    public function getReadList(string $id){
        try{
            $user = profile::with('readListsRelation')->findOrFail($id);
            return new UserReadListsResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return the comments of an user
     * @return \Illuminate\Http\JsonResponse|UserCommentsResource
     */
    public function getComments(string $id){
        try{
            $user = profile::with('commentsRelation')->findOrFail($id);
            return new UserCommentsResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return the collections of an user
     * @return \Illuminate\Http\JsonResponse|UserCollectionResource
     */
    public function getCollections(string $id){
        try{
            $user = profile::with('collectionsRelation')->findOrFail($id);
            return new UserCollectionResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return all users being followed y the user with the given id
     * @return \Illuminate\Http\JsonResponse|UserFollowingResource
     */
    public function getFollowing(string $id){
        try{
            $user = profile::with('following')->findOrFail($id);
            return new UserFollowingResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    /**
     * return all users following the user with the given id
     * @return \Illuminate\Http\JsonResponse|UserFollowersResource
     */
    public function getFollowers(string $id){
        try{
            $user = profile::with('followers')->findOrFail($id);
            return new UserFollowersResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=> $ex->getMessage()]));
        }
    }

    //_______________________________________________GET DATA FROM LOGGED IN USER____________________________________________________________

    public function getOwnWorks(){
        try{
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $user = profile::with('worksRelation')->findOrFail($profile->profile_id);
            return new UserWorksResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    public function getOwnWips()
    {
        try
        {
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $user = profile::with('wipsRelation')->findOrFail($profile->profile_id);
            return new UserWipsCollection($user);
        }
        catch(Exception $ex)
        {
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    public function getOwnReadLists(){
        try{
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $user = profile::with('readListsRelation')->findOrFail($profile->profile_id);
            return new UserReadListsResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    public function getOwnCollections(){
        try{
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $user = profile::with('collectionsRelation')->findOrFail($profile->profile_id);
            return new UserCollectionResource($user);
        }
        catch(Exception $ex){
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

    public function follow(FollowingRequest $request){
        try {
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['follower_id'] = $profile->profile_id;
            $follower = profile::findOrFail($validated['follower_id']);
            $user = profile::find($validated['user_id']);;
            $follower->following()->syncWithoutDetaching($user->profile_id);
            $follower->load('following');
            return new UserFollowingResource($follower);
        } catch (Exception $ex) {
            return response()->json((["Error"=>$ex->getMessage()]));
        }
    }

}
