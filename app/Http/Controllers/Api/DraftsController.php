<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drafts\StoreDraftRequest;
use App\Http\Requests\Drafts\UpdateDraftRequest;
use App\Http\Resources\Draft\DraftChapterResource;
use App\Http\Resources\Draft\DraftWipResource;
use App\Http\Resources\DraftsResource;
use App\Http\Resources\WorksInProgressResource;
use App\Models\Drafts;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Log;

class DraftsController extends Controller
{
    /**
     * Return all drafts
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try
        {
            $drafts = Drafts::all();
            return DraftsResource::collection($drafts);
        } 
        catch (Exception $ex)
        {
            return response()->json(['error'=>$ex],500);
        }
    }

    /**
     * Create a draft
     * @param StoreDraftRequest $request
     * @return DraftsResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreDraftRequest $request)
    {
        try
        {
            $validated = $request->validated();
            $exists = Drafts::where(['name'=>$validated['name'],'work_in_progress'=>$validated['work_in_progress']])->exists();
            if ($exists)
            {
                return response()->json(['Error' => "Ese nombre de draft ya existe en este trabajo"]);
            }

            //Store the file in the local disk
            $fileName = 'drafts/'.Str::uuid().'.json';
            
            $stored = Storage::disk('local')->put($fileName,'');
            if(!$stored){
                return response()->json(['Error'=> 'Error al crear archivo'],500);
            }

            $validated['route'] = $fileName;

            $draft = Drafts::create($validated);
            return new DraftsResource($draft);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error' => $ex->getMessage()]);
        }
    }

    /**
     * Store the content in the file
     * @param Request $request
     * @return DraftsResource|\Illuminate\Http\JsonResponse
     */
    public function storeContent(Request $request){
        try{
            $request->validate([
                'id'=>'required|string',
                'content'=>'required'
            ]);

            $draft = Drafts::find($request->id);
            if($draft == null){
                return response()->json(['Error'=> 'Borrador no existe'],500);
            }
            $content = $request->input('content');

            if (isset($content['ops']) && is_array($content['ops'])) {
                foreach ($content['ops'] as &$op) {
                    if (array_key_exists('insert', $op) && $op['insert'] === null) {
                        $op['insert'] = "\n";
                    }
                }
            }
            $updated = Storage::disk('local')->put($draft->route, json_encode($content));
            if(!$updated){
                return response()->json(['Error'=> 'Error al actualizar contenido'],500);
            }

            $draft->touch();
            return new DraftsResource($draft);
        }
        catch (Exception $ex){
            return response()->json(['Error'=> "Error al guardar contenido: ".$ex->getMessage()]);
        }
    }

    /**
     * Get the content
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(Request $request)
    {
        try {
            $request->validate([
                'id'=>'required',
            ]);

            $id= $request->id;
            $draft = Drafts::findOrFail($id);
            $content = Storage::disk('local')->get($draft->route);
            return response()->json(json_decode($content));
        } catch (Exception $ex) {
            return response()->json(['Error'=> 'Error al obtener el contenido: '.$ex->getMessage()]);
        }
    }

    /**
     * Get a draft by the id
     * @param string $id
     * @return DraftsResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        try
        {
            $draft = Drafts::findOrFail($id);
            return new DraftsResource($draft);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Update an existing draft model
     * @param UpdateDraftRequest $request
     * @param string $id
     * @return DraftsResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateDraftRequest $request, string $id)
    {
        try
        {
            $draft = Drafts::findOrFail($id);
            $draft->update($request->validated());
            $draft->touch();
            return new DraftsResource($draft);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Delete a draft moodel from the database
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try
        {
            $draft = Drafts::findOrFail($id)->delete();
            if($draft->route && Storage::disk('local')->exists($draft->route)){
                Storage::disk('local')->delete($draft->route);
            }
            return response()->json(['Mensaje'=>'Borrador eliminado correctamente']);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Get the work in progress of a draft
     * @param string $id
     * @return DraftWipResource|\Illuminate\Http\JsonResponse
     */
    public function workInProgress(string $id)
    {
        try
        {
            $draft = Drafts::with('wipRelation')->findOrFail($id);
            return new DraftWipResource($draft);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * returns the draft and the chapter is related to
     * @param string $id
     * @return DraftChapterResource|\Illuminate\Http\JsonResponse
     */
    public function chapter(string $id){
        try{
            $draft = Drafts::with('chapterRelation')->findOrFail($id);
            return new DraftChapterResource($draft);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }   
}
