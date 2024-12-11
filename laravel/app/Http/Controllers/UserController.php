<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    private const PHOTO_PATH = 'public/photos/'; 

    public function showMe(Request $request) {
        return new UserResource($request->user());
    }

    public function index() {
        return UserResource::collection(User::paginate(20));
    }

    public function show(User $user) {
        return new UserResource($user);
    }

    public function showMyBalance(Request $request) {
        return response()->json(["brain_coins_balance" => $request->user()->brain_coins_balance], 200);
    }

    public function store(StoreUserRequest $request) {
        if (!$request->has('password_confirmation') || $request->password != $request->password_confirmation)
            return response()->json(["message"=> "Please confirm the password"],400);

        $user = DB::transaction(function () use ($request) {
            $user = new User();
            $user->fill($request->validated());
            $user->type = User::TYPE_PLAYER;
            $user->blocked = User::UNBLOCKED;
            $user->brain_coins_balance = 0;
            $user->save();
            
            if($request->hasFile('photo')) {
                $path = $request->photo_image->store(UserController::PHOTO_PATH);
                $user->photo_filename = basename($path);
                $user->save();
            }

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->transaction_datetime = Carbon::now();
            $transaction->type = Transaction::TYPE_BONUS;
            $transaction->brain_coins = 10;

            $transaction->save();            

            $user->brain_coins_balance =+ 10;
            $user->save();

            return $user;
        });

        if ($user == null)
            response()->json(["message" => "Something went wrong when creating the user"],500);

        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request) {
        $user = $request->user();

        $user->fill($request->validated());
        $user->save();

        if($request->hasFile('photo')) {
            if($user->photo_filename && Storage::fileExists(UserController::PHOTO_PATH . $user->photo_filename)) {
                Storage::delete(UserController::PHOTO_PATH . $user->photo_filename);
            }
            $path = $request->photo_image->store(UserController::PHOTO_PATH);
            $user->photo_filename = basename($path);
            $user->save();
        }

        return new UserResource($user);
    }
    
    public function updatePassword(Request $request) {
        $user = $request->user();

        $request->validateWithBag('userDeletion', [
            'old_password' => ['required','string','current_password'],
            "password"=> ["required","string", Password::min(8)->letters()->numbers(), "confirmed"],
        ]);
        
        $user->fill($request->validated());
        $user->save();

        return response()->json($user,200);
    }

    public function destroy(Request $request) {
        $user = $request->user();

        $request->validateWithBag('userDeletion', [
            'password' => ['required','string','current_password'],
        ]);

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

        if($photo && Storage::fileExists(UserController::PHOTO_PATH . $photo)) {
            Storage::delete(UserController::PHOTO_PATH . $user->photo_filename);
        }

        return response()->json(null, 204);
    }
}
