<?php

namespace Requests;

if (defined("\Requests\Version") === false) {
    require_once(realpath(__DIR__ . "/Api.php"));
}

class Session
{
    const GET = 0;
    const POST = 1;
    const HEAD = 2;

    private $parsedUrl;
    private $method;
    private $namedParams;
    private $preparedParams;
    private $availableRequesters;

    private $requester;
    public $headers;

    /**
     * Sets up default settings
     *
     * @api
     */
    public function __construct()
    {
        $this->namedParams = [
            "params"=>[],
            "data"=>[],
            "headers"=>[],
            "cookies"=>[],
            "auth"=>[],
            "timeout"=>30,
            "allowRedirects"=>false,
            "proxies"=>[],
            "verify"=>true,
            "cert"=>[]];

        $this->availableRequesters = ["Requests\Requesters\Curl"];
    }

    /**
     * @param $name
     * @return null|String
     */
    public function __get($name)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        } else {
            return null;
        }
    }

    /**
     * Run the request
     *
     * @api
     *
     * @param $method Request method defined in constants
     * @param $url String URL we are requesting
     * @param $namedParams Array w/ keys as string for the request
     *
     * @return \Requests\Response
     *
     */
    public function request($method, $url, $namedParams)
    {
        $this->method = $method;
        $this->namedParams = array_merge($this->namedParams, $namedParams);
        $this->parseUrl($url);
        $this->prepareParams();
        return $this->send();
    }

    private function parseUrl($url)
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl["scheme"])) {
            throw new \Exception("URL is missing the scheme.");
        }
        if (!isset($parsedUrl["host"])) {
            throw new \Exception("URL is missing the host.");
        }

        $this->parsedUrl = $parsedUrl;
    }

    private function prepareParams()
    {
        // Build the URL
        $this->preparedParams["url"] = $this->parsedUrl["scheme"] . "://" . $this->parsedUrl["host"] . ":" . $this->parsedUrl["port"];

        if ($this->parsedUrl["path"]) {
            $this->preparedParams["url"] .= $this->parsedUrl["path"];
        }

        if ($this->parsedUrl["query"]) {
            parse_str($this->parsedUrl["query"], $params);
            $this->namedParams["params"] = array_merge($params, $this->namedParams["params"]);
        }
        if (count($this->namedParams["params"])) {
            $this->preparedParams["url"] .= "?" . http_build_query($this->namedParams['params']);
        }

        if ($this->parsedUrl["fragment"]) {
            $this->preparedParams["url"] .= "#{$this->parsedUrl["fragment"]}";
        }

        // Build the auth params
        if ($this->namedParams["auth"]) {
            $this->preparedParams["auth"] = $this->namedParams["auth"];
        } elseif ($this->parsedUrl["user"] && $this->parsedUrl["pass"]) {
            $this->preparedParams["auth"] = ["user" => $this->parsedUrl["user"], "pass" => $this->parsedUrl["pass"]];
        } else {
            $this->preparedParams["auth"] = [];
        }

        // Build the proxy
        if (array_key_exists($this->parsedUrl["scheme"], $this->namedParams["proxies"])) {
            $this->preparedParams["proxy"] = $this->namedParams["proxies"][$this->parsedUrl["scheme"]];
        }

        // Verify SSL
        // TODO: Need to handle a string path (CA_BUNDLE?)
        $this->preparedParams["verify"] = (bool)$this->namedParams["verify"];

        // Build everything else
        $this->preparedParams["data"] = $this->namedParams["data"];
        $this->preparedParams["headers"] = $this->namedParams["headers"];
        $this->preparedParams["cookies"] = $this->namedParams["cookies"];
        $this->preparedParams["timeout"] = $this->namedParams["timeout"];
        $this->preparedParams["allowRedirects"] = $this->namedParams["allowRedirects"];
        $this->preparedParams["cert"] = $this->namedParams["cert"];
    }

    private function send()
    {
        $this->setRequester();
        $this->requester->setMethod($this->method);
        $this->requester->setParams($this->preparedParams);
        $response = $this->requester->request();
        $this->parseHeaders(explode("\r\n", $this->requester->getRequestHeaders()));
        return new Response($response, $this);
    }

    private function setRequester()
    {
        foreach ($this->availableRequesters AS $requester) {
            if ($requester::isAvailable($this->method, $this->parsedUrl['scheme'])) {
                $this->requester = new $requester();
            }
        }

        if (isset($this->requester) === false) {
            throw new \Exception("No requester is available for {$this->method} and {$this->parsedUrl['scheme']}");
        }
    }

    private function parseHeaders($headers)
    {
        $requestHeaders = [];
        foreach ($headers AS $header) {
            if (strpos($header, ":") === false) {
                $requestHeaders["method"] = trim($header);
            } else {
                $parts = explode(":", $header);
                $requestHeaders[trim(strtolower($parts[0]))] = trim($parts[1]);
            }
        }

        $this->headers = new \Requests\Headers($requestHeaders);
    }
}