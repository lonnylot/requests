<?php

// Test redirects
if ($_SERVER["SERVER_PORT"] === "8080") {
    http_response_code(301);
    header("Location: localhost:8000");
} else {
    if (array_key_exists("testTimeout", $_GET) && $_GET["testTimeout"] === "yes") {
        sleep(1);
    }
    http_response_code(200);
    setcookie("RequestTest", md5("RequestTest"), time() + 3600);
}