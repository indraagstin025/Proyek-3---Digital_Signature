<?php

namespace App\Providers;

use App\Helpers\PasetoHelper;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * @var array<class-string, class-string>
     */
    protected $policies = [];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();


        Auth::viaRequest('paseto', function (Request $request) {
            $token = $request->bearerToken();
            if (!$token) {
                return null;
            }

            if (Cache::has('token_blacklist:' . $token)) {
                return null;
            }

            try {
                $parsedToken = PasetoHelper::parseToken($token);
                $claims = $parsedToken->getClaims();
                return User::find($claims['sub']);
            } catch (\Throwable $th) {
                return null;
            }
        });
    }
}
