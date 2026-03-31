<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $table = 'designs';
    protected $fillable = ['room_id', 'style', 'imageGenere'];

    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function furniture() {
        return $this->belongsToMany(Furniture::class, 'design_furniture');
    }

    // Methods
    public function appliquerStyle($style) {
        $this->style = $style;
        $this->save();
    }

    public function genererDesign() {
        // Complex logic to generate a design based on room and preferences
        // For now, just a placeholder
        return $this;
    }
}