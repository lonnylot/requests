<?php

class PostApiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once realpath(__DIR__ . '/../../lib/Requests/Api.php');
    }
    public function testOk()
    {
        $response = Requests\Post("http://localhost:8000/PostTest.php", ["data"=>["sentPostTest"=>"yes"]]);
        $this->assertTrue($response->isOk(), $response->statusCode . " was returned.");
    }

    public function testHeader()
    {
        $response = Requests\Post("http://localhost:8000/PostTest.php", ["data"=>["sentPostTest"=>"yes"]]);
        $this->assertEquals("text/html; charset=UTF-8", $response->headers["content-type"]);
    }
}