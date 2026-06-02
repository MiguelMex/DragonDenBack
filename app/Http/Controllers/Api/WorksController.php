<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Works\StoreWorksRequest;
use App\Http\Requests\Works\UpdateWorksRequest;
use App\Http\Resources\AgeRestrictionsResource;
use App\Http\Resources\ChaptersResource;
use App\Http\Resources\GenresResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Works\WorksAgeRestrictionResource;
use App\Http\Resources\Works\WorksAuthorResource;
use App\Http\Resources\Works\WorksChapterResource;
use App\Http\Resources\Works\WorksCollectionResource;
use App\Http\Resources\Works\WorksCommentResource;
use App\Http\Resources\Works\WorksGenresResource;
use App\Http\Resources\Works\WorksReadHistoryResource;
use App\Http\Resources\Works\WorksReadListResource;
use App\Http\Resources\Works\WorksWipsResource;
use App\Models\Genres;
use App\Models\profile;
use App\Models\User;
use App\Models\Works;
use App\Http\Resources\WorksResource;
use App\Http\Resources\CollectionsResource;
use App\Models\WorksInProgress;
use Exception;
use Illuminate\Foundation\Http\Middleware\Concerns\ExcludesPaths;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rule;
use Str;

class WorksController extends Controller
{
    /**
     * Get all works
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try
        {
            $works = Works::with([
                'genresRelation',
                'authorRelation',
                'ageRestrictionRelation',
                'chapterRelation',
                'collectionsRelation',
                'readListsRelation',
                ])
                ->withCount('chapterRelation as chapters_count')
                ->orderBy('updated_at','desc')
                ->get();
            return WorksResource::collection($works);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function latestWorks()
    {
        try
        {
            $works = Works::with([
                'genresRelation',
                'authorRelation',
                'ageRestrictionRelation',
                'chapterRelation',
                'collectionsRelation',
                ])
                ->withCount('chapterRelation as chapters_count')
                ->latest('updated_at')
                ->limit(100)
                ->get();
            return WorksResource::collection($works);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Get a work by the id
     * @param string $id
     * @return WorksResource|\Illuminate\Http\JsonResponse
     */
    public function show(String $id)
    {
        try
        {
            $work = Works::with([
                'genresRelation',
                'authorRelation',
                'ageRestrictionRelation',
                'chapterRelation',
                'collectionsRelation',
                'readListsRelation'
                ])
                ->withCount('chapterRelation as chapters_count')
                ->findOrFail($id);
            return new WorksResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Create a new work
     * @param StoreWorksRequest $request
     * @return WorksResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreWorksRequest $request)
    {
        try
        {
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['author'] = $profile->profile_id;

            //revisar que el autor sea el mismo que el del wip o que sea administrador
            $wip = WorksInProgress::findOrFail($validated['work_in_progress']);
            if($validated['author'] !== $wip->author && !auth()->user()->admin){
                return response()->json(['Error' => 'Usuario no autorizado'],403);
            }

            $exists = Works::where(['title'=>$validated['title'],'author'=>$validated['author']])->exists();
            if ($exists)
            {
                return response()->json(['Error'=>'este autor ya posee ese titulo'],500);
            }
            
            $validated['cover'] = 'covers/default.jpeg';
            $work = Works::create($validated);
            return new WorksResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function storeCover(Request $request){
        try{
            $request->validate([
                'id'=>['required',Rule::exists('works','work_id')],
                'image'=>'required|file|max:2048'
            ]);

            //Search for work
            $work = Works::findOrFail($request->id);

            //see that is the author trying to modify the cover
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            if($work->author !== $profile->profile_id && !auth()->user()->admin){
                return response()->json(['Error'=> 'Usuario no autorizado'],403);
            }

            //get the extension of the file
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();

            //Delete if it exists
            if($work->cover && Storage::disk('public')->exists($work->cover) && $work->cover != 'covers/default.jpeg'){
                Storage::disk('public')->delete($work->cover);
            }

            //generate unique file name
            $fileName = Str::uuid().'.'.$extension;
            $path = 'covers/'.$fileName;

            // $stored = Storage::disk('public')->put($path,file_get_contents($image));
            $stored = $image->storeAs('',$path,'public');
            if(!$stored){
                return response()->json(['Error'=> 'Error al crear archivo'],500);
            }

            $work->cover = $path;
            $work->save();

            return response()->json(['Mensaje'=>'Portada guardada con exito','Path'=>$path],200);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Update a work
     * @param UpdateWorksRequest $request
     * @param string $workId
     * @return WorksResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateWorksRequest $request, String $workId)
    {
        try
        {
            $validated = $request->validated();

            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();

            if(!auth()->user()->admin){

                $validated['author'] = $profile->profile_id;
                $exists = Works::where([
                    'title'=>$validated['title'],
                    'author'=>$validated['author'],
                    ])
                    ->where('work_id', '!=', $workId)
                    ->exists();
                if ($exists)
                {
                    return response()->json(['Error'=>'este autor ya posee ese titulo'],409);
                }
            }

            //If title is not already on use for this user
            $work = Works::findOrFail($workId);

            //User's check
            if($profile->profile_id !== $work->author && !auth()->user()->admin){
                return response()->json(['Error'=>'Usuario no autorizado'],403);
            }

            $work->update($validated);
            $work->touch();
            return new WorksResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Delete an existing work
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(String $id)
    {
        try
        {
            $work = Works::findOrFail($id);
            $profile = profile::where('user_id', auth()->id())->firstOrFail();
            if($profile->profile_id !== $work->author && !auth()->user()->admin){
                return response()->json(['Error'=>'Usuario no autorizado'],403);
            }
            if($work->cover && Storage::disk('public')->exists($work->cover)){
                Storage::disk('public')->delete($work->cover);
            }
            $work->delete();
            return response()->json(['Message'=>'Trabajo borrado exitosamente']);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * returns the work with the author
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|WorksAuthorResource
     */
    public function getAuthor(String $id)
    {
        try
        {
            $work = Works::with('authorRelation')->findOrFail($id);
            return new WorksAuthorResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * returns the work with the age restriction
     * @param string $id
     * @return WorksAgeRestrictionResource|\Illuminate\Http\JsonResponse
     */
    public function getAgeRestriction(String $id)
    {
        try
        {
            $work = Works::with('ageRestrictionRelation')->findOrFail($id);
            return new WorksAgeRestrictionResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getChapter(String $id)
    {
        try
        {
            $work = Works::with('chapterRelation')->findOrFail($id);
            return new WorksChapterResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getNumChapters(String $id){
        try{
            $work = Works::findOrFail($id);
            $work->loadCount('chapterRelation as chapter_count');

            return response()->json(['chapter_count'=>$work->chapter_count],200);
        }
        catch (Exception $ex) {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getComment(string $id)
    {
        try
        {
            $work = Works::with('commentRelation')->findOrFail($id);
            return new WorksCommentResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getGenres(String $id)
    {
        try
        {
            $work = Works::with('genresRelation')->findOrFail($id);
            return new WorksGenresResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function addGenre(Request $request){
        try{
            $request->validate([
                'work_id' => ['required',Rule::exists('works','work_id')],
                'genre_id' => ['required',Rule::exists('genres','genre_id')]
            ]);
            $work = Works::findOrFail($request->work_id);
            //Check that the user and author are the same
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            if($work->author !== $profile->profile_id){
                return response()->json(['Error'=>'Usuario no autorizado'],500);
            }

            //Check that there are no more than 2 genres
            $work->loadCount('genresRelation as genres');
            if($work->genres >= 2){
                return response()->json(['Error'=>'Cantidad maxima de generos alcanzada'],500);
            }
            $genre = Genres::findOrFail($request->genre_id);
            $work->genresRelation()->syncWithoutDetaching($genre->genre_id);
            $work->load('genresRelation');
            return new WorksGenresResource($work);
        } catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    /**
     * Update the genres of a work (replace all existing genres with the given list, max 2).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|WorksGenresResource
     */
    public function updateGenres(Request $request)
    {
        try {
            $request->validate([
                'work_id' => ['required', Rule::exists('works', 'work_id')],
                'genre_ids' => ['array', 'max:2'],
                'genre_ids.*' => ['uuid', Rule::exists('genres', 'genre_id')],
            ]);

            $work = Works::findOrFail($request->work_id);

            // Authorization: only the author or an admin can edit genres
            $userId = auth()->id();
            $profile = profile::where('user_id', $userId)->first();
            if ($work->author !== $profile->profile_id && !auth()->user()->admin) {
                return response()->json(['Error' => 'Usuario no autorizado'], 403);
            }

            // Sync the genres – this will remove any existing genres and attach the new ones
            $work->genresRelation()->sync($request->genre_ids);
            $work->load('genresRelation');

            return new WorksGenresResource($work);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    public function getReadHistory(string $id)
    {
        try
        {
            $work = Works::with('readHistoriesRelation')->findOrFail($id);
            return new WorksReadHistoryResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getReadList(string $id)
    {
        try
        {
            $work = Works::with('readListsRelation')->findOrFail($id);
            return new WorksReadListResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getCollection(String $id)
    {
        try
        {
            $work = Works::with('collectionsRelation')->findOrFail($id);
            return new WorksCollectionResource($work);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function getWips(string $id){
        try{
            $work = Works::with('wipsRelation')->findOrFail($id);
            return new WorksWipsResource($work);
        }
        catch (Exception $ex){
            return response()->json(['Error'=> $ex->getMessage()],500);
        }
    }

}
