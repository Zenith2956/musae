<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sheet;
use Inertia\Inertia;
 
  
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
        'name' => $request['name'], 
        'link' => $request['link'], 
        'instrument_id' => $request['instrument'],
        'users_id' => auth()->user()->id,
        ]);
         return redirect('/library');
    }

    
    

}
