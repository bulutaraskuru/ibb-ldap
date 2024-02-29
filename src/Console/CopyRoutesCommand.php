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
        $packageRoutesPath = __DIR__ . '/../routes/web.php';
        $appRoutesPath = base_path('routes/web.php');

        if (file_exists($packageRoutesPath) && is_readable($packageRoutesPath)) {
            $packageRoutes = file_get_contents($packageRoutesPath);
            $this->appendToFile($appRoutesPath, $packageRoutes);
        } else {
            $this->error("The package routes file does not exist or is not readable at {$packageRoutesPath}");
        }
    }
}
