<?php

namespace AppBundle\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ApplicationHandler
{
	public function handle($application)
    {
    	//Set roles array to application
        $application['roles'] = [$application['roles']];

        return $application;
    }
}