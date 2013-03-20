<?php

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testHeaders()
    {
        $request = new Requests\Session();
        $request->request("get", "http://localhost:8000/RequestTest.php", ["headers" => ["User-Agent: RequestsTest"]]);
        $this->assertEquals("RequestsTest", $request->headers["user-agent"]);
    }

    public function testUserAgent()
    {
        $request = new Requests\Session();
        $request->request("get", "http://localhost:8000/RequestTest.php", []);
        $this->assertEquals("Requests/" . \Requests\Version, $request->headers["user-agent"]);
    }

    public function testTimeout()
    {
        $request = new Requests\Session();
        $response = $request->request("get", "http://localhost:8000/RequestTest.php", ["params" => ["testTimeout" => "yes"], "timeout" => 1]);

        $this->assertLessThan($response->totaltime, 1);
    }

    public function testRedirectNotFollowed()
    {
        $request = new Requests\Session();
        $response = $request->request("get", "http://localhost:8080/RequestTest.php", []);

        $this->assertEquals(301, $response->statuscode);
        $this->assertNotEquals("8000 HTTP/1.1", $request->headers["get /localhost"]);
    }

    public function testRedirectFollowed()
    {
        $request = new Requests\Session();
        $response = $request->request("get", "http://localhost:8080/RequestTest.php", ["allowRedirects" => true]);

        $this->assertEquals(200, $response->statuscode);
    }

    public function testAuth()
    {
        $request = new Requests\Session();
        $response = $request->request("get", "http://localhost:8000/RequestTest.php", ["params" => ["testAuth" => "yes"], "auth" => ["user" => "testuser", "pass" => "testPass"]]);

        $this->assertEquals(200, $response->statuscode);
    }

    public function testFiles()
    {
        $this->markTestIncomplete("File testing is not yet implemented.");
    }

    public function testProxies()
    {
        $this->markTestIncomplete("Proxy is implemented, but not test has been created.");
    }

    public function testVerify()
    {
        $this->markTestIncomplete("Verify is implemented, but not test has been created.");
    }

    public function testStream()
    {
        $this->markTestIncomplete("Stream testing is not yet implemented.");
    }

    public function testCert()
    {
        $this->markTestIncomplete("Cert is implemented, but not test has been created.");
    }

}