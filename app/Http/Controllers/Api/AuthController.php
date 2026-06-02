<?php

namespace App\Http\Controllers\Api;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profiles\StoreProfileRequest;
use App\Http\Resources\Profiles\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\profile;
use DB;
use Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Str;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        try
        {
            $creator = new CreateNewUser();
            $user = $creator->create($request->all());

            // Create a default profile for the new user
            $profile = profile::create([
                'user_id'     => $user->user_id,
                'name'        => 'Usuario',
                'nickname'    => 'Apodo',
                'description' => 'Nuevo Usuario',
                'avatar'      => 'avatars/default.jpeg',
            ]);

            DB::commit();

            $token = $user->createToken('auth-token')->plainTextToken;

            //'user' => new UserResource($user),
            return response()->json([
                'profile' => new ProfileResource($profile),
                'token' => $token,
            ], 201);
        }
        catch (Exception $ex)
        {
            return response()->json(['Error'=>$ex->getMessage(), 'Sent'=>$request->all()], 422);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email','password')))
        {
            throw ValidationException::withMessages([
                'email'=>['Las credenciales proporcionadas son incorrectas.']
            ]);
        }

        $user = User::where('email',$request->email)->firstOrFail();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Sesion cerrada corrctamente']);
    }

    public function user(Request $request)
    {
        $user = $request->user();
        $profile = profile::where('user_id',$user->user_id)
        ->withCount('worksRelation as works_count')
        ->withCount('collectionsRelation as collections_count')
        ->first();
        return new ProfileResource($profile);
    }

    public function adminCheck(Request $request){
        try{
            $user = $request->user();
            return response()->json(['admin' => (bool) $user->admin]);
        } catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $updater = new UpdateUserProfileInformation();
        $updater->update($user,$request->all());
        return response()->json(['message'=>'Perfil actualizado','user'=>$user]);
        
    }

    public function updateAvatar(Request $request){
        try{
            $request->validate([
                'image'=>'required|file|max:2048'
            ]);

            //get the use id
            $id = auth()->id();

            //Search for work
            $user = User::findOrFail($id);

            //get the extension of the file
            $image = $request->file('image');
            $extension = $image->getClientOriginalExtension();

            //Delete if it exists
            if($user->avatar && Storage::dis('public')->exists($user->cover) && $user->avatar != 'avatars/default.jpeg'){
                Storage::disk('public')->delete($user->cover);
            }

            //generate unique file name
            $fileName = Str::uuid().'.'.$extension;
            $path = 'covers/'.$fileName;

            // $stored = Storage::disk('public')->put($path,file_get_contents($image));
            $stored = $image->storeAs('',$path,'public');
            if(!$stored){
                return response()->json(['Error'=> 'Error al crear archivo'],500);
            }

            $user->avatar = $path;
            $user->save();

            return response()->json(['Mensaje'=>'Avatar guardado con exito','Path'=>$path],200);
        }
        catch (Exception $ex){
            return response()->json(['Error'=>$ex->getMessage()],500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user();

        if(!Hash::check($request->current_password, $user->password))
        {
            throw ValidationException::withMessages([
                'current_password' => ['La contraseña actual no coincide.'],
            ]);
        }

        $updater = new UpdateUserPassword();
        $updater->update($user,$request->all());

        return response()->json(['message'=>'Contraseña actualizada']);
    }
}