<?php
namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->rooms()->with('photos', 'design')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'largeur' => 'required|numeric',
            'hauteur' => 'required|numeric',
            'type' => 'required|string',
        ]);

        $room = $request->user()->rooms()->create($data);
        return response()->json($room, 201);
    }

    public function show(Room $room)
    {
        $this->authorize('view', $room); // ensure user owns it
        return $room->load('photos', 'design');
    }

    public function update(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        $data = $request->validate([
            'largeur' => 'sometimes|numeric',
            'hauteur' => 'sometimes|numeric',
            'type' => 'sometimes|string',
        ]);
        $room->update($data);
        return response()->json($room);
    }

    public function destroy(Room $room)
    {
        $this->authorize('delete', $room);
        $room->delete();
        return response()->json(null, 204);
    }

    public function surface(Room $room)
    {
        $this->authorize('view', $room);
        return response()->json(['surface' => $room->calculerSurface()]);
    }

    public function addPhoto(Request $request, Room $room)
    {
        $this->authorize('update', $room);
        $data = $request->validate([
            'cheminImage' => 'required|string',
        ]);
        $photo = $room->ajouterPhoto($data);
        return response()->json($photo, 201);
    }
}