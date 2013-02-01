<?php

class GetTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once realpath(__DIR__ . '/../../lib/Requests/Api.php');
    }

    public function testOk()
    {
        $response = Requests\Get("https://www.google.com/robots.txt");
        $this->assertTrue($response->isOk(), $response->statusCode . " was returned.");
    }

    public function testHeader()
    {
        $response = Requests\Get("https://www.google.com/robots.txt");
        $this->assertEquals("text/plain", $response->headers["content-type"]);
    }
}