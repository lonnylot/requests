<?php

class CookieTest extends \PHPUnit_Framework_TestCase
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

    public function testCookies()
    {
        $request = new Requests\Session();
        $request->request(Requests\Session::GET, "http://localhost:8000/RequestTest.php", ["cookies" => ["PHPSESSID=263b62a606693a0c1133cf5aac9db6b2","MYPHPNET=en%2Cquickref%2CNONE%2C0%2C"]]);
        $this->assertEquals("PHPSESSID=263b62a606693a0c1133cf5aac9db6b2;MYPHPNET=en%2Cquickref%2CNONE%2C0%2C", $request->headers["cookie"]);
    }

    public function testSetCookieJar()
    {
        $request = new Requests\Session();
        $response = $request->request(Requests\Session::GET, "http://localhost:8000/RequestTest.php", ["cookies" => $this->cookieJar]);

        $this->assertRegExp("/".file_get_contents(__DIR__ . "/cookie.jar.expected")."/", file_get_contents($this->cookieJar));

        $setCookie = explode(";", $response->headers["set-cookie"], 2);
        return $setCookie[0];
    }

    /**
     * @depends testSetCookieJar
     */
    public function testCookieJar($cookie)
    {
        $request = new Requests\Session();
        $request->request(Requests\Session::GET, "http://localhost:8000/RequestTest.php", ["cookies" => [$cookie]]);
        $this->assertEquals($cookie, $request->headers["cookie"]);
    }
}