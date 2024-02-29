<?php

namespace BulutKuru\IbbLdap\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
            $this->appendToFile($appRoutesPath, $useStatements . "\n" . $packageRoutes);
            $this->info("Successfully added routes and use statements to {$appRoutesPath}");
        } else {
            $this->error("The package routes file does not exist or is not readable at {$packageRoutesPath}");
        }

        // Copy controllers
        if (is_dir($packageControllersPath) && is_readable($packageControllersPath)) {
            foreach (new \DirectoryIterator($packageControllersPath) as $fileInfo) {
                if ($fileInfo->isDot() || !$fileInfo->isFile()) {
                    continue;
                }
                $sourcePath = $fileInfo->getPathname();
                $destinationPath = $appControllersPath . '/' . $fileInfo->getFilename();
                copy($sourcePath, $destinationPath);
                $this->info("Successfully copied {$fileInfo->getFilename()} to {$appControllersPath}");
            }
        } else {
            $this->error("The package controllers directory does not exist or is not readable at {$packageControllersPath}");
        }
    }

    protected function appendToFile($filePath, $content)
    {
        if (file_exists($filePath) && is_writable($filePath)) {
            // Dosyadan mevcut içeriği okuyun.
            $existingContent = file_get_contents($filePath);

            // Eğer içerik zaten "<?php" ile başlıyorsa, bunu kaldırın.
            $contentToAdd = preg_replace('/^<\?php\s*/', '', $content);

            // Eğer mevcut içerik "<?php" ile başlamıyorsa, "<?php" ekleyin.
            if (!Str::startsWith($existingContent, '<?php')) {
                $existingContent = "<?php\n\n" . $existingContent;
            }

            // Yeni içeriği mevcut içeriğin sonuna ekleyin.
            $newContent = $existingContent . "\n" . $contentToAdd;

            // Yeni içeriği dosyaya yazın.
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
