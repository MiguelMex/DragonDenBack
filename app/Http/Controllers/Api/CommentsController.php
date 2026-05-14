<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comments\StoreCommenstRequest;
use App\Http\Requests\Comments\UpdateCommenstRequest;
use App\Http\Resources\Comments\CommensChapterResource;
use App\Http\Resources\Comments\CommensChildrenResource;
use App\Http\Resources\Comments\CommensParentResource;
use App\Http\Resources\Comments\CommensUserResource;
use App\Http\Resources\CommentsResource;
use App\Models\Comments;
use App\Models\profile;
use Exception;

class CommentsController extends Controller
{
    /**
     * get all the comments
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(){
        try
        {
            $comments = Comments::all();
            return CommentsResource::collection($comments);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'->$ex->getMessage()]);
        }
    }

    /**
     * get a comment by the id
     * @param string $id
     * @return CommentsResource|\Illuminate\Http\JsonResponse
     */
    public function show(string $id){
        try
        {
            $comment = Comments::findOrFail($id);
            if($comment == null)
            {
                return response()->json(['Error' => 'Comentario no encontrado']);
            }

            return new CommentsResource($comment);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'->$ex->getMessage()]);
        }
    }

    /**
     * create a comment
     * @param StoreCommenstRequest $request
     * @return CommentsResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreCommenstRequest $request){
        try
        {
            $validated = $request->validated();
            $id = auth()->id();
            $profile = profile::where('user_id',$id)->first();
            $validated['user'] = $profile->profile_id;
            $comment = Comments::create($validated);
            return new CommentsResource($comment);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'->$ex->getMessage()]);
        }
    }

    /**
     * update a comment
     * @param UpdateCommenstRequest $request
     * @param string $id
     * @return CommentsResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateCommenstRequest $request, string $id){
        try
        {
            $comment = Comments::findOrFail($id);
            $comment->update($request->validated());
            $comment->touch();
            return new CommentsResource($comment);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'->$ex->getMessage()]);
        }
    }

    /**
     * delete an existing comment
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id){
        try
        {
            $comment = Comments::findOrFail($id)->delete();
            return response()->json(['Mensaje'=>'Comentario eliminado correctamente']);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'->$ex->getMessage()]);
        }
    }
    
    /**
     * return the chapter the comment was made on
     * @param string $id
     * @return CommensChapterResource|\Illuminate\Http\JsonResponse
     */
    public function chapter(string $id)
    {
        try
        {
            $comment = Comments::with('chapterRelation')->findOrFail($id);            
            return new CommensChapterResource($comment);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * returns the user that made the comment
     * @param string $id
     * @return CommensUserResource|\Illuminate\Http\JsonResponse
     */
    public function user(string $id)
    {
        try
        {
            $comment = Comments::with('userRelation')->findOrFail($id);            
            return new CommensUserResource($comment);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * returns the parent of the comment
     * @param string $id
     * @return CommensParentResource|\Illuminate\Http\JsonResponse
     */
    public function parent(string $id)
    {
        try
        {
            $comment = Comments::with('parent')->findOrFail($id);            
            return new CommensParentResource($comment);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }

    /**
     * returns the children of the comment
     * @param string $id
     * @return CommensChildrenResource|\Illuminate\Http\JsonResponse
     */
    public function children(string $id)
    {
        try
        {
            $comment = Comments::with('children')->findOrFail($id);            
            return new CommensChildrenResource($comment);
        }
        catch(Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage()]);
        }
    }
}
