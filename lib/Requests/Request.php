<?php

namespace Requests;

class Request
{
    const GET = 0;

    private $parsedUrl;
    private $method;
    private $namedParams;
    private $preparedParams;
    private $availableRequesters;

    private $requester;

    public function __construct()
    {
        $this->namedParams = [
            "params"=>[],
            "data"=>[],
            "headers"=>[],
            "cookies"=>[],
            "auth"=>[],
            "timeout"=>30,
            "allowRedirects"=>false];

        $this->availableRequesters = ["Requests\Requesters\Curl"];
    }

    /**
     * @param $method Request method defined in constants
     * @param $url URL we are requesting
     * @param $namedParams Array w/ keys as string for the request
     * Possible values for $namedParams:
     * params: (optional) Array of key=>val to be sent in the query string
     * data: (optional) Array or String to be sent in the body
     * headers: (optional) Array of HTTP Headers
     * cookies: (optional) Array of cookies
     * auth: (optional) Array of ["user"=>"","pass"=>""] for Basic HTTP Auth
     * timeout: (optional) Float describing the timeout of the request.
     * allowRedirects: (optional) Boolean. Set to True if redirect following is allowed.
     * NOT IMPLEMENTED:
     * files: (optional) Dictionary of 'name': file-like-objects (or {'name': ('filename', fileobj)}) for multipart encoding upload.
     * proxies: (optional) Dictionary mapping protocol to the URL of the proxy.
     * verify: (optional) if ``True``, the SSL cert will be verified. A CA_BUNDLE path can also be provided.
     * stream: (optional) if ``False``, the response content will be immediately downloaded.
     * cert: (optional) if String, path to ssl client cert file (.pem). If Tuple, ('cert', 'key') pair.
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
            throw new Exception("URL is missing the scheme.");
        }
        if (!isset($parsedUrl["host"])) {
            throw new Exception("URL is missing the host.");
        }

        $this->parsedUrl = $parsedUrl;
    }

    private function prepareParams()
    {
        // Build the URL
        $this->preparedParams["url"] = $this->parsedUrl["scheme"] . "://" . $this->parsedUrl["host"];

        if ($this->parsedUrl["path"]) {
            $this->preparedParams["url"] .= $this->parsedUrl["path"];
        }

        if ($this->parsedUrl["query"]) {
            parse_str($this->parsedUrl["query"], $params);
            $this->namedParams["params"] = array_merge($params, $this->namedParams["params"]);
        }
        $this->preparedParams["url"] .= http_build_query($this->namedParams['params']);

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

        // Build everything else
        $this->preparedParams["data"] = $this->namedParams["data"];
        $this->preparedParams["headers"] = $this->namedParams["headers"];
        $this->preparedParams["cookies"] = $this->namedParams["cookies"];
        $this->preparedParams["timeout"] = $this->namedParams["timeout"];
        $this->preparedParams["allowRedirects"] = $this->namedParams["allowRedirects"];
    }

    private function send()
    {
        $this->setRequester();
        $this->requester->setMethod($this->method);
        $this->requester->setParams($this->preparedParams);
        $response = $this->requester->request();
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
}