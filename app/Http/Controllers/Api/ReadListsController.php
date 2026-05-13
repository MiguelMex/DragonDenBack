<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReadLists\StoreReadListsRequest;
use App\Http\Requests\ReadLists\UpdateReadListsRequest;
use App\Http\Requests\ReadListsWorks\StoreReadListsWorksRequest;
use App\Http\Resources\ReadList\ReadListUserResource;
use App\Http\Resources\ReadList\ReadListWorkResource;
use App\Http\Resources\ReadListsResource;
use App\Http\Resources\WorksResource;
use App\Models\profile;
use App\Models\ReadLists;
use App\Models\ReadListsWorks;
use App\Models\Works;
use Exception;

class ReadListsController extends Controller
{
    /**
     * return all read lists
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(){
        try{
            $lists = ReadLists::all();
            return ReadListsResource::collection($lists);
        }
        catch(Exception $ex){
            return response()->json(["Error"=>$ex->getMessage()],500);
        }
    }

    /**
     * return a lists by the id
     * @param string $id
     * @return ReadListsResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id){
        try{
            $list = ReadLists::findOrFail($id);
            return new ReadListsResource($list);
        }
        catch(Exception $ex){
            return response()->json(["Error"=>$ex->getMessage()],500);
        }
    }

    /**
     * store a new list
     * @param StoreReadListsRequest $request
     * @return ReadListsResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreReadListsRequest $request){
        try{
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['user'] = $profile->profile_id;
            $exist = ReadLists::where(["user"=>$validated['user'],"name"=>$validated['name']])->first();
            if($exist){
                return response()->json(["Error"=> "El usuario ya tiene una lista con ese nombre"],500);
            }

            $list = ReadLists::create($validated);
            return new ReadListsResource($list);
        }
        catch(Exception $e){
            return response()->json(["Error"=>"Error al crear lista: "+ $e->getMessage()],500);
        }
    }


    public function update(UpdateReadListsRequest $request, string $id){
        try{
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['user'] = $profile->profile_id;
            $exist = ReadLists::where(["user"=>$validated['user'],"name"=>$validated['name']])->first();
            if($exist){
                return response()->json(["Error"=> "El usuario ya tiene una lista con ese nombre"],500);
            }

            $list = ReadLists::findOrFail($id);
            $list->update($validated);
            return new ReadListsResource($list);
        }
        catch(Exception $e){
            return response()->json(["Error"=>"Error al actualizar lista"],500);
        }
    }

    /**
     * delete a list by the id
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id){
        try{
            ReadLists::find($id)->delete();
            return response()->json(["Lista eliminada correctamente"],200);
        }
        catch(Exception $e){
            return response()->json(["Error"=>"Error al eliminar lista"],500);
        }
    }

    /**
     * returns the list with its user
     * @param string $id
     * @return ReadListUserResource|\Illuminate\Http\JsonResponse
     */
    public function getUser(string $id){
        try{
            $list = ReadLists::with('userRelation')->findOrFail($id);
            return new ReadListUserResource($list);
        }
        catch(Exception $e){
            return response()->json(["Error"=>$e->getMessage()],500);
        }
    }

    /**
     * returns the list with all of the works
     * @param mixed $id
     * @return ReadListWorkResource|\Illuminate\Http\JsonResponse
     */
    public function getWork($id){
        try{
            $list = ReadLists::with('workRelation')->findOrFail($id);
            return new ReadListWorkResource($list);
        }
        catch(Exception $e){
            return response()->json(["Error"=>$e->getMessage()],500);
        }
    }

    public function workAttach(StoreReadListsWorksRequest $request){
        try {
            $validated = $request->validated();
            $list = ReadLists::findOrFail($validated['read_list']);
            $work = Works::findOrFail($validated['work']);
            //Make sure the work does not belong to the user but the list does
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            if($profile->profile_id !== $list->user || $profile->profile_id === $work->author){
                return response()->json(['Error'=>'Usuario no autorizado'],500);
            }
            $list->workRelation()->syncWithoutDetaching($work->work_id);
            $list->load('workRelation');
            return new ReadListWorkResource($list);
        } catch (Exception $ex) {
            return response()->json(["Error"=> "Error al crear relación con trabajo: ".$ex->getMessage()],500);
        }
    }

    public function workRemove(StoreReadListsWorksRequest $request){
        try{
            $validated = $request->validated();
            $list = ReadLists::findOrFail($validated['read_list']);
            $work = Works::findOrFail($validated['work']);
            
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            if($profile->profile_id !== $list->user || $profile->profile_id === $work->author){
                return response()->json(['Error'=>'Usuario no autorizado'],500);
            }

            $list->workRelation()->detach($work->work_id);
            $list->load('workRelation');

            return new ReadListWorkResource($list);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }
}
