<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chapters\StoreChaptersRequest;
use App\Http\Requests\Chapters\UpdateChaptersRequest;
use App\Http\Resources\Chapters\ChaptersDraftResource;
use App\Http\Resources\Chapters\ChaptersHistoryResource;
use App\Http\Resources\Chapters\ChaptersWorkResource;
use App\Http\Resources\ChaptersResource;
use App\Models\Chapters;
use App\Models\Drafts;
use App\Models\profile;
use App\Models\Works;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Validation\Rule;

class ChaptersController extends Controller
{
    /**
     * Get all chapters
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        try 
        {
            $chapters = Chapters::all();
            return ChaptersResource::collection($chapters);
        } catch (Exception $ex) {
            return response()->json(['error' => 'Error: '. $ex->getMessage()]);
        }
    }

    /**
     * Show a Chapter by the id
     * @param mixed $id
     * @return ChaptersResource|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try
        {
            $chapter = Chapters::findOrFail($id);
            return new ChaptersResource($chapter);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error: '=>$ex->getMessage()]);
        }
    }

    /**
     * Create a new chapter
     * @param StoreChaptersRequest $request
     * @return ChaptersResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreChaptersRequest $request)
    {
        try
        {
            $validated = $request->validated();
            $exist = Chapters::where([
                'name'=>$validated['name'],
                'work'=>$validated['work'],
            ])->exists();

            if ($exist)
            {
                return response()->json(['Error'=>'Ese nombre ya existe en este trabajo']);
            }

            //See that the draft belongs to the same chapter
            $work = Works::findOrFail($validated['work']);
            $draft = Drafts::findOrFail($validated['draft']);

            if($work->work_in_progress != $draft->work_in_progress){
                return response()->json(['Error'=>'El borrador no coincide con el proyecto dado'],500);
            }

            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            if ($work->author !== $profile->profile_id) {
                return response()->json(['Error' => 'No autorizado para agregar capítulos a este trabajo'], 403);
            }

            // Ensure the draft file exists
            $draftPath = $draft->route;
            if (!Storage::disk('local')->exists($draftPath)) {
                return response()->json(['Error' => 'El archivo del borrador no existe'], 404);
            }
            $content = Storage::disk('local')->get($draftPath);

            //Storage system
            $fileName = 'chapters/'.Str::uuid().'.json';

            $stored = Storage::disk('public')->put($fileName, $content);
            if(!$stored){
                return response()->json(['Error'=> 'Error al crear archivo'],500);
            }

            $validated['route'] = $fileName;
            $validated['cover'] = 'covers/default.jpeg';
            $chapter = Chapters::create($validated);
            return new ChaptersResource($chapter);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Almacenar el contenido del capitulo
     * @param Request request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateContent(Request $request){
        try{    
            $request->validate([
                'id' => ['required',Rule::exists('chapters','chapter_id')]
            ]);

            $chapter = Chapters::find($request->id);
            if($chapter == null){
                return response()->json(['Error'=> 'Error al obtener el capitulo'],500);
            }

            //Obtain the draft file
            $draft = Drafts::findOrFail($chapter->draft);

            //obtain file
            $filePath = $draft->route;
            $file = Storage::disk('local')->get($filePath);
            
            //put the content in the chapter file
            $chapterPath = $chapter->route;
            $updated = Storage::disk('public')->put($chapterPath,$file);
            if(!$updated){
                return response()->json(['Error'=> 'Error al actualizar archivo'],500);
            }

            $chapter->touch();

            return response()->json(['Mensaje'=> 'Contenido actualizado exitosamente'],200);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>'Error al modificar archivo: '.$ex->getMessage()]);
        }
    }

    /**
     * store the content of a cover in the route of the model
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeCover(Request $request){
        try{
            $request->validate([
                'id'=>['required',Rule::exists('works','work_id')],
                'image'=>'required|file|max:2048'
            ]);

            //Search for work
            $chapter = Chapters::findOrFail($request->id);

            //see that is the author trying to modify the cover
            $work = Works::findOrFail($chapter->work);
            if($work->author !== auth()->id()){
                return response()->json(['Error'=> 'Usuario no autorizado'],500);
            }

            //get the extension of the file
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();

            //Delete if it exists
            if($chapter->cover && Storage::disk('public')->exists($work->cover) && $chapter->cover != 'covers/default.jpeg'){
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

            $chapter->cover = $path;
            $chapter->save();
            $chapter->touch();

            return response()->json(['Mensaje'=>'Portada guardada con exito','Path'=>$path],200);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>"Error al guardar la portada: ".$ex->getMessage()]);
        }
    }

    /**
     * returns the content of a work
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function content(Request $request){
        try{
            $request->validate([
                'id'=>'required',
            ]);

            $id = $request->id;
            $chapter = Chapters::findOrFail($id);
            $content = Storage::disk('public')->get($chapter->route);
            //return response()->json([$chapter->route]);
            return response()->json(json_decode($content),200);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>'Error al obtener el contenido del archivo: '.$ex->getMessage()],500);
        }
    }

    /**
     * Update an existing chapter
     * @param UpdateChaptersRequest $request
     * @param string $id
     * @return ChaptersResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateChaptersRequest $request, string $id)
    {
        try
        {
            $chapter = Chapters::findOrFail($id);
            $chapter->update($request->validated());
            $chapter->touch();
            return new ChaptersResource($chapter);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Delete a chapter
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        try
        {
            $chapter = Chapters::findOrFail($id);
            if($chapter->route && Storage::disk('public')->exists($chapter->route)){
                Storage::disk('public')->delete($chapter->route);
            }
            $chapter->delete();
            return response()->json(['Mensaje'=>'Capitulo eliminado']);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * Get the work of the chapter
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|ChaptersWorkResource
     */
    public function getWork(string $id)
    {
        try
        {
            $chapter = Chapters::with('workRelation')->findOrFail($id);
            return new ChaptersWorkResource( $chapter );
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * return all read histories appearanc of this chapter
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|ChaptersHistoryResource
     */
    public function getReadHistory(string $id)
    {
        try
        {
            $chapter = Chapters::with('historyRelation')->findOrFail($id);
            return new ChaptersHistoryResource($chapter);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * return the draft corresponding to that chapter
     * @param string $id
     * @return \Illuminate\Http\JsonResponse|ChaptersDraftResource
     */
    public function getDraft(string $id){
        try
        {
            $chapter = Chapters::with('draftRelation')->findOrFail($id);
            return new ChaptersDraftResource($chapter);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }
}
