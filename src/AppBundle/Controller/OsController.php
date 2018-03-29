<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use AppBundle\Form\OsType;

class OsController extends BaseController
{
    /**
     * @Route("/os", name="os")
     */
    public function indexAction(Request $request)
    {
        $uri = 'api/os';

        $oss = $this->request($uri);

        return $this->render('os/index.html.twig', [
            'oss' => $oss
        ]);
    }

    /**
     * @Route("/os/create", name="os_create")
     */
    public function createAction(Request $request)
    {
        $uri = 'api/os';

        // Create form
        $form = $this->createForm(OsType::class, []);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newOs = $request->request->get('os');

            try {
                $this->request($uri, 'POST', $newOs);
                $this->feedBack($request, "success", "Le nouveau système d'exploitation a correctement été enregistré.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la création du nouveau système d'exploitation.");
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
        $uri = 'api/os/'.$id;

        // Get OS
        $os = $this->request($uri);

        // Create form
        $form = $this->createForm(OsType::class, $os);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $newOs = $request->request->get('os');

            try {
                $this->request($uri, 'PUT', $newOs);
                $this->feedBack($request, "success", "Le système d'exploitation a correctement été modifié.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la modification du système d'exploitation.");
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
        $uri = 'api/os/'.$id;

        // Get OS
        $os = $this->request($uri);

        // Create an empty form with only CSRF to secure OS deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $this->request($uri, 'DELETE');
                $this->feedBack($request, "success", "La suppression du système d'exploitation s'est correctement effectuée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la suppression du système d'exploitation.");
            }
            return $this->redirectToRoute('os');
        }

        return $this->render('os/delete.html.twig', [
            'os' => $os,
            'form' => $form->createView()
        ]);
    }
}
