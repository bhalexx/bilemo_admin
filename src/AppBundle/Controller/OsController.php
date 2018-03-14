<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use AppBundle\Form\OsType;

class OsController extends Controller
{
    /**
     * @Route("/os", name="os")
     */
    public function indexAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/os';
        
        $headers = [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
            ]
        ];

        $oss = $client->get($uri, $headers);

        return $this->render('os/index.html.twig', [
            'oss' => json_decode($oss->getBody(), true)
        ]);
    }

    /**
     * @Route("/os/create", name="os_create")
     */
    public function createAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/os';        
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Create form
        $form = $this->createForm(OsType::class, []);        

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newOs = $request->request->get('os');
            
            try {
                $client->post($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newOs)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Enregistrement effectué.');             
            } catch (RequestException $e) {                
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');                
            }
            return $this->redirectToRoute('os');
        }

        return $this->render('os/create.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/os/update/{id}", name="os_update", requirements = { "id": "\d+" })
     */
    public function updateAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/os/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get OS
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $os = json_decode($response->getBody(), true);

        // Create form
        $data = [
            'name' => $os['name']
        ];
        $form = $this->createForm(OsType::class, $data);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newOs = $request->request->get('os');
            
            try {
                $client->put($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newOs)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Modification effectuée.');
            } catch (RequestException $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');
            }
            return $this->redirectToRoute('os');      
        }

        return $this->render('os/update.html.twig', [
            'os' => $os,
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/os/delete/{id}", name="os_delete", requirements = { "id": "\d+" })
     */
    public function deleteAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/os/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get OS
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $os = json_decode($response->getBody(), true);

        // Create an empty form with only CSRF to secure OS deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $client->delete($uri, [
                    'headers' => $headers
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Suppression effectuée.');
            } catch (RequestException $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');
            }
            return $this->redirectToRoute('os');
        }

        return $this->render('os/delete.html.twig', [
            'os' => $os,
            'form' => $form->createView() 
        ]);
    }
}
