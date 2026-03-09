<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Room;
use App\Models\Photo;
use App\Models\Design;
use App\Policies\RoomPolicy;
use App\Policies\PhotoPolicy;
use App\Policies\DesignPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Room::class   => RoomPolicy::class,
        Photo::class  => PhotoPolicy::class,
        Design::class => DesignPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Vous pouvez ajouter ici des Gates personnalisés si nécessaire
    }
}