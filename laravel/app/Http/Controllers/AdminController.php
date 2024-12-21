<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private const PHOTO_PATH = 'public/photos/'; 
    public function store(StoreUserRequest $request) {
        $user = new User();
        $user->fill($request->validated());
        $user->type = User::TYPE_ADMIN;
        $user->blocked = User::UNBLOCKED;
        $user->brain_coins_balance = 0;

        $user->save();

        if($request->hasFile('photo_image')) {
            $path = $request->photo_image->store(AdminController::PHOTO_PATH, 'public');
            $user->photo_filename = basename($path);
            $user->save();
        }

        return new UserResource($user);
    }

    public function updatePlayerState(User $user) {
        $user->blocked = $user->blocked == User::UNBLOCKED ? User::BLOCKED : User::UNBLOCKED;

        $user->save();

        return new UserResource($user);
    }

    public function destroy(User $user) {
        $photo = $user->photo_filename;

        if (count($user->createdGames) > 0 || count($user->wonGames) > 0 || count($user->transactions) > 1) {
            $user->brain_coins_balance = 0;
            $user->delete();
        } else {
            DB::transaction(function () use ($user) {
                if (count($user->transactions) == 1) {    
                    $user->transactions->first()->delete();
                }
                $user->forceDelete();
            });
        }

        if($photo && Storage::fileExists(AdminController::PHOTO_PATH . $photo)) {
            Storage::delete(AdminController::PHOTO_PATH . $photo);
        }

        return response()->json(null, 204);
    }
}
