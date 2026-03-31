<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['user_id', 'largeur', 'hauteur', 'type'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function photos() {
        return $this->hasMany(Photo::class);
    }

    public function design() {
        return $this->hasOne(Design::class); // assuming one design per room
    }

    // Methods
    public function ajouterPhoto($photoData) {
        return $this->photos()->create($photoData);
    }

    public function supprimerPhoto($photoId) {
        return $this->photos()->where('id', $photoId)->delete();
    }

    public function calculerSurface() {
        return $this->largeur * $this->hauteur;
    }
}