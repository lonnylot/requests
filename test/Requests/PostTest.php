<?php

class PostTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once realpath(__DIR__ . '/../../lib/Requests/Api.php');
    }
    public function testOk()
    {
        $response = Requests\Post("https://www.gmail.com/", ["data"=>["foo"=>"bar"]]);
        $this->assertTrue($response->isOk(), $response->statusCode . " was returned.");
    }

    public function testHeader()
    {
        $response = Requests\Post("https://www.gmail.com/", ["data"=>["foo"=>"bar"]]);
        $this->assertEquals("text/html; charset=UTF-8", $response->headers["content-type"]);
    }
}