<?php
namespace App\Http\Controllers;

use App\Models\Furniture;
use Illuminate\Http\Request;

class FurnitureController extends Controller
{
    public function index()
    {
        return Furniture::all();
    }

    public function show(Furniture $furniture)
    {
        return $furniture;
    }

    public function compare(Request $request, Furniture $furniture)
    {
        $otherId = $request->input('other_id');
        $other = Furniture::findOrFail($otherId);
        $comparison = $furniture->comparerPrix($other);
        return response()->json([
            'furniture1' => $furniture,
            'furniture2' => $other,
            'comparison' => $comparison // -1, 0, 1
        ]);
    }
}