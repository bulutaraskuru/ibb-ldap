<?php

namespace BulutKuru\IbbLdap\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;

class CopyRoutesCommand extends Command
{
    protected $name = 'ibbldap:copy-routes';
    protected $description = 'Copies the IbbLdap routes and controllers to the Laravel application';

    public function handle()
    {
        $packageRoutesPath = __DIR__ . '/../routes/web.php';
        $appRoutesPath = base_path('routes/web.php');
        $packageControllersPath = __DIR__ . '/../Controllers';
        $appControllersPath = app_path('Http/Controllers');

        // Copy routes
        if (file_exists($packageRoutesPath) && is_readable($packageRoutesPath)) {
            $packageRoutes = file_get_contents($packageRoutesPath);
            $useStatements = $this->extractUseStatements($packageRoutes);
            // Remove "<?php" tag from routes if it exists
            $formattedRoutes = str_replace('<?php', '', $packageRoutes);
            $this->appendToFile($appRoutesPath, "\n" . $useStatements . "\n" . $formattedRoutes);
            $this->info("Successfully added routes and use statements to {$appRoutesPath}");
        } else {
            $this->error("The package routes file does not exist or is not readable at {$packageRoutesPath}");
        }

        // Copy controllers
        $fileSystem = new Filesystem();
        if ($fileSystem->isDirectory($packageControllersPath)) {
            $fileSystem->copyDirectory($packageControllersPath, $appControllersPath);
            $this->info("Successfully copied controllers to {$appControllersPath}");
        } else {
            $this->error("The package controllers directory does not exist or is not readable at {$packageControllersPath}");
        }
    }

    protected function appendToFile($filePath, $content)
    {
        if (file_exists($filePath) && is_writable($filePath)) {
            $existingContent = file_get_contents($filePath);
            // Check if existing content starts with <?php and remove it
            $existingContent = preg_replace('/^<\?php\s*/', '', $existingContent);
            // Ensure that we still have <?php at the start after removing duplicates
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
