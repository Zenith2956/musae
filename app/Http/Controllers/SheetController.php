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
        $sheets = Sheet::with('instrument')->get();
        return Inertia::render('Sheets/Index', [
            'sheets' => $sheets
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
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|string',
            'instrument_id' => 'nullable|integer',
            'composer' => 'nullable|string|max:255',
            'bpm' => 'nullable|integer|between:50,200',
            'gamme' => 'nullable|string|max:255',
            'proficiency_level' => 'nullable|integer|min:1|max:5',
            'style' => 'nullable|string|max:255',
        ]);

        $data['user_id'] = Auth::id();
        Sheet::create($data);
        return redirect('/library');
    }

    public function update(Request $request, Sheet $sheet)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'link' => 'sometimes|required|string',
            'instrument_id' => 'nullable|integer',
            'composer' => 'nullable|string|max:255',
            'bpm' => 'nullable|integer|between:50,200',
            'gamme' => 'nullable|string|max:255',
            'proficiency_level' => 'nullable|integer|min:1|max:5',
            'style' => 'nullable|string|max:255',
        ]);

        $sheet->update($data);

        return redirect()->route('sheet.detail', $sheet);
    }

    public function listInstruments()
    {
        $instruments = GenericInstrument::all(['id', 'name']);
        return response()->json($instruments);
    }
}
