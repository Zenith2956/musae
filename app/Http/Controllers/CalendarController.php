<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BaseController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use App\Models\GenericInstrument;
use Illuminate\Support\Facades\Date;

class CalendarController extends BaseController
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

        $start = $request->start
            ? Carbon::parse($request->start)
            : now()->startOfMonth();

        $end = $request->end
            ? Carbon::parse($request->end)
            : now()->endOfMonth();

        $trainings = Training::with('instrument')
            ->where('user_id', $userId)
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('date_training', [$start, $end])
                ->orWhereBetween('end_training', [$start, $end]);
            })
            ->get();

        return response()->json(
            $trainings->map(fn($t) => [
                'id' => $t->id,
                'title' => $t->name,
                'instrument_id' => $t->instrument_id,
                'instrument' => $t->instrument?->name, // 👈 IMPORTANT
                'link' => $t->link,
                'start' => $t->date_training->toIso8601String(),
                'end' => $t->date_training->copy()->addMinutes($t->duration)->toIso8601String(),
            ])
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'start' => 'required|date',
            'end' => 'required|date',
            'instrument_id' => 'nullable|integer',
            'link' => 'nullable|string',
        ]);

        $userId = Auth::id();

        $start = Carbon::parse($validated['start']);
        $end = Carbon::parse($validated['end']);

        $training = Training::create([
            'name' => $validated['title'],
            'instrument_id' => $validated['instrument_id'],
            'link' => $validated['link'],
            'date_training' => $start,
            'end_training' => $end,
            'duration' => $start->diffInMinutes($end),
            'user_id' => $userId,
        ]);

        return response()->json($this->formatEvent($training));
    }

    public function update(Request $request, $id)
    {
        Log::info('UPDATE EVENT', $request->all());
        $training = Training::findOrFail($id);

        if ($training->user_id !== Auth::id()) {
            abort(403);
        }

        $start = Carbon::parse($request->start ?? $training->date_training);
        $end = Carbon::parse($request->end ?? $training->date_training->copy()->addMinutes($training->duration));

        $training->update([
            'name' => $request->title ?? $training->name,
            'instrument_id' => $request->instrument_id ?? $training->instrument_id,
            'link' => $request->link ?? $training->link,
            'date_training' => $start,
            'end_training' => $end,
            'duration' => $start->diffInMinutes($end),]);

        return response()->json($this->formatEvent($training->fresh('instrument')));
    }

    public function destroy($id)
    {
        $training = Training::findOrFail($id);

        if ($training->user_id !== Auth::id()) {
            abort(403);
        }

        $training->delete();

        return response()->json(['success' => true]);
    }

    public function listInstruments()
    {
        $instruments = GenericInstrument::all(['id', 'name']);
        return response()->json($instruments);
    }

    private function formatEvent($t)
{
    return [
        'id' => $t->id,
        'title' => $t->name,
        'instrument_id' => $t->instrument_id,
        'instrument' => $t->instrument?->name,
        'link' => $t->link,
        'start' => $t->date_training->toIso8601String(),
        'end' => $t->end_training->toIso8601String(),
    ];
}
}
