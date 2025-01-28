<?php

namespace Agenciafmd\Categories\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //        '\Agenciafmd\{{ Pacotes }}\{{ Tipo }}' => '\Agenciafmd\{{ Pacotes }}\Policies\{{ Tipo }}Policy',
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
