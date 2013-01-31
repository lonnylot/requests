<?php

namespace Requests\Requesters;

class Curl implements RequestersInterface
{
    private $params;
    private $method;

    /**
     * Setup default parameters on initialization
     */
    public function __construct()
    {
        // Set some default parameters
        $this->params[CURLOPT_HEADER] = true;
        $this->params[CURLOPT_RETURNTRANSFER] = true;
        $this->params[CURLOPT_USERAGENT] = "Requests/0.0.1-dev";
    }
    /**
     * This is a visitor to check if we are able to use this implementation to make requests
     * @param $method The method we are going to be using
     * @param $scheme The scheme we are going to be using
     * @return bool
     */
    public static function isAvailable($method, $scheme)
    {
        return function_exists('curl_init') ? true : false;
    }

    /**
     * @param $method The method we are going to be using
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Set the parameters to be used in the request
     * @param $preparedParams The preparedParams from the Request object
     */
    public function setParams($preparedParams)
    {
        $this->params = [
            CURLOPT_URL => $preparedParams["url"],
            CURLOPT_HTTPHEADER => $preparedParams["headers"],
            CURLOPT_COOKIE => explode(";", array_walk(
                                    $preparedParams["cookies"],
                                    function($name, $value){ return "{$name}={$value}";}
                                )),
            CURLOPT_TIMEOUT => $preparedParams["timeout"],
            CURLOPT_FOLLOWLOCATION => $preparedParams["allowRedirects"]
        ] + $this->params;

        if (array_key_exists("user", $preparedParams["auth"]) && array_key_exists("pass", $preparedParams["auth"])) {
            $this->params[CURLOPT_HTTPAUTH] = "CURLAUTH_BASIC";
            $this->params[CURLOPT_USERPWD] = "{$preparedParams["auth"]}:{$preparedParams["pass"]}";
        }

        if ($this->method > 0) {
            $this->params[CURLOPT_POSTFIELDS] = $preparedParams["data"];
        }
    }

    /**
     * Make the request using our params
     * @return array
     */
    public function request()
    {
        $ch = curl_init();
        curl_setopt_array($ch, $this->params);
        $response = curl_exec($ch);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);

        list($headers, $body) = explode("\r\n\r\n", $response, 2);
        $headers = explode("\r\n", $headers);
        return [
            "body" => $body,
            "headers" => $headers,
            "totalTime" => $totalTime
        ];
    }
}