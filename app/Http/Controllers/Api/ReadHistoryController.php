<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReadHistory\StoreReadHistoryRequest;
use App\Http\Resources\History\ReadHistoryChapterResource;
use App\Http\Resources\History\ReadHistoryUserResource;
use App\Http\Resources\History\ReadHistoryWorkResource;
use App\Http\Resources\ReadHistoryResource;
use App\Models\profile;
use App\Models\ReadHistory;
use Exception;
use Illuminate\Http\Request;

class ReadHistoryController extends Controller
{
    /**
     * returns all read histories
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(){
        try{
            $history = ReadHistory::with(['workRelation','chapterRelation'])->get();
            return ReadHistoryResource::collection($history);
        }
        catch(Exception $e){
            return response()->json(["Error"=>"Error al regresar todos los historiales"],500);
        }
    }

    /**
     * returns a read history by id
     * @param string $id
     * @return ReadHistoryResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id){
        try{
            $history = ReadHistory::with(['workRelation','chapterRelation'])->findOrFail($id);
            return new ReadHistoryResource($history);
        }
        catch(Exception $e){
            return response()->json(["Error"=> "Error al mostrar historial"],500);
        }
    }

    /**
     * create a new read history row
     * @param StoreReadHistoryRequest $request
     * @return ReadHistoryResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreReadHistoryRequest $request){
        try{
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['user'] = $profile->profile_id;
            $history = ReadHistory::create($validated);
            return new ReadHistoryResource($history);
        }
        catch(Exception $e){
            return response()->json(["Error"=> "Error al guardar historial"]);
        }
    }

    /**
     * update a read history
     * @param StoreReadHistoryRequest $request
     * @param string $id
     * @return ReadHistoryResource|\Illuminate\Http\JsonResponse
     */
    public function update(StoreReadHistoryRequest $request, string $id){
        try{
            $history = ReadHistory::findOrFail($id);
            $history->update($request->validated());
            return new ReadHistoryResource($history);
        }
        catch(Exception $e){
            return response()->json(["Error"=> "Error al modificar el historial"]);
        }
    }

    /**
     * deleta a history
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id){
        try{
            ReadHistory::findOrFail($id)->delete();
            return response()->json(["Mensaje"=> "Historial eliminado correctamente"]);
        }
        catch(Exception $e){
            return response()->json(["Error"=> "Error al eliminar el historial"]);
        }
    }

    /**
     * return the history with its user
     * @param string $id
     * @return ReadHistoryUserResource|\Illuminate\Http\JsonResponse
     */
    public function getUser(string $id){
        try{
            $history = ReadHistory::with('userRelation')->findOrFail($id);
            return new ReadHistoryUserResource($history);
        }
        catch(Exception $ex){
            return response()->json(["Error"=>$ex->getMessage()]);
        }
    }

    /**
     * returns the history with its chapter by id
     * @param string $id
     * @return ReadHistoryChapterResource|\Illuminate\Http\JsonResponse
     */
    public function getChapter(string $id){
        try{
            $history = ReadHistory::with('chapterRelation')->findOrFail($id);
            return new ReadHistoryChapterResource($history);
        }
        catch(Exception $ex){
            return response()->json(["Error"=>$ex->getMessage()]);
        }
    }

    /**
     * returns the history with its work by the id
     * @param string $id
     * @return ReadHistoryWorkResource|\Illuminate\Http\JsonResponse
     */
    public function getWork(string $id){
        try{
            $history = ReadHistory::with('workRelation')->findOrFail($id);
            return new ReadHistoryWorkResource($history);
        }
        catch(Exception $ex){
            return response()->json(["Error"=>$ex->getMessage()]);
        }
    }
}
