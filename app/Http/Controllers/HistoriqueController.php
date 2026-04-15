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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(historique $historique)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(historique $historique)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, historique $historique)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(historique $historique)
    {
        //
    }
}
