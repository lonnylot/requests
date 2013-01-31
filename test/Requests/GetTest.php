<?php

namespace Requests;

class GetTest extends \PHPUnit_Framework_TestCase
{
    public function testOk()
    {
        $response = (new Get)->request("https://www.lonnylot.com/robots.txt");
        $this->assertTrue($response->isOk());
    }

    public function testHeader()
    {
        $response = (new Get)->request("https://www.lonnylot.com/robots.txt");
        $this->assertEquals("nginx/1.2.0", $response->server);
    }
}