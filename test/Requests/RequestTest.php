<?php

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public $cookieJar = "";

    public function setUp()
    {
        $this->cookieJar = __DIR__ . "/cookie.jar";
        $cookieJarSetup = fopen($this->cookieJar, "w+");
        fclose($cookieJarSetup);
        chmod($this->cookieJar, 0777);
    }

    public function tearDown()
    {
        unlink($this->cookieJar);
    }

    public function testHeader()
    {
        $request = new Requests\Request();
        $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["headers" => ["User-Agent: RequestsTest"]]);
        $this->assertEquals("RequestsTest", $request->headers["user-agent"]);
    }

    public function testCookies()
    {
        $request = new Requests\Request();
        $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["cookies" => ["PHPSESSID=263b62a606693a0c1133cf5aac9db6b2","MYPHPNET=en%2Cquickref%2CNONE%2C0%2C"]]);
        $this->assertEquals("PHPSESSID=263b62a606693a0c1133cf5aac9db6b2;MYPHPNET=en%2Cquickref%2CNONE%2C0%2C", $request->headers["cookie"]);
    }

    public function testSetCookieJar()
    {
        $request = new Requests\Request();
        $response = $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["cookies" => $this->cookieJar]);

        $this->assertRegExp("/".file_get_contents(__DIR__ . "/cookie.jar.expected")."/", file_get_contents($this->cookieJar));

        $setCookie = explode(";", $response->headers["set-cookie"], 2);
        return $setCookie[0];
    }

    /**
     * @depends testSetCookieJar
     */
    public function testCookieJar($cookie)
    {
        $request = new Requests\Request();
        $request->request(Requests\Request::GET, "http://localhost:8000/RequestTest.php", ["cookies" => [$cookie]]);
        $this->assertEquals($cookie, $request->headers["cookie"]);
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