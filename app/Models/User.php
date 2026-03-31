<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // if you use Sanctum

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';
    protected $fillable = ['nom', 'email', 'motDePasse', 'preferences'];
    protected $hidden = ['motDePasse', 'remember_token'];
    protected $casts = [
        'preferences' => 'array',
    ];

    // Override to use 'motDePasse' instead of 'password' for authentication
    public function getAuthPassword()
    {
        return $this->motDePasse;
    }

    // Relations
    public function rooms() {
        return $this->hasMany(Room::class);
    }

    // Custom methods from diagram
    public function seConnecter($credentials) {
        // Use Laravel's attempt with custom field name
        if (auth()->attempt(['email' => $credentials['email'], 'password' => $credentials['motDePasse']])) {
            return auth()->user();
        }
        return null;
    }

    public function seDeconnecter() {
        auth()->logout();
    }

    public function metteAuJouProfil($data) {
        $this->update($data);
    }

    public function obtenirRecommandations() {
        // Implement your recommendation logic based on preferences
        // Example: return Furniture::whereIn('style', $this->preferences)->get();
        return collect(); // placeholder
    }
}