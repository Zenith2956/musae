<?php

namespace App\Http\Controllers;

use App\Models\GenericInstrument;
use Illuminate\Http\Request;
use App\Models\Sheet;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
 
  
class SheetController extends Controller
{
    public function index()
    {
        $get = Sheet::all();

        return Inertia::render('Sheets/Index', [
            'sheets' => $get
        ]);
    }

    public function detail(Sheet $sheet)
    {
        return Inertia::render('Sheets/Detail', [
            'sheet' => $sheet
        ]);
    }

    public function store(Request $request)
    {
        Sheet::create([
        'name' => $request->input('name'), 
        'link' => $request->input('link'), 
        'instrument_id' => $request->input('instrument_id'),
        'composer' => $request->input('composer') ?? null,
        'user_id' => Auth::id(),
        ]);
         return redirect('/library');
    }

    public function listInstruments()
    {
        $instruments = GenericInstrument::all(['id', 'name']);
        return response()->json($instruments);
    }
    
    

}
