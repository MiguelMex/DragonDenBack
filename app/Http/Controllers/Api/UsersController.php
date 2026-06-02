<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\profile;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use Illuminate\Validation\Rule;
use Request;

class UsersController extends Controller
{
    /**
     * Check if the authenticated user is admin.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    private function ensureAdmin()
    {
        if (!auth()->user() || !auth()->user()->admin) {
            abort(403, 'Unauthorized. Admin access required.');
        }
    }

    /**
     * Display a listing of users (admin only).
     */
    public function index()
    {
        $this->ensureAdmin();
        try {
            $users = User::with(['profileRelation' => function($query){
                $query->withCount('worksRelation as works_count')
                ->withCount('collectionsRelation as collections_count');
            }])
            ->get();
            return UserResource::collection($users);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Store a newly created user (admin only).
     */
    public function store(StoreUserRequest $request)
    {
        $this->ensureAdmin();
        try {
            $validated = $request->validated();
            $validated['password'] = Hash::make($validated['password']);
            $user = User::create($validated);

            // Create a default profile for the new user
            $profile = profile::create([
                'user_id'     => $user->user_id,
                'name'        => 'Usuario',
                'nickname'    => 'Apodo',
                'description' => 'Nuevo Usuario',
                'avatar'      => 'avatars/default.jpeg',
            ]);

            DB::commit();

            $user->load(['profileRelation' => function($query){
                $query->withCount('worksRelation as works_count')
                ->withCount('collectionsRelation as collections_count');
            }]);
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Display the specified user.
     */
    public function show(string $id)
    {
        try {
            $user = User::with(['profileRelation' => function($query){
                $query->withCount('worksRelation as works_count')
                ->withCount('collectionsRelation as collections_count');
            }])
            ->findOrFail($id);
            // Allow showing own profile or admin view
            if (auth()->id() !== $user->user_id && !auth()->user()->admin) {
                abort(403, 'Unauthorized.');
            }
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Update the specified user (admin only).
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $this->ensureAdmin();
        try {
            $user = User::findOrFail($id);
            $validated = $request->validated();
            if (isset($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            }
            $user->update($validated);
            $user->load(['profileRelation' => function($query){
                $query->withCount('worksRelation as works_count')
                ->withCount('collectionsRelation as collections_count');
            }]);
            return new UserResource($user);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }

    /**
     * Remove the specified user (admin only).
     */
    public function destroy(string $id)
    {
        $this->ensureAdmin();
        try {
            $user = User::findOrFail($id);
            // Prevent admin from deleting themselves? Optional
            if ($user->user_id === auth()->id()) {
                return response()->json(['Error' => 'Cannot delete your own account.'], 403);
            }
            $user->delete();
            return response()->json(['Message' => 'Usuario eliminado correctamente']);
        } catch (Exception $ex) {
            return response()->json(['Error' => $ex->getMessage()], 500);
        }
    }
}
