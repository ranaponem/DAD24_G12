<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\TransactionTypeRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Game;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TransactionController extends Controller
{
    //REMOVE BEFORE PRODUCTION
    private const DEBUG = false;
    private const externalApp = "https://dad-202425-payments-api.vercel.app/api/debit";
    private const websocket = "ws:/192.168.0.211:8086";

    /**
     * Display a listing of the resource.
     */
    public function index(TransactionTypeRequest $request)
    {
        $query = Transaction::query();
        $query = $this->filterQuery($request, $query);
        return TransactionResource::collection($query->paginate(10));
    }

    /**
     * Show my transactions
     */
    public function showMy(TransactionTypeRequest $request) {
        $query = Transaction::where('user_id', $request->user()->id);
        $query = $this->filterQuery($request, $query);
        return TransactionResource::collection($query->paginate(10));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionRequest $request)
    {   
        $user = $request->user();
        $requestValidated = $request->validated();

        $time = Carbon::now();

        if ($user->brain_coins_balance + (int)$requestValidated['brain_coins'] < 0)
            return response()->json(['message' => 'Insufficient balance.'], 400);

        if (TransactionController::DEBUG && $requestValidated['type'] == Transaction::TYPE_PURCHASE) {
            $response = Http::post(TransactionController::externalApp, [
                'type' => $requestValidated['payment_type'],
                'reference' => $requestValidated['payment_ref'],
                'value' => $requestValidated['brain_coins'] / Transaction::EURO_TO_COIN_RATIO,
            ]);

            if ($response->clientError())
                return response()->json(['message'=> $response->json()['message']],400);
            
            if ($response->serverError())
                return response()->json(['message'=> 'Something went wrong when trying to reach the payment server', 500]);
        }

        $transaction = DB::transaction(function () use ($user, $requestValidated, $time) {
            $transaction = new Transaction();
            $transaction->fill($requestValidated);

            switch ($requestValidated['type']) {
                case Transaction::TYPE_PURCHASE:
                    $transaction->euros = $requestValidated['brain_coins'] / Transaction::EURO_TO_COIN_RATIO;                    

                    break;
                case Transaction::TYPE_INTERNAL:
                    $game = Game::where('id', $requestValidated['game_id'])->first();
                    if ($game->status == 'E') {
                        throw new \Exception("Game with id $game->id already ended");
                    }
                    $transaction->game_id = $game->id;
                    break;
            };

            $transaction->user_id = $user->id;
            $transaction->transaction_datetime = $time;
            $transaction->save();

            $user->brain_coins_balance += (int)$requestValidated['brain_coins']; 
                            
            $user->save();

            return $transaction;
        });

        $transaction->transaction_datetime = $time->isoFormat("YYYY-mm-DD HH:MM:ss");

        return new TransactionResource($transaction);
    }

    private function filterQuery(TransactionTypeRequest $request, $query) {
        if ($request->type) {
            $query->where('type', $request->type);
            $query->when($request->type == Transaction::TYPE_PURCHASE, function ($query) use ($request) {
                if ($request->has('euros_lower_limit'))
                    $query->where('euros', '>=', $request->euros_lower_limit);
                if ($request->has('euros_higher_limit'))
                    $query->where('euros', '<=', $request->euros_higher_limit);
                return $query;
            })->when($request->type == Transaction::TYPE_INTERNAL, function ($query) use ($request) {
                if ($request->has('reward'))
                    $query->where('brain_coins', $request->reward ? '>' : '<', 0);
                return $query;
            });
        }

        return $query;
    }
}
