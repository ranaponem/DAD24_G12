<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Game;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function totalProfit(Request $request)
    {
        // Get the desired time range from the request
        $timeRange = $request->input('time_range', 'today'); // Default to 'today'

        // Initialize the query
        $query = Transaction::query();

        // Apply date filters based on the time range
        switch ($timeRange) {
            case 'today':
                $query->where('transaction_datetime', '>=', Carbon::today());
                break;

            case 'this_week':
                $query->where('transaction_datetime', '>=', Carbon::now()->startOfWeek());
                break;

            case 'this_month':
                $query->where('transaction_datetime', '>=', Carbon::now()->startOfMonth());
                break;

            case 'this_year':
                $query->where('transaction_datetime', '>=', Carbon::now()->startOfYear());
                break;

            default:
                return response()->json(['error' => 'Invalid time range'], 400);
        }

        // Calculate the total profit
        $profit = $query->sum('euros');

        return response()->json(['valor' => $profit]);
    }

    public function detailedProfit(Request $request)
    {
        $timeRange = $request->input('time_range', 'this_week');

        // Initialize the data array
        $data = [];

        switch ($timeRange) {
            case 'today':
                // Return hourly data for today
                $data = Transaction::selectRaw('HOUR(transaction_datetime) as hour, SUM(euros) as total')
                    ->whereDate('transaction_datetime', Carbon::today())
                    ->groupBy('hour')
                    ->orderBy('hour')
                    ->get();
                break;

            case 'this_week':
                $data = Transaction::selectRaw('DATE(transaction_datetime) as day, SUM(euros) as total')
                    ->where('transaction_datetime', '>=', Carbon::now()->startOfWeek())
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get();
                break;

            case 'this_month':
                $data = Transaction::selectRaw('DATE(transaction_datetime) as day, SUM(euros) as total')
                    ->where('transaction_datetime', '>=', Carbon::now()->startOfMonth())
                    ->groupBy('day')
                    ->orderBy('day')
                    ->get();
                break;

            case 'this_year':
                $data = Transaction::selectRaw('MONTH(transaction_datetime) as month, SUM(euros) as total')
                    ->where('transaction_datetime', '>=', Carbon::now()->startOfYear())
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                break;

            default:
                return response()->json(['error' => 'Invalid time range'], 400);
        }

        return response()->json($data);
    }

    public function totalUsers(Request $request)
    {
        $timeRange = $request->input('time_range', 'today');

        $query = User::query();

        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;

            case 'this_week':
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                break;

            case 'this_month':
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                break;

            case 'this_year':
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
                break;

            default:
                return response()->json(['error' => 'Invalid time range'], 400);
        }

        // Count the total number of users
        $totalUsers = $query->count();

        // Fetch top 5 spenders by total euros spent
        $topSpenders = Transaction::selectRaw('user_id, SUM(euros) as total_spent')
            ->whereHas('user', function ($q) use ($timeRange) {
                switch ($timeRange) {
                    case 'today':
                        $q->whereDate('created_at', Carbon::today());
                        break;
                    case 'this_week':
                        $q->where('created_at', '>=', Carbon::now()->startOfWeek());
                        break;
                    case 'this_month':
                        $q->where('created_at', '>=', Carbon::now()->startOfMonth());
                        break;
                    case 'this_year':
                        $q->where('created_at', '>=', Carbon::now()->startOfYear());
                        break;
                }
            })
            ->groupBy('user_id')
            ->orderByDesc('total_spent')
            ->with('user:id,name,nickname,email') // Fetch user's name and email for display
            ->take(5)
            ->get();

        return response()->json([
            'time_range' => $timeRange,
            'total_users' => $totalUsers,
            'top_spenders' => $topSpenders->map(function ($transaction) {
                return [
                    'user_id' => $transaction->user->id,
                    'name' => $transaction->user->name,
                    'nickname' => $transaction->user->nickname,
                    'email' => $transaction->user->email,
                    'total_spent' => $transaction->total_spent,
                ];
            }),
        ]);
    }

    public function totalGames(Request $request)
    {
        // Get the desired time range from the request
        $timeRange = $request->input('time_range', 'today'); // Default to 'today'

        // Initialize the query
        $query = Game::query();

        // Apply date filters based on the time range using the 'created_at' column
        switch ($timeRange) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;

            case 'this_week':
                $query->where('created_at', '>=', Carbon::now()->startOfWeek());
                break;

            case 'this_month':
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                break;

            case 'this_year':
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
                break;

            default:
                return response()->json(['error' => 'Invalid time range'], 400);
        }

        // Count the total number of games
        $totalGames = $query->count();

        // Count the games grouped by type
        $gamesByType = $query->select('type', \DB::raw('COUNT(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => $item->count];
            });

        return response()->json([
            'time_range' => $timeRange,
            'total_games' => $totalGames,
            'games_by_type' => [
                'multiplayer' => $gamesByType[Game::TYPE_MULTIPLAYER] ?? 0,
                'singleplayer' => $gamesByType[Game::TYPE_SINGLEPLAYER] ?? 0,
            ],
        ]);
    }

}
