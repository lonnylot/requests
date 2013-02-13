<?php

namespace Requests;

class Response
{
    private $request;
    private $totalTime;

    public $headers;
    private $body;
    private $statusCode = 0;

    /**
     * @api
     *
     * @param $response The response array given from the Requesters adapter
     * @param $request The Request object that called this object
     */
    public function __construct($response, $request)
    {
        $this->request = $request;
        $this->body = $response["body"];
        $this->parseHeaders($response["headers"]);
        $this->totalTime = $response["totalTime"];
    }

    /**
     * Check if the response is OK
     *
     * @api
     *
     * @return bool
     */
    public function isOk()
    {
        if ($this->statusCode >= 200 && $this->statusCode < 400) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return null|String
     */
    public function __get($name)
    {
        $name = strtolower($name);
        if ($name === "body") {
            return $this->body;
        } elseif ($name === "totaltime") {
            return $this->totalTime;
        } elseif ($name === "statuscode") {
            return $this->statusCode;
        } elseif (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        } else {
            return null;
        }
    }

    private function parseHeaders($headers)
    {
        $responseHeaders = [];
        foreach ($headers AS $header) {
            $parts = explode(":", $header);
            if (count($parts) === 1) {
                $this->parseStatusCode($parts[0]);
            } else {
                $responseHeaders[trim(strtolower($parts[0]))] = trim($parts[1]);
            }
        }

        $this->headers = new Headers($responseHeaders);
    }

    private function parseStatusCode($statusCode) {
        preg_match("/([0-9]{3})/", $statusCode, $code);
        if(count($code)) {
            $this->statusCode = $code[0];
        }
    }
}