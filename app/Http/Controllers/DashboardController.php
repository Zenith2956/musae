<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use Illuminate\Support\Carbon;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    #[OA\Get(
        path: "/test",
        summary: "Route de test Swagger",
        tags: ["Test"]
    )]
    public function test()
    {
        return response()->json(["message" => "OK"]);
    }

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









