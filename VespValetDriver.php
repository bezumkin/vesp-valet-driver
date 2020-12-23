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
        if (preg_match('#^/(api|__clockwork)/#', $uri) || preg_match('#^/(image|file|sound|avatar)/\d+#', $uri)) {
            return false;
        }

        if (substr($uri, -1) === '/') {
            $uri .= 'index.html';
        } elseif (!pathinfo($uri, PATHINFO_EXTENSION)) {
            $uri .= '/index.html';
        }

        $files = [
            $uri,
            preg_match('#^/admin/#', $uri) ? '/admin/200.html' : '/site' . $uri,
            '/site/200.html',
            '/200.html',
        ];
        foreach ($files as $file) {
            $file = $sitePath . '/frontend/dist' . $file;
            if (file_exists($file)) {
                return $file;
            }
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
            foreach (['site/index.html', 'site/200.html', 'index.html', '200.html'] as $page) {
                $page = $sitePath . '/frontend/dist/' . $page;
                if (file_exists($page)) {
                    return $page;
                }
            }
        }

        return file_exists($sitePath . '/www/api.php')
            ? $sitePath . '/www/api.php'
            : $sitePath . '/www/index.php';
    }
}
