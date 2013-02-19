<?php

// Test redirects
if ($_SERVER["SERVER_PORT"] === "8080") {
    http_response_code(301);
    header("Location: http://localhost:8000/RequestTest.php");
} elseif (array_key_exists("testAuth", $_GET) && $_GET["testAuth"] === "yes") {
    if (array_key_exists("PHP_AUTH_USER", $_SERVER) === false) {
        header('WWW-Authenticate: Basic realm="RequestTest"');
        http_response_code(401);
    } else {
        http_response_code(200);
    }
} else {
    if (array_key_exists("testTimeout", $_GET) && $_GET["testTimeout"] === "yes") {
        sleep(1);
    }
    http_response_code(200);
    setcookie("RequestTest", md5("RequestTest"), time() + 3600);
}