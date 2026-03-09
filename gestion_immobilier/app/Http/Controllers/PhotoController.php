<?php
namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function destroy(Photo $photo)
    {
        $this->authorize('delete', $photo);
        $photo->supprimer(); // your custom delete method
        return response()->json(null, 204);
    }

    public function analyze(Photo $photo)
    {
        $this->authorize('view', $photo);
        $result = $photo->analyserImage(); // implement logic
        return response()->json($result);
    }
}