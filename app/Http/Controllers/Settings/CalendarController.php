<?php

namespace App\Http\Controllers\Settings;

use App\Models\Training;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{


    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        return Inertia::render('Calendar', [
            'currentUserId' => $currentUserId
        ]);
    }

    public function events(Request $request)
    {
        $userId = Auth::id();
        $start = $request->start ? Carbon::parse($request->start) : now()->startOfMonth();
        $end = $request->end ? Carbon::parse($request->end) : now()->endOfMonth();

        $trainings = Training::where('user_id', $userId)
            ->whereDate('date_training', '>=', $start)
            ->whereDate('date_training', '<=', $end)
            ->get();

        return response()->json(
            $trainings->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->name,
                'instrument' => $t->instrument,
                'link' => $t->link,
                'start' => Carbon::parse($t->date_training)->toIso8601String(),
                'end' => Carbon::parse($t->date_training)->addMinutes($t->duration ?? 0)->toIso8601String(),
            ])
        );
    }

    public function store(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $userId = Auth::id();

        $training = Training::create([
            'name' => $request->title,
            'instrument' => $request->instrument,
            'link' => $request->link,
            'date_training' => $start,
            'duration' => $start->diffInMinutes($end),
            'user_id' => $userId,
        ]);

        return response()->json([
            'id' => $training->id,
            'title' => $training->name,
            'instrument' => $training->instrument,
            'link' => $training->link,
            'start' => $training->date_training->toIso8601String(),
            'end' => $training->date_training->copy()->addMinutes($training->duration)->toIso8601String(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);
        $start = $request->start ? Carbon::parse($request->start) : $training->date_training;
        $end = $request->end ? Carbon::parse($request->end) : $training->date_training->copy()->addMinutes($training->duration);

        $training->update([
            'name' => $request->title ?? $training->name,
            'instrument' => $request->instrument ?? $training->instrument,
            'link' => $request->link ?? $training->link,
            'date_training' => $start,
            'duration' => $start->diffInMinutes($end),
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        $training = Training::findOrFail($id);
        $training->delete();

        return response()->json(['success' => true]);
    }
}
