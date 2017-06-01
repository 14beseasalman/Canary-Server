<?php

// Tests the CANARY API
class APITest extends PHPUnit_Framework_TestCase
{
    private $http;

    public function setUp()
    {
        $this->http = new GuzzleHttp\Client(['base_uri' => 'https://canary-asaddev.rhcloud.com/']);
    }

    public function tearDown()
    {
        $this->http = null;
    }

    // Tests invalid POST requests on the API
    public function testInvalidRequest()
    {
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false]);
        $this->assertEquals($response->getStatusCode(), 400);

        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 400);

        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'off' ]]);
        $this->assertEquals($response->getStatusCode(), 400);

        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'nil', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 400);
    }

    // Tests tracking functionality of the API
    public function testTrackingOnOff()
    {
        // Turn tracking on and assert
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'ON', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getBody(), "success");

        // Update tracking with latest time and assert
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'ON', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getBody(), "success");

        // Turn tracking off and assert
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'OFF', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getBody(), "success");
    }

    // Tests boundary cases of the API
    public function testBoundaryCase()
    {
        // Turn tracking off multiple times in sequence and assert
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'OFF', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getBody(), "success");
        $response = $this->http->request('POST', 'canary.php', ['http_errors' => false, 'form_params' => [ 'action' => 'OFF', 'id' => '000000' ]]);
        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertEquals($response->getBody(), "success");
    }
}
