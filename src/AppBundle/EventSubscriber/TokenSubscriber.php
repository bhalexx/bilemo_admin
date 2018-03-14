<?php

	namespace AppBundle\EventSubscriber;

	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
	use Symfony\Component\EventDispatcher\EventSubscriberInterface;
	use Symfony\Component\HttpKernel\KernelEvents;
	use Symfony\Component\Security\Core\Exception\BadCredentialsException;
	use Symfony\Component\Security\Core\Exception\AuthenticationException;
	use GuzzleHttp\Client;
	use GuzzleHttp\Exception\ClientException;

	class TokenSubscriber implements EventSubscriberInterface
	{
	    private $client;
	    private $bilemoTokenUrl;
	    private $clientId;
	    private $clientSecret;
	    private $username;
	    private $password;
	    private $session;

		private $accessToken;
		private $refreshToken;
		private $expirationTime;    

	    public function __construct(Client $client, $bilemoTokenUrl, $clientId, $clientSecret, $username, $password, $session)
	    {
	        $this->client = $client;
	        $this->bilemoTokenUrl = $bilemoTokenUrl;
	        $this->clientId = $clientId;
	        $this->clientSecret = $clientSecret;
	        $this->username = $username;
	        $this->password = $password;
	        $this->session = $session;
	    }

	    public function onKernelController(FilterControllerEvent $event)
	    {
	        $controller = $event->getController();
	        
	        /*
	         * $controller passed can be either a class or a Closure.
	         * This is not usual in Symfony but it may happen.
	         * If it is a class, it comes in array format
	         */
	        if (!is_array($controller)) {
	            return;
	        }

	        // Get access_token if not exists
	        if ($this->session->get('expiration_time') === null) {
	        	$params = [
		        	'grant_type' => 'password',
		        	'client_id' => $this->clientId,
		        	'client_secret' => $this->clientSecret,
		        	'username' => $this->username,
		        	'password' => $this->password
		        ];

		        $this->getAccessToken($params);
	        } else {
	        	// If token has expired
	        	if ($this->hasExpired()) {
	        		$params = [
			        	'grant_type' => 'refresh_token',
			        	'client_id' => $this->clientId,
			        	'client_secret' => $this->clientSecret,
			        	'refresh_token' => $this->session->get('refresh_token')
			        ];

	        		$this->getAccessToken($params);
	        	}
	        }
	    }

	    private function getAccessToken($params)
	    {
	    	try {
	            $response = $this->client->post($this->bilemoTokenUrl, [
	            	'form_params' => $params
	            ]);

	            $data = json_decode($response->getBody()->getContents(), true);
	            if ($data['access_token'] !== null) {
		            $this->store($data);
		        }
	        } catch (ClientException $e) {
	            $r = $e->getResponse();
	            $error = json_decode($r->getBody()->getContents(), true);
	            $this->session->clear();
	            if ($error['error'] === 'invalid_grant') {
	                throw new BadCredentialsException();
	            }
	        }
	    }

	    private function store($data)
	    {
	    	$this->accessToken = $data['access_token'];
	    	$this->refreshToken = $data['refresh_token'];
	    	$this->expirationTime = $this->setExpirationTime($data['expires_in']);

	    	$this->session->set('access_token', $this->accessToken);
	    	$this->session->set('refresh_token', $this->refreshToken);
	    	$this->session->set('expiration_time', $this->expirationTime);
	    }

	    private function hasExpired()
	    {
	    	$now = round(microtime(true) * 1000);
	    	return $now >= $this->session->get('expiration_time');
	    }

	    private function setExpirationTime($expirationLifetime)
	    {
	    	$now = round(microtime(true) * 1000);
	    	return $now + $expirationLifetime;
	    }

	    public static function getSubscribedEvents()
	    {
	        return [
	            KernelEvents::CONTROLLER => 'onKernelController',
	        ];
	    }
	}