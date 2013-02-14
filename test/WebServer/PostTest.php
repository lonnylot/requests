<?php

if (array_key_exists("sentPostTest", $_POST) && $_POST["sentPostTest"] === "yes") {
    http_response_code(200);
    header("Content-Type: text/html; charset=UTF-8");
} else {
    http_response_code(400);
    header("Content-Type: text/html; charset=UTF-8");
}