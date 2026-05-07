<?php

namespace App\Http\Controllers;

use App\Models\historique;
use App\Models\Training;
use App\Models\Sheet;
use App\Models\GenericInstrument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoriqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    $userId = Auth::id();

        $trainings = Training::where('user_id', $userId)
            ->with('sheet')
            ->orderBy('date_training', 'desc')
            ->get();

        $instruments = GenericInstrument::all(['id', 'name'])->values();
        $sheets = Sheet::all(['id', 'name'])->values();

        return Inertia::render('Historique', [
            'trainings' => $trainings,
            'instruments' => $instruments,
            'sheets' => $sheets
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(historique $historique)
    {
        //
    }
}
