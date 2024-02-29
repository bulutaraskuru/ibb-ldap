<?php

namespace BulutKuru\IbbLdap\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CopyRoutesCommand extends Command
{
    protected $name = 'ibbldap:install';
    protected $description = 'Copies the IbbLdap routes, controllers, and helpers to the Laravel application';

    public function handle()
    {
        $this->copyRoutes();
        $this->copyControllers();
        $this->copyHelpers();
        $this->copyMigrations();
    }

    protected function copyRoutes()
    {
        $packageRoutesPath = __DIR__ . '/../routes/web.php';
        $appRoutesPath = base_path('routes/web.php');

        if (file_exists($packageRoutesPath) && is_readable($packageRoutesPath)) {
            $packageRoutes = file_get_contents($packageRoutesPath);
            $useStatements = $this->extractUseStatements($packageRoutes);
            $formattedRoutes = str_replace('<?php', '', $packageRoutes);
            $this->appendToFile($appRoutesPath, "\n" . $useStatements . "\n" . $formattedRoutes);
            $this->info("Successfully added routes and use statements to {$appRoutesPath}");
        } else {
            $this->error("The package routes file does not exist or is not readable at {$packageRoutesPath}");
        }
    }

    protected function copyControllers()
    {
        $packageControllersPath = __DIR__ . '/../Controllers';
        $appControllersPath = app_path('Http/Controllers');

        $fileSystem = new Filesystem();
        if ($fileSystem->isDirectory($packageControllersPath)) {
            $fileSystem->copyDirectory($packageControllersPath, $appControllersPath);
            $this->info("Successfully copied controllers to {$appControllersPath}");
        } else {
            $this->error("The package controllers directory does not exist or is not readable at {$packageControllersPath}");
        }
    }

    protected function copyHelpers()
    {
        $packageHelpersPath = __DIR__ . '/../Helpers';
        $appHelpersPath = app_path('Helpers');

        $fileSystem = new Filesystem();
        if ($fileSystem->isDirectory($packageHelpersPath)) {
            $fileSystem->copyDirectory($packageHelpersPath, $appHelpersPath);
            $this->info("Successfully copied helpers to {$appHelpersPath}");
        } else {
            $this->error("The package helpers directory does not exist or is not readable at {$packageHelpersPath}");
        }
    }

    protected function copyMigrations()
    {
        $packageMigrationsPath = __DIR__ . '/../database/migrations';
        $appMigrationsPath = database_path('migrations');

        $fileSystem = new Filesystem();
        if ($fileSystem->isDirectory($packageMigrationsPath)) {
            $fileSystem->copyDirectory($packageMigrationsPath, $appMigrationsPath);
            $this->info("Successfully copied migrations to {$appMigrationsPath}");
        } else {
            $this->error("The package migrations directory does not exist or is not readable at {$packageMigrationsPath}");
        }
    }

    protected function appendToFile($filePath, $content)
    {
        if (file_exists($filePath) && is_writable($filePath)) {
            $existingContent = file_get_contents($filePath);
            $existingContent = preg_replace('/^<\?php\s*/', '', $existingContent);
            $newContent = "<?php\n\n" . $existingContent . "\n" . $content;
            file_put_contents($filePath, $newContent);
            $this->info("Successfully added content to {$filePath}");
        } else {
            $this->error("The file {$filePath} is not writable.");
        }
    }
    protected function extractUseStatements($content)
    {
        preg_match_all('/^use\s+[a-zA-Z0-9\\\\_]+;/m', $content, $matches);
        return implode("\n", array_unique($matches[0]));
    }
}
