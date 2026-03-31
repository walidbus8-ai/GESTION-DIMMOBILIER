<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Furniture extends Model
{
    protected $table = 'furniture';
    protected $fillable = ['nom', 'type', 'couleur', 'prix', 'imageURL'];

    public function designs() {
        return $this->belongsToMany(Design::class, 'design_furniture');
    }

    // Methods
    public function afficherInfos() {
        return $this->toArray();
    }

    public function comparerPrix(Furniture $other) {
        return $this->prix <=> $other->prix;
    }
}