<?php

namespace Requests;

const Version = "0.1.1-dev";

/**
 * @api
 *
 * @param $method The \Requests\Session method constant
 * @param $url
 * @param array $namedParams
 *
 * @example Possible values for $namedParams:
 * @example params: (optional) Array of key=>val to be sent in the query string
 * @example data: (optional) Array or String to be sent in the body
 * @example headers: (optional) Array of HTTP Headers
 * @example cookies: (optional) Array of cookies or String of the file for a cookie JAR
 * @example auth: (optional) Array of ["user"=>"","pass"=>""] for Basic HTTP Auth
 * @example timeout: (optional) Float describing the timeout of the request.
 * @example allowRedirects: (optional) Boolean. Set to True if redirect following is allowed.
 * @example TODO: proxies: (optional) Dictionary mapping protocol to the URL of the proxy.
 * @example TODO: verify: (optional) if ``True``, the SSL cert will be verified. TODO: A CA_BUNDLE path can also be provided.
 * @example TODO: files: (optional) Dictionary of 'name': file-like-objects (or {'name': ('filename', fileobj)}) for multipart encoding upload.
 * @example TODO: stream: (optional) if ``False``, the response content will be immediately downloaded.
 * @example TODO: cert: (optional) if String, path to ssl client cert file (.pem). If Tuple, ('cert', 'key') pair.
 *
 * @return Response
 */
function Request($method, $url, $namedParams=[])
{
    $session = new Session();
    return $session->request($method, $url, $namedParams);
}

/**
 * @api
 *
 * @param $url
 * @param array $namedParams
 * @return Response
 */
function Get($url, $namedParams=[])
{
    if (array_key_exists("allowRedirects", $namedParams) === false) {
        $namedParams["allowRedirects"] = 1;
    }
    return Request(Session::GET, $url, $namedParams);
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
    return Request(Session::POST, $url, $namedParams);
}

/**
 * @api
 *
 * @param $url
 * @param array $namedParams
 * @return Response
 */
function Head($url, $namedParams=[])
{
    return Request(Session::HEAD, $url, $namedParams);
}