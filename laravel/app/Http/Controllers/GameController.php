<?php

namespace App\Http\Controllers;

use App\Http\Requests\GameTypeRequest;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Board;
use App\Http\Resources\GameResource;
use App\Http\Requests\CreateGameRequest;
use App\Http\Requests\UpdateGameRequest;
use Carbon\Carbon;
use PhpParser\Node\Expr\AssignOp\Mul;

class GameController extends Controller
{
    public function index(GameTypeRequest $request)
    {
        // Filter games that have ended and are associated with a valid user
        $query = Game::whereHas('creator');

        // Apply additional filters using readAttributes if present
        $attributesResponse = $this->readAttributes($request, $query);

        // Return the response from readAttributes or a paginated collection of games
        return $attributesResponse ?? GameResource::collection($query->paginate(10));
    }

    public function indexEndedGames(GameTypeRequest $request)
    {
        // Filter games that have ended and are associated with a valid user
        $query = Game::where('status', GAME::STATUS_ENDED)
            ->whereHas('creator');

        // Apply additional filters using readAttributes if present
        $attributesResponse = $this->readAttributes($request, $query);

        // Return the response from readAttributes or a paginated collection of games
        return $attributesResponse ?? GameResource::collection($query->paginate(10));
    }

    public function showMy(GameTypeRequest $request)
    {
        $userId = $request->user()->id;
        $type = $request->query('type') ?? Game::TYPE_SINGLEPLAYER;

        $query = Game::when($type == Game::TYPE_SINGLEPLAYER, function ($query) use ($userId) {
            return $query->where('created_user_id', $userId)->where('type', Game::TYPE_SINGLEPLAYER);
            })
            ->when($type == Game::TYPE_MULTIPLAYER, function ($query) use ($userId) {
                return $query->whereHas('multiplayerGamesPlayed', function ($subQuery) use ($userId) {
                        $subQuery->where('user_id', $userId);
                    });
            });

        $attributesResponse = $this->readAttributes($request, $query);

        return $attributesResponse ?? GameResource::collection($query->paginate(10));
    }

    public function store(CreateGameRequest $request)
    {
        $requestValidated = $request->validated();
        $user = $request->user();

        $payment = 0;
        if ($requestValidated['board_id'] != 1)
        $payment = 1;

        if ($user->brain_coins_balance - $payment < 0)
        return response()->json(['message' => 'Insufficient balance.'], 400);


        $time = now();

        $game = DB::transaction(function () use ($requestValidated, $user, $time, $payment) {

            $game = new Game();
            $game->fill($requestValidated);
            $game->created_user_id = $user->id;
            $game->type = Game::TYPE_SINGLEPLAYER;

            $game->status = Game::STATUS_PROGRESS;
            $game->began_at = $time;

            $game->save();

            if($payment != 0){
                $transaction = $this->gameTransaction($user->id, $game->id, Transaction::TYPE_INTERNAL, -$payment, $time);

                $user->brain_coins_balance += $transaction->brain_coins;
                $user->save();
            }

            return $game;
        });

        $game->began_at = $time->isoFormat("YYYY-mm-DD HH:MM:ss");

        return new GameResource($game);
    }

    public function update(UpdateGameRequest $request, Game $game)
    {
        if($game->status == $request->status)
        return response()->json(['message' => 'Game is already in that state'], 400);

        if($game->status == Game::STATUS_ENDED || $game->status == Game::STATUS_INTERRUPTED)
        return response()->json(['message' => 'Cannot update ended or interrupted games'], 400);

        if ($game->status == Game::STATUS_PROGRESS && $request->status == Game::STATUS_PENDING)
        return response()->json(['message' => 'Cannot put a game in progress back to pending'], 400);

        $requestValidated = $request->validated();

        $game->fill($requestValidated);
        $game->ended_at = now();
        $game->total_time = Carbon::parse($game->ended_at)->diffInSeconds(Carbon::parse($game->began_at));

        $game->save();

        return new GameResource($game);
    }

    private function readAttributes(Request $request, $query)
    {
        // Filter by board
        if ($request->has('board')) {
            $board_size = explode("x", $request->query('board'));
            $board = Board::where('board_cols', $board_size[0])->where('board_rows', $board_size[1])->first();
            if ($board == null)
            return response()->json(['message' => 'Invalid board size: board not found'], 404);
            $query->where('board_id', $board->id);
        }

        // Order by either number of turns or time taken (score)
        if ($request->has('score_type')) {
            if ($request->query('score_type') === 'time')
        $query->orderBy('total_time', 'asc')->orderBy('ended_at', 'asc')->orderBy('total_turns_winner', 'asc');
            else if ($request->query('score_type') === 'turns')
        $query->orderBy('total_turns_winner', 'asc')->orderBy('ended_at', 'asc')->orderBy('total_time', 'asc');
        }
        else{
            $query->orderBy('began_at', 'desc');
        }

        // Filter by type, defaults to singleplayer
        $type = $request->query('type');
        if ($type != 'A')
        $query->where('type', $type ?? Game::TYPE_SINGLEPLAYER);

        // Filter by a start date (all games played after it are gotten)
        if ($request->has('date_start')) {
            $dateStart = $request->query('date_start');
            $parsedDateStart = \DateTime::createFromFormat('d-m-Y', $dateStart);

            // Filter by a start date (all games played after it are gotten)
            if($request->has('date_start')){
                $dateStart = $request->query('date_start');
                $parsedDateStart = \DateTime::createFromFormat('d-m-Y', $dateStart);

                if($parsedDateStart)
                $query->where('ended_at', '>=', $parsedDateStart->format('Y-m-d'));

                else
                return response()->json(['message' => 'Invalid \'date_start\' date format: must be DD-mm-YYYY'], 402);
            }

            // Filter by end date (all games played before it are gotten)
            if($request->has('date_end')){
                $dateEnd = $request->query('date_end');
                $parsedDateEnd = \DateTime::createFromFormat('d-m-Y', $dateEnd);

                if($parsedDateEnd)
                $query->where('ended_at', '<=', $parsedDateEnd->format('Y-m-d').' 23:59:59');

                else
                return response()->json(['message' => 'Invalid \'date_end\' date format: must be DD-mm-YYYY'], 402);
            }
        }

        // Filter by end date (all games played before it are gotten)
        if ($request->has('date_end')) {
            $dateEnd = $request->query('date_end');
            $parsedDateEnd = \DateTime::createFromFormat('d-m-Y', $dateEnd);

            if ($parsedDateEnd)
            $query->where('ended_at', '<=', $parsedDateEnd->format('Y-m-d') . ' 23:59:59');
            else
            return response()->json(['message' => 'Invalid \'date_end\' date format: must be DD-mm-YYYY'], 402);
        }
    }
    private function gameTransaction($userId, $gameId, $type, $amount, $time)
    {
        $transaction = new Transaction();
        $transaction->user_id = $userId;
        $transaction->game_id = $gameId;
        $transaction->type = $type;
        $transaction->brain_coins = $amount;
        $transaction->transaction_datetime = $time;

        $transaction->save();
        return $transaction;
    }
}
