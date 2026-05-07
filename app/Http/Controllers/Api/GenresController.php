<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genres\GenreWorkRequest;
use App\Http\Resources\Genres\GenreWorksResource;
use App\Http\Resources\WorksResource;
use App\Models\Genres;
use App\Http\Requests\Genres\GenresRequest;
use App\Http\Resources\GenresResource;
use App\Models\Works;
use Illuminate\Http\Request;
use Exception;
use function PHPUnit\Framework\returnArgument;

class GenresController extends Controller
{
    /**
     * Method to get all genres
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try
        {
            $genres = Genres::all();
            return GenresResource::collection($genres);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Method to get a genre by the id
     * @param mixed $id
     * @return GenresResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $genre = Genres::findOrFail($id);
            return new GenresResource($genre);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Method to store a genre
     * @param GenresRequest $request
     * @return GenresResource|\Illuminate\Http\JsonResponse
     */
    public function store(GenresRequest $request)
    {
        try
        {
            $exists = Genres::where('name',$request->name)->exists();
            if($exists)
            {
                return response()->json(['Error'=>'Ese genero ya existe'],500);
            }

            $genre = Genres::create($request->validated());
            return new GenresResource($genre);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Method to update a genre
     * @param GenresRequest $request
     * @param string $id
     * @return GenresResource|\Illuminate\Http\JsonResponse
     */
    public function update(GenresRequest $request,String $id)
    {
        try
        {
            $genre = Genres::findOrFail($id);
            $genre->update($request->validated());
            $genre->touch();
            return new GenresResource($genre);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Method to delete a genre
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(String $id)
    {
        try
        {
            Genres::findOrFail($id)->delete();
            return response()->json(['El genero ah sido borrado exitosamente'],200);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Get all the works of a genre
     * @param string $id
     * @return GenreWorksResource|\Illuminate\Http\JsonResponse
     */
    public function work(string $id)
    {
        try
        {
            $genre = Genres::with('workRelation')->findOrFail($id);
            return new GenreWorksResource($genre);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function addWork(GenreWorkRequest $request){
        try {
            $validated = $request->validated();
            $genre = Genres::findOrFail($validated['genre_id']);
            $work = Works::findOrFail($validated['work_id']);
            $genre->workRelation()->syncWithoutDetaching($work->work_id);
            $genre->load('workRelation');
            return new GenreWorksResource($genre);
        } catch (Exception $ex) {
            return response()->json(['Error'=> $ex->getMessage()],500);
        }
    }
}
