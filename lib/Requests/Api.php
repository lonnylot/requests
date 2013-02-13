<?php

namespace Requests;

/**
 * @api
 *
 * @param $url
 * @param array $namedParams
 * @return Response
 */
function Get($url, $namedParams=[])
{
    $request = new Request();
    return $request->request(Request::GET, $url, $namedParams);
}

/**
 * @api
 *
 * @param $url
 * @param array $namedParams
 * @return Response
 */
function Post($url, $namedParams=[])
{
    $request = new Request();
    return $request->request(Request::POST, $url, $namedParams);
}