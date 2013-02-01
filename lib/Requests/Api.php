<?php

namespace Requests;

function Get($url, $namedParams=[])
{
    $request = new Request();
    return $request->request(Request::GET, $url, $namedParams);
}

function Post($url, $namedParams=[])
{
    $request = new Request();
    return $request->request(Request::POST, $url, $namedParams);
}