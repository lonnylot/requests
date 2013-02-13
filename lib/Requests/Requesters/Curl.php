<?php

namespace Requests\Requesters;

class Curl implements RequestersInterface
{
    private $params;
    private $method;
    private $headers;

    /**
     * Setup default parameters on initialization
     */
    public function __construct()
    {
        // Set some default parameters
        $this->params[CURLOPT_HEADER] = true;
        $this->params[CURLINFO_HEADER_OUT] = true;
        $this->params[CURLOPT_RETURNTRANSFER] = true;
        $this->params[CURLOPT_USERAGENT] = "Requests/0.1.1-dev";
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
     * Get the headers used in the request
     * @return String
     */
    public function getRequestHeaders()
    {
        return $this->headers;
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
            CURLOPT_TIMEOUT => $preparedParams["timeout"],
            CURLOPT_FOLLOWLOCATION => $preparedParams["allowRedirects"]
        ] + $this->params;

        if (array_key_exists("user", $preparedParams["auth"]) && array_key_exists("pass", $preparedParams["auth"])) {
            $this->params[CURLOPT_HTTPAUTH] = "CURLAUTH_BASIC";
            $this->params[CURLOPT_USERPWD] = "{$preparedParams["auth"]}:{$preparedParams["pass"]}";
        }

        // Handle cookies
        if (is_string($preparedParams["cookies"]) && is_file($preparedParams["cookies"])) {
            $this->params[CURLOPT_COOKIEFILE] = $preparedParams["cookies"];
            $this->params[CURLOPT_COOKIEJAR] = $preparedParams["cookies"];
        } elseif (is_array($preparedParams["cookies"]) && count($preparedParams["cookies"])) {
            $this->params[CURLOPT_COOKIE] = implode(";", $preparedParams["cookies"]);
        }

        // Do POST parameters
        if ($this->method === 1) {
            $this->params[CURLOPT_POST] = true;
            if (is_array($preparedParams["data"])) {
                $this->params[CURLOPT_POSTFIELDS] = http_build_query($preparedParams["data"]);
            } else {
                $this->params[CURLOPT_POSTFIELDS] = $preparedParams["data"];
            }
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
        $this->headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
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