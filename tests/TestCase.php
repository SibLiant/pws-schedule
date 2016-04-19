<?php

class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

	public $client;
	public $clientToken;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
		$this->setClientToken();

        return $app;
    }


	private function setClientToken(){
		$client = factory(App\User::class)->create(['type' => 'client']);

		$this->client = $client;
		$this->clientToken = JWTAuth::fromUser($client);
	}

	public function clientGet($url,  $headers = [])
	{
		$url .= '?token=' . $this->clientToken;
		return $this->get($url, $headers);  
	}

	public function clientPost($url,  $data = [], $headers = [])
	{
		$url .= '?token=' . $this->clientToken;
		return $this->post($url, $data, $headers);  
	}

	
	/**
	 *
	 * @return date string
	 */
	public function clientJson($method, $url, $data = [], $headers = [])
	{
		$url .= '?token=' . $this->clientToken;
		return $this->json($method, $url, $data, $headers);  
	}
}

