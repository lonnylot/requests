<?php

namespace Requests;

class Get
{
    private $request;

    public function &request($url, $namedParams = [])
    {
        $this->request = new Request();
        return $this->request->request(Request::GET, $url, $namedParams);
    }
}