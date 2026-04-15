<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $nextTraining = Training::with(['instrument', 'sheet'])
            ->where('user_id', $user->id)
            ->where('date_training', '>=', now())
            ->orderBy('date_training', 'asc')
            ->first();

        return inertia('Dashboard', [
            'nextTraining' => $nextTraining
        ]);
    }
}
