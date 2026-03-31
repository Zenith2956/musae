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
            Log::info('Start Date: ' . $request->start);
            Log::info('End Date: ' . $request->end);

            $start = Carbon::parse($request->start)->startOfDay();
            $end = Carbon::parse($request->end)->endOfDay();

            $trainings = Training::whereBetween('date_training', [$start, $end])->get();

            if ($trainings->isEmpty()) {
                return response()->json([]);
            }

            $events = $trainings->map(function ($training) {
                return [
                    'id' => $training->id,
                    'title' => $training->name,
                    'start' => $training->date_training->toIso8601String(),
                    'end' => Carbon::parse($training->date_training)->addMinutes($training->duration)->toIso8601String(),
                ];
            });

            return response()->json($events);
        } catch (\Exception $e) {
            Log::error('Error fetching calendar events: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue : ' . $e->getMessage()], 500);
        }
    }
}