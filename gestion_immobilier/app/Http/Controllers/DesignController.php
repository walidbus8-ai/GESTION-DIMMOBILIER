<?php
namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\Room;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function index(Request $request)
    {
        // Return designs for user's rooms
        return Design::whereHas('room', function($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->with('room', 'furniture')->get();
    }

    public function store(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        $data = $request->validate([
            'style' => 'required|string',
        ]);
        $design = $room->design()->create($data);
        // attach furniture if any
        if ($request->has('furniture_ids')) {
            $design->furniture()->sync($request->furniture_ids);
        }
        return response()->json($design, 201);
    }

    public function show(Design $design)
    {
        $this->authorize('view', $design->room);
        return $design->load('room', 'furniture');
    }

    public function update(Request $request, Design $design)
    {
        $this->authorize('update', $design->room);
        $data = $request->validate([
            'style' => 'sometimes|string',
            'imageGenere' => 'sometimes|string',
        ]);
        $design->update($data);
        if ($request->has('furniture_ids')) {
            $design->furniture()->sync($request->furniture_ids);
        }
        return response()->json($design);
    }

    public function destroy(Design $design)
    {
        $this->authorize('delete', $design->room);
        $design->delete();
        return response()->json(null, 204);
    }

    public function generate(Design $design)
    {
        $this->authorize('update', $design->room);
        $design->genererDesign(); // your logic
        return response()->json($design);
    }
}