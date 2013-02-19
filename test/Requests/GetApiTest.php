<?php

class GetApiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once realpath(__DIR__ . '/../../lib/Requests/Api.php');
    }

    public function testOk()
    {
        $response = Requests\Get("http://localhost:8000/GetTest.php");
        $this->assertTrue($response->isOk(), $response->statusCode . " was returned.");
    }

    public function testResponseHeader()
    {
        $response = Requests\Get("http://localhost:8000/GetTest.php");
        $this->assertEquals("text/plain", $response->headers["content-type"]);
    }
}