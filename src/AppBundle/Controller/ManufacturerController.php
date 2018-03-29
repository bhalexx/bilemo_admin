<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ManufacturerType;

class ManufacturerController extends BaseController
{
    /**
     * @Route("/manufacturers", name="manufacturers")
     */
    public function indexAction(Request $request)
    {
        $uri = 'api/manufacturers';
        $manufacturers = $this->request($uri);

        return $this->render('manufacturers/index.html.twig', [
            'manufacturers' => $manufacturers
        ]);
    }

    /**
     * @Route("/manufacturers/create", name="manufacturers_create")
     */
    public function createAction(Request $request)
    {
        $uri = 'api/manufacturers';

        // Create form
        $form = $this->createForm(ManufacturerType::class, []);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newManufacturer = $request->request->get('manufacturer');

            try {
                $this->request($uri, 'POST', $newManufacturer);
                $this->feedBack($request, "success", "Le nouveau fabricant a correctement été enregistré.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de l'enregistrement du nouveau fabricant.");
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
        $uri = 'api/manufacturers/'.$id;

        // Get manufacturer
        $manufacturer = $this->request($uri);

        // Create form
        $form = $this->createForm(ManufacturerType::class, $manufacturer);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newManufacturer = $request->request->get('manufacturer');

            try {
                $this->request($uri, 'PUT', $newManufacturer);
                $this->feedBack($request, "success", "Le fabricant a correctement été modifié.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la modification du fabricant.");
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
        $uri = 'api/manufacturers/'.$id;

        // Get manufacturer
        $manufacturer = $this->request($uri);

        // Create an empty form with only CSRF to secure manufacturer deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $this->request($uri, 'DELETE');
                $this->feedBack($request, "success", "La suppression du fabricant s'est correctement effectuée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la suppression du fabricant.");
            }
            return $this->redirectToRoute('manufacturers');
        }

        return $this->render('manufacturers/delete.html.twig', [
            'manufacturer' => $manufacturer,
            'form' => $form->createView()
        ]);
    }
}
