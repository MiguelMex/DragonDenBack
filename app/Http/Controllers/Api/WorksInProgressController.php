<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreWorksInProgressRequest;
use App\Http\Requests\UpdateWorksInProgressRequest;
use App\Http\Resources\DraftsResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WipDraftsResource;
use App\Http\Resources\WIPS\WorkInProgressDraftsResource;
use App\Http\Resources\WIPS\WorkInProgressUserResource;
use App\Http\Resources\WIPS\WorkInProgressWorkResource;
use App\Models\Genres;
use App\Models\WorksInProgress;
use App\Http\Resources\WorksInProgressResource;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WorksInProgressController extends Controller
{
    /**
     * Muestra todos los modelos
     */
    public function index()
    {
        try
        {
            $wips = WorksInProgress::all();
            return WorksInProgressResource::collection($wips);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * almacenar wip
     */
    public function store(StoreWorksInProgressRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['author'] = auth()->id();
            $exists = WorksInProgress::where('title',$validated['title'])->exists();
            if ($exists)
            {
                return response()->json(['Error'=>'Nombre ya existente'],500);
            }
            $wip = WorksInProgress::create($validated);
            return new WorksInProgressResource($wip);
        } catch (Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un post especifico
     */
    public function show(string $id)
    {
        try {
            $worksInProgress = WorksInProgress::findOrFail($id);
            return new WorksInProgressResource($worksInProgress);
        } catch (Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar un post existente
     */
    public function update(string $id, UpdateWorksInProgressRequest $wip)
    {
        try {
            $workInProgress = WorksInProgress::findOrFail($id);
            $workInProgress->update($wip->validated());
            return new WorksInProgressResource($workInProgress);
        } catch (Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $wip = WorksInProgress::findOrFail($id)->delete();
            return response()->json(['message'=>'Eliminado correctamente'],200);
        } catch (Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all drafts of this wip
     * @param string $id
     * @return WorkInProgressDraftsResource|JsonResponse
     */
    public function getDrafts(String $id)
    {
        try
        {
            $wip = WorksInProgress::with('draftsRelation')->findOrFail($id);
            return new WorkInProgressDraftsResource($wip);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    
    /**
     * Get the user a wip belongs to
     * @param string $id
     * @return JsonResponse|WorkInProgressUserResource
     */
    public function getUser(String $id)
    {
        try
        {
            $wip = WorksInProgress::with('userRelation')->findOrFail($id);
            return new WorkInProgressUserResource($wip);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * get the work that corresponds to this wip
     * @param string $id
     * @return JsonResponse|WorkInProgressWorkResource
     */
    public function getWork(string $id){
        try{
            $wip = WorksInProgress::with('workRelation')->findOrFail($id);
            return new WorkInProgressWorkResource($wip);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }
}
