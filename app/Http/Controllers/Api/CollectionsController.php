<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\AddWorkCollection;
use App\Http\Requests\Collection\RemoveWorkFromCollectionRequest;
use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use App\Http\Resources\Collections\CollectionsUserResource;
use App\Http\Resources\Collections\CollectionsWorkResource;
use App\Http\Resources\CollectionsResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\WorksResource;
use App\Models\Collections;
use App\Models\Works;
use Error;
use Exception;
use Illuminate\Http\Request;

class CollectionsController extends Controller
{
    /**
     * Get all Collections
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try
        {
            $collections = Collections::all();
            return CollectionsResource::collection($collections);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Get all collections
     * @param mixed $id
     * @return CollectionsResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $collection = Collections::findOrFail($id);
            return new CollectionsResource($collection);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Store a Collection
     * @param StoreCollectionRequest $request
     * @return CollectionsResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreCollectionRequest $request)
    {
        try
        {
            //Se if it already exists for this user
            $exists = Collections::where(['name'=>$request->name,'user'=>$request->user])->exists();
            if($exists)
            {
                return response()->json(['Error'=>'Ya existe este nombre en las colecciones del usuario']);
            }

            //se guarda
            $validated = $request->validated();
            $validated['user'] = auth()->id();
            $collection = Collections::create($validated);
            return new CollectionsResource($collection);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Update a Collection
     * @param UpdateCollectionRequest $request
     * @param string $id
     * @return CollectionsResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateCollectionRequest  $request, string $id)
    {
        try
        {
            //Se if it already exists for this user
            $exists = Collections::where(['name'=>$request->name,'user'=>$request->user])->exists();
            if($exists)
            {
                return response()->json(['Error'=>'Ya existe este nombre en las colecciones del usuario']);
            }

            //store the changes
            $collection = Collections::findOrFail($id);
            $collection->update($request->validated());
            $collection->touch();
            return new CollectionsResource($collection);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Delete a collection
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try
        {
            Collections::findOrFail($id)->delete();
            return response()->json(['Message'=>'Colección eliminada correctamente']);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Get all the works of a collection
     * @param string $id
     * @return WorksResource|\Illuminate\Http\JsonResponse|CollectionsWorkResource
     */
    public function works(string $id)
    {
        try
        {
            $collection = Collections::with('workRelation')->findOrFail($id);
            return new CollectionsWorkResource( $collection );
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Get the user a collection belongs to
     * @param mixed $id
     * @return UserResource|\Illuminate\Http\JsonResponse|CollectionsUserResource
     */
    public function user($id)
    {
        try
        {
            $collection = Collections::with('userRelation')->findOrFail($id);
            return new CollectionsUserResource( $collection );   
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    public function addWork(AddWorkCollection $request){
        try{
            $validated = $request->validated();
            $work = Works::findOrFail($validated['work_id']);
            $collection = Collections::findOrFail($validated['collection_id']);

            //Check that the work and collection corresponds to the user
            $id = auth()->id();
            if($id !== $collection->user || $id !== $work->author){
                return response()->json(['Error'=>'El trabajo debe de pertenecer al usuario'],500);
            }

            $collection->workRelation()->syncWithoutDetaching($work->work_id);
            $collection->load('workRelation');
            return new CollectionsWorkResource($collection);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    public function removeWork(RemoveWorkFromCollectionRequest $request)
    {
        try {
            $validated = $request->validated();
            $work = Works::findOrFail($validated['work_id']);
            $collection = Collections::findOrFail($validated['collection_id']);

            $id = auth()->id();

            if($id !== $collection->user || $id !== $work->author){
                return response()->json(['Error'=>'El trabajo debe de pertenecer al usuario'],500);
            }

            // Detach the work from the collection
            $collection->workRelation()->detach($work->work_id);
            
            // Reload the relationship to reflect the updated state
            $collection->load('workRelation');
            
            return new CollectionsWorkResource($collection);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
