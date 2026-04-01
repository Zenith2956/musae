<?php

namespace App\Http\Controllers\Settings;

use App\Models\Training;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class CalendarController extends Controller
{
    public function events(Request $request)
    {
        try {
            if (!$request->start || !$request->end) {
                return response()->json(['error' => 'Dates manquantes'], 400);
            }

            $start = Carbon::parse($request->start);
            $end = Carbon::parse($request->end);

            $trainings = Training::whereBetween('date_training', [$start, $end])->get();

            return response()->json(
                $trainings->map(function ($training) {
                    $start = Carbon::parse($training->date_training);

                    return [
                        'id' => $training->id,
                        'title' => $training->name,
                        'start' => $start->toIso8601String(),
                        'end' => $start->copy()
                            ->addMinutes($training->duration ?? 0)
                            ->toIso8601String(),
                    ];
                })
            );
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $training = Training::findOrFail($id);

        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        Log::debug("update new Training {training}", ['training' => $training]);

        $training->update([
            // 'start' => $request->start ? Carbon::parse($request->start) : $training->date_training,
            // 'end' => $request->end ? Carbon::parse($request->end) : $training->date_training->copy()->addMinutes($training->duration),
            'name' => $request->title ?? $training->name,
            'instrument' => $request->instrument ?? $training->instrument,
            'link' => $request->link ?? $training->link,
            'date_training' => $start,
            'duration' => $start->diffInMinutes($end)
        ]);

        return response()->json(['success' => true]);
    }
    public function store(Request $request)
    {
        try {
            $start = \Carbon\Carbon::parse($request->start);
            $end = \Carbon\Carbon::parse($request->end);

            $training = Training::create([
                'name' => $request->title,
                'date_training' => $start,
                'duration' => $start->diffInMinutes($end),
                'user_id' => 1
            ]);
            Log::debug("Created new Training {training}", ['training' => $training]);
            return response()->json([
                'id' => $training->id,
                'title' => $training->name,
                'start' => $training->date_training->toIso8601String(),
                'end' => $training->date_training
                    ->copy()
                    ->addMinutes($training->duration)
                    ->toIso8601String(),
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
