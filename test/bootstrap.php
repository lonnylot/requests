<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Start our built-in web server
$webServer = proc_open("/usr/local/bin/php -S localhost:8000", [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]], $pipes, __DIR__ . "/WebServer/");

if (isProcRunning($webServer) === false) {
    throw new \Exception("Failed to start test web server!");
}

// Start our built-in web server for redirects
$redirectWebServer = proc_open("/usr/local/bin/php -S localhost:8080", [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]], $redirectPipes, __DIR__ . "/WebServer/");

if (isProcRunning($redirectWebServer) === false) {
    throw new \Exception("Failed to start redirect test web server!");
}

// Wait a moment so the servers setup.
sleep(1);

/**
 * @param $proc Resource of a running process
 * @return bool
 */
function isProcRunning($proc)
{
    if (is_resource($proc)) {
        $status = proc_get_status($proc);
        return $status["running"];
    } else {
        return false;
    }
}

// Register our shutdown for our web server
register_shutdown_function(function($webServer, $pipes, $redirectWebServer, $redirectPipes){
    echo "Closing web server pipes.\n";
    foreach ($pipes AS $pipe) {
        fclose($pipe);
    }

    echo "Terminating test web server.\n";
    if (isProcRunning($webServer)) {
        proc_terminate($webServer, 2);
    }

    echo "Closing redirect web server pipes.\n";
    foreach ($redirectPipes AS $pipe) {
        fclose($pipe);
    }

    echo "Terminating redirect test web server.\n";
    if (isProcRunning($redirectWebServer)) {
        proc_terminate($redirectWebServer, 2);
    }
}, $webServer, $pipes, $redirectWebServer, $redirectPipes);