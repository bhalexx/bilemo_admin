<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use AppBundle\Form\ManufacturerType;

class ManufacturerController extends Controller
{
    /**
     * @Route("/manufacturers", name="manufacturers")
     */
    public function indexAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/manufacturers';
        
        $headers = [
            'headers' => [
                'Content-type' => 'application/json',
                'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
            ]
        ];

        $manufacturers = $client->get($uri, $headers);

        return $this->render('manufacturers/index.html.twig', [
            'manufacturers' => json_decode($manufacturers->getBody(), true)
        ]);
    }

    /**
     * @Route("/manufacturers/create", name="manufacturers_create")
     */
    public function createAction(Request $request)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/manufacturers';        
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Create form
        $form = $this->createForm(ManufacturerType::class, []);        

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newManufacturer = $request->request->get('manufacturer');
            
            try {
                $client->post($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newManufacturer)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Enregistrement effectué.');             
            } catch (RequestException $e) {                
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');                
            }
            return $this->redirectToRoute('manufacturers');
        }

        return $this->render('manufacturers/create.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/manufacturers/update/{id}", name="manufacturers_update", requirements = { "id": "\d+" })
     */
    public function updateAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/manufacturers/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get manufacturer
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $manufacturer = json_decode($response->getBody(), true);

        // Create form
        $data = [
            'name' => $manufacturer['name']
        ];
        $form = $this->createForm(ManufacturerType::class, $data);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newManufacturer = $request->request->get('manufacturer');
            
            try {
                $client->put($uri, [
                    'headers' => $headers,
                    'body' => json_encode($newManufacturer)
                ]);

                $request->getSession()->getFlashBag()->add('success', 'Modification effectuée.');
            } catch (RequestException $e) {
                $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue.');
            }
            return $this->redirectToRoute('manufacturers');      
        }

        return $this->render('manufacturers/update.html.twig', [
            'manufacturer' => $manufacturer,
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/manufacturers/delete/{id}", name="manufacturers_delete", requirements = { "id": "\d+" })
     */
    public function deleteAction(Request $request, $id)
    {
        $client = $this->get('csa_guzzle.client.bilemo_api');
        $uri = 'api/manufacturers/'.$id;
        $headers = [
            'Content-type' => 'application/json',
            'Authorization' => 'Bearer '.$this->get('session')->get('access_token')
        ];

        // Get manufacturer
        $response = $client->get($uri, [
            'headers' => $headers
        ]);
        $manufacturer = json_decode($response->getBody(), true);

        // Create an empty form with only CSRF to secure manufacturer deletion
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
            return $this->redirectToRoute('manufacturers');
        }

        return $this->render('manufacturers/delete.html.twig', [
            'manufacturer' => $manufacturer,
            'form' => $form->createView() 
        ]);
    }
}
