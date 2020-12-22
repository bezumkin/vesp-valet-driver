<?php

class VespValetDriver extends BasicValetDriver
{
    /**
     * Determine if the driver serves the request.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return is_dir($sitePath . '/core') && is_dir($sitePath . '/www');
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $ext = pathinfo($uri, PATHINFO_EXTENSION);
        if (substr($uri, -1) === '/') {
            $uri .= 'index.html';
        } elseif (!$ext) {
            $uri .= '/index.html';
        }

        if (file_exists($sitePath . '/frontend/dist' . $uri)) {
            return $sitePath . '/frontend/dist' . $uri;
        }
        if (file_exists($sitePath . '/frontend/dist/site' . $uri)) {
            return $sitePath . '/frontend/dist/site' . $uri;
        }
        if (!$ext && !preg_match('#^/(api|image|file|sound|avatar|__clockwork)/#', $uri)) {
            return $sitePath . '/frontend/dist/site/200.html';
        }

        return false;
    }

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param string $sitePath
     * @param string $siteName
     * @param string $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        if ($uri === '/') {
            return $sitePath . '/frontend/dist/site/index.html';
        }

        return file_exists($sitePath . '/www/api.php')
            ? $sitePath . '/www/api.php'
            : $sitePath . '/www/index.php';
    }
}
