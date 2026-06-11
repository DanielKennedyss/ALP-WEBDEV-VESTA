<?php

use Valet\Drivers\ValetDriver;

class LocalValetDriver extends ValetDriver
{
    /**
     * Determine if the driver serves the request.
     */
    public function serves(string $sitePath, string $siteName, string $uri): bool
    {
        return true;
    }

    /**
     * Determine if the incoming request is for a static file.
     * If the file is a missing storage asset, we return false so that Valet
     * passes the request to Laravel's index.php, letting our fallback redirect run.
     */
    public function isStaticFile(string $sitePath, string $siteName, string $uri)
    {
        $staticFilePath = $sitePath . '/public' . $uri;

        // If the file is inside the /storage/ directory and does not exist locally
        if (str_starts_with($uri, '/storage/')) {
            if (!file_exists($staticFilePath) || is_dir($staticFilePath)) {
                // Return false to pass this request to the front controller (Laravel index.php)
                return false;
            }
        }

        // Standard Valet behavior for other static files
        if (file_exists($staticFilePath) && !is_dir($staticFilePath)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     */
    public function frontControllerPath(string $sitePath, string $siteName, string $uri): string
    {
        return $sitePath . '/public/index.php';
    }
}
