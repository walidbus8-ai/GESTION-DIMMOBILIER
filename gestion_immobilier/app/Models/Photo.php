<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $fillable = ['room_id', 'cheminImage'];

    public function room() {
        return $this->belongsTo(Room::class);
    }

    // Methods
    public function telecharger() {
        // Logic to download the image (maybe return a response)
    }

    public function supprimer() {
        // Delete file from storage and database
        $this->delete();
    }

    public function analyserImage() {
        // AI or image processing logic
    }
}