<?php

namespace Requests\Requesters;

if (defined("\Requests\Version") === false) {
    require_once(realpath(__DIR__ . "/../Api.php"));
}

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
        $this->params[CURLOPT_USERAGENT] = "Requests/" . \Requests\Version;
    }

    /**
     * This is a visitor to check if we are able to use this implementation to make requests
     * @param $method String The method we are going to be using
     * @param $scheme String The scheme we are going to be using
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
     * @param $method String The method we are going to be using
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * Set the parameters to be used in the request
     * @param $preparedParams Array The preparedParams from the Request object
     */
    public function setParams($preparedParams)
    {
        $this->params = [
            CURLOPT_URL => $preparedParams["url"],
            CURLOPT_HTTPHEADER => $preparedParams["headers"],
            CURLOPT_TIMEOUT => $preparedParams["timeout"],
            CURLOPT_FOLLOWLOCATION => $preparedParams["allowRedirects"]
        ] + $this->params;

        // Handle cookies
        if (is_string($preparedParams["cookies"]) && is_file($preparedParams["cookies"])) {
            $this->params[CURLOPT_COOKIEFILE] = $preparedParams["cookies"];
            $this->params[CURLOPT_COOKIEJAR] = $preparedParams["cookies"];
        } elseif (is_array($preparedParams["cookies"]) && count($preparedParams["cookies"])) {
            $this->params[CURLOPT_COOKIE] = implode(";", $preparedParams["cookies"]);
        }

        // Do POST parameters
        if ($this->method === "post") {
            $this->params[CURLOPT_POST] = true;
            if (is_array($preparedParams["data"])) {
                $this->params[CURLOPT_POSTFIELDS] = http_build_query($preparedParams["data"]);
            } else {
                $this->params[CURLOPT_POSTFIELDS] = $preparedParams["data"];
            }
        } elseif ($this->method === "head") {
            $this->params[CURLOPT_NOBODY] = true;
        }

        // Handle HTTP AUTH
        if (array_key_exists("user", $preparedParams["auth"]) && array_key_exists("pass", $preparedParams["auth"])) {
            $this->params[CURLOPT_HTTPAUTH] = CURLAUTH_ANY;
            $this->params[CURLOPT_USERPWD] = "{$preparedParams["auth"]["user"]}:{$preparedParams["auth"]["pass"]}";
        }

        // Handle Proxy
        if (array_key_exists("proxy", $preparedParams)) {
            $this->params[CURLOPT_HTTPPROXYTUNNEL] = true;
            $this->params[CURLOPT_PROXY] = $preparedParams["proxy"];
        }

        // Handle verify
        if ($preparedParams["verify"]) {
            $this->params[CURLOPT_SSL_VERIFYPEER] = true;
            $this->params[CURLOPT_SSL_VERIFYHOST] = 2;
        } else {
            $this->params[CURLOPT_SSL_VERIFYPEER] = false;
            $this->params[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if (count($preparedParams["cert"])) {
            if (is_string($preparedParams["cert"])) {
                $this->params[CURLOPT_SSLCERT] = $preparedParams["cert"];
            } elseif (is_array($preparedParams["cert"])) {
                $this->params[CURLOPT_SSLCERT] = array_pop($preparedParams["cert"]);
                $this->params[CURLOPT_SSLKEY] = array_pop($preparedParams["cert"]);
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
        $responseBody = curl_exec($ch);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        // Get the request headers that were sent
        $this->headers = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        curl_close($ch);

        do {
            // Split the header and body - overwrite the responseBody w/ the new body
            list($headers, $responseBody) = explode("\r\n\r\n", $responseBody, 2);
            // Continue doing this while we have response headers - this is for any connections that require multiple requests
        } while(preg_match("/^HTTP\/\d\.\d \d{3} [a-zA-Z ]+\\r\\n/", $responseBody));

        $headers = explode("\r\n", $headers);
        return [
            "body" => $responseBody,
            "headers" => $headers,
            "totalTime" => $totalTime
        ];
    }
}