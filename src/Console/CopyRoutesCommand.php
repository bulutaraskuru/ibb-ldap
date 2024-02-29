<?php

namespace BulutKuru\IbbLdap\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class CopyRoutesCommand extends Command
{
    protected $name = 'ibbldap:copy-routes';
    protected $description = 'Copies the IbbLdap routes to the main web routes file';

    public function handle()
    {
        $packageRoutesPath = __DIR__ . '/../../routes/web.php'; // Paketinizin routes dosyasının yolu
        $appRoutesPath = base_path('routes/web.php'); // Uygulamanın routes dosyasının yolu

        if (file_exists($packageRoutesPath) && is_readable($packageRoutesPath)) {
            $packageRoutes = file_get_contents($packageRoutesPath);
            if (file_exists($appRoutesPath)) {
                file_put_contents($appRoutesPath, "\n" . $packageRoutes, FILE_APPEND);
                $this->info("Routes copied to {$appRoutesPath}");
            } else {
                $this->error("The application routes file does not exist at {$appRoutesPath}");
            }
        } else {
            $this->error("The package routes file does not exist or is not readable at {$packageRoutesPath}");
        }
    }
}
