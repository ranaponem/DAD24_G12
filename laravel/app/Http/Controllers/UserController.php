<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private const PHOTO_PATH = 'public/photos/'; 

    public function showMe(Request $request) {
        return new UserResource($request->user());
    }

    public function index() {
        return UserResource::collection(User::where('type', 'P')->get());
    }

    public function show(int $id) {
        return new UserResource(User::find($id));
    }

    public function store(StoreUpdateUserRequest $request) {
        $user = new User();
        $user->fill($request->validated());
        $user->type = User::TYPE_PLAYER;
        $user->blocked = User::UNBLOCKED;
        $user->brain_coins_balance = 0;
        $user = UserController::savePhoto($request, $user);
        $user->save();

        return new UserResource($request->user());
    }

    public function update(StoreUpdateUserRequest $request, User $user) {
        $user->fill($request->validated());
        $user = UserController::savePhoto($request, $user);
        $user->save();

        return new UserResource($user);
    }
    
    public function destroy(User $user) {
        $user->delete();

        return response()->json(null, 204);
    }

    private function savePhoto(StoreUpdateUserRequest $request, User $user) {
        if($request->hasFile('photo')) {
            if($user->photo_filename && Storage::fileExists(UserController::PHOTO_PATH . $user->photo_filename)) {
                Storage::delete(UserController::PHOTO_PATH . $user->photo_filename);
            }
            $path = $request->photo_image->store(UserController::PHOTO_PATH);
            $user->photo_filename = basename($path);
        }

        return $user;
    }
}
