<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use AppBundle\Form\ApplicationType;

class ApplicationController extends Controller
{
    /**
     * @Route("/applications", name="applications")
     */
    public function indexAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/applications';
        
        $headers = [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
            ]
        ];

        $applications = $client->get($uri, $headers);

        return $this->render('applications/index.html.twig', [
            'applications' => json_decode($applications->getBody(), true)
        ]);
    }

    /**
     * @Route("/applications/view/{id}", name="applications_view", requirements = { "id": "\d+" })
     */
    public function viewAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/applications/'.$id;
        
        $headers = [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
            ]
        ];

        $application = $client->get($uri, $headers);

        return $this->render('applications/view.html.twig', [
            'application' => json_decode($application->getBody(), true)
        ]);
    }

    /**
     * @Route("/applications/create", name="applications_create")
     */
    public function createAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/applications';        
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Create form
        $data = [
            'roles' => ['ROLE_APPLICATION']
        ];
        $form = $this->createForm(ApplicationType::class, $data);        

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newApplication = $request->request->get('application');
            $newApplication['roles'] = [$newApplication['roles']];
            
            try {
                $client->post($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newApplication)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Enregistrement effectué.');             
            } catch (RequestException $e) {                
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');                
            }
            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/create.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/applications/update/{id}", name="applications_update", requirements = { "id": "\d+" })
     */
    public function updateAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/applications/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get application
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $application = json_decode($response->getBody(), true);

        // Create form
        $data = [
            'username' => $application['username'],
            'email' => $application['email'],
            'uri' => $application['uri'],
            'roles' => $application['roles']
        ];
        $form = $this->createForm(ApplicationType::class, $data);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newApplication = $request->request->get('application');
            $newApplication['roles'] = [$newApplication['roles']];
            
            try {
                $client->put($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newApplication)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Modification effectuée.');
            } catch (RequestException $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');
            }
            return $this->redirectToRoute('applications');      
        }

        return $this->render('applications/update.html.twig', [
            'application' => $application,
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/applications/delete/{id}", name="applications_delete", requirements = { "id": "\d+" })
     */
    public function deleteAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/applications/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get application
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $application = json_decode($response->getBody(), true);

        // Create an empty form with only CSRF to secure application deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $client->delete($uri, [
                    'headers' => $headers
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Modification effectuée.');
            } catch (RequestException $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');
            }
            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/delete.html.twig', [
            'application' => $application,
            'form' => $form->createView() 
        ]);
    }
}
