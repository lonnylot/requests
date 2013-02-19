<?php

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testHeader()
    {
        $request = new Requests\Request();
        $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["headers" => ["User-Agent: RequestsTest"]]);
        $this->assertEquals("RequestsTest", $request->headers["user-agent"]);
    }

    public function testTimeout()
    {
        $request = new Requests\Request();
        $response = $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["params" => ["testTimeout" => "yes"], "timeout" => 1]);

        $this->assertLessThan($response->totaltime, 1);
    }

    public function testRedirectNotFollowed()
    {
        $request = new Requests\Request();
        $response = $request->request(Requests\Request::GET, "http://localhost:8080/RequestTest.php", []);

        $this->assertEquals(301, $response->statuscode);
        $this->assertNotEquals("8000 HTTP/1.1", $request->headers["get /localhost"]);
    }

    public function testRedirectFollowed()
    {
        $request = new Requests\Request();
        $response = $request->request(Requests\Request::GET, "http://localhost:8080/RequestTest.php", ["allowRedirects" => true]);

        $this->assertEquals(200, $response->statuscode);
    }

    public function testAuth()
    {
        $request = new Requests\Request();
        $response = $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["params" => ["testAuth" => "yes"], "auth" => ["user" => "testuser", "pass" => "testPass"]]);

        $this->assertEquals(200, $response->statuscode);
    }

    public function testFiles()
    {
        $this->markTestIncomplete("File testing is not yet implemented.");
    }

    public function testProxies()
    {
        $this->markTestIncomplete("Proxy testing is not yet implemented.");
    }

    public function testVerify()
    {
        $this->markTestIncomplete("Verify testing is not yet implemented.");
    }

    public function testStream()
    {
        $this->markTestIncomplete("Stream testing is not yet implemented.");
    }

    public function testCert()
    {
        $this->markTestIncomplete("Cert testing is not yet implemented.");
    }

}