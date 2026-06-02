<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgeRestriction\StoreAgeRestrictionRequest;
use App\Http\Requests\AgeRestriction\UpdateAgeRestrictionRequest;
use App\Http\Resources\AgeRestrictionsResource;
use App\Http\Resources\AgeRestrictionWorksResource;
use App\Models\AgeRestrictions;
use Exception;

class AgeRestrictionsController extends Controller
{
    /**
     * Return all age Restrictions models
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try
        {
            $restrictions = AgeRestrictions::all();
            return AgeRestrictionsResource::collection($restrictions);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Return an Age Restriction by its Id
     * @param mixed $id
     * @return AgeRestrictionsResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $restriction = AgeRestrictions::findOrFail($id);
            return new AgeRestrictionsResource($restriction);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Method to store in the database
     * @param StoreAgeRestrictionRequest $request
     * @return AgeRestrictionsResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreAgeRestrictionRequest $request)
    {
        try
        {
            if(!auth()->user()->admin){
                return response()->json(['Error'=>'Usuario no autorizado'],403);
            }

            $exists = AgeRestrictions::where('name',$request->name)->exists();
            if($exists)
            {
                return response()->json(['Error'=>'Ya existe un modelo con es enombre']);
            }

            $restriction = AgeRestrictions::create($request->validated());
            return new AgeRestrictionsResource($restriction);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Update an existing model
     * @param UpdateAgeRestrictionRequest $request
     * @param string $id
     * @return AgeRestrictionsResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateAgeRestrictionRequest $request,string $id)
    {
        try
        {
            if(!auth()->user()->admin){
                return response()->json(['Error'=>'Usuario no autorizado'],403);
            }

            $exists = AgeRestrictions::where('name', $request->name)
                        ->where('age_restriction_id', '!=', $id)
                        ->exists();

            if($exists){
                return response()->json(['Error'=>'Ya existe un modelo con es enombre']);
            }

            $restriction = AgeRestrictions::findOrFail($id);
            $restriction->update($request->validated());
            $restriction->touch();
            return new AgeRestrictionsResource($restriction);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Deleta an existing Age Restriction
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try
        {
            AgeRestrictions::findOrFail($id)->delete();
            return response()->json(['Mensaje'=>'Restricción de edad eliminada correctamente']);
        }   
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * return the age restriction with all works related to it
     * @param string $id
     * @return \Illuminate\Http\JsonResponse| AgeRestrictionWorksResource
     */
    public function work(string $id)
    {
        try
        {
            if(!auth()->user()->admin){
                return response()->json(['Error'=>'Usuario no autorizado'],403);
            }
            
            $restriction = AgeRestrictions::with('workRelation')->findOrFail($id);
            return new AgeRestrictionWorksResource($restriction);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }
}
