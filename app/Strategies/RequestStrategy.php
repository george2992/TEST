<?php
namespace App\Strategies;

use GuzzleHttp\Client;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;

class RequestStrategy
{
    public function make(array $data = [], $method = 'POST', $uri = 'https://atomic.incfile.com/fakepost')
    {
        $method = Str::lower($method);

        try {
            $client = new Client([ 'verify' => false ]);

            $response = $client->{$method}($uri);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        } catch (ServerException $e) {
            $response = $e->getResponse();
        }

        return $response;
    }
}