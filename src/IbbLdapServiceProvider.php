<?php

namespace BulutKuru\IbbLdap;

use Illuminate\Support\ServiceProvider;

class IbbLdapServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Burada paketinizin kaynaklarını yükleyebilirsiniz.
        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        // Burada paketinizin bağımlılıklarını kaydedebilirsiniz.
    }
}
