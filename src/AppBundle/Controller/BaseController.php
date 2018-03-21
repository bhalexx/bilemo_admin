<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class BaseController extends Controller
{
    protected $client; 
    protected $headers;

    // public function __construct()
    // {
    //     $this->client = $this->getHttpClient();
    //     $this->headers = $this->getHeaders();
    // }

    protected function getHttpClient()
    {
        return $this->get('csa_guzzle.client.bilemo_api');
    }

    protected function getHeaders()
    {
        return [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];
    }

    protected function request($uri, $method = 'GET', $body = array())
    {
        $response = null;
        switch ($method) {   
            case 'POST':
                $request = $this->getHttpClient()->post($uri, [
                    'headers' => $this->getHeaders(),
                    'body' => json_encode($body)
                ]); 
                break;
            case 'PUT':
                $request = $this->getHttpClient()->put($uri, [
                    'headers' => $this->getHeaders(),
                    'body' => json_encode($body)
                ]);
                break;
            case 'DELETE':
                $request = $this->getHttpClient()->delete($uri, [
                    'headers' => $this->getHeaders()
                ]);
                break;
            default: // GET
                $request = $this->getHttpClient()->get($uri, [
                    'headers' => $this->getHeaders()
                ]);
        }

        try {
            $response = json_decode($request->getBody(), true);
        } catch (RequestException $e) {
            dump($e);
        }

        return $response;
    }

    protected function feedBack(Request $request, $type = "error", $message = "Une erreur est survenue.")
    {
        $request->getSession()->getFlashBag()->add($type, $message); 
    }
}
