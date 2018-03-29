<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\ApplicationType;
use AppBundle\Handler\ApplicationHandler;

class ApplicationController extends BaseController
{
    /**
     * @Route("/applications", name="applications")
     */
    public function indexAction(Request $request)
    {
        $uri = 'api/applications';
        $applications = $this->request($uri);

        return $this->render('applications/index.html.twig', [
            'applications' => $applications
        ]);
    }

    /**
     * @Route("/applications/view/{id}", name="applications_view", requirements = { "id": "\d+" })
     */
    public function viewAction(Request $request, $id)
    {
        $uri = 'api/applications/'.$id;
        $application = $this->request($uri);

        return $this->render('applications/view.html.twig', [
            'application' => $application
        ]);
    }

    /**
     * @Route("/applications/create", name="applications_create")
     */
    public function createAction(Request $request)
    {
        $uri = 'api/applications';

        // Create form
        $data = [
            'roles' => ['ROLE_APPLICATION']
        ];
        $form = $this->createForm(ApplicationType::class, $data);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $handler = new ApplicationHandler();
            $newApplication = $handler->handle($request->request->get('application'));

            try {
                $this->request($uri, 'POST', $newApplication);
                $this->feedBack($request, "success", "La nouvelle application partenaire a correctement été enregistrée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de l'enregistrement de la nouvelle application partenaire.");
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
        $uri = 'api/applications/'.$id;
        $application = $this->request($uri);

        // Create form
        $form = $this->createForm(ApplicationType::class, $application);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $handler = new ApplicationHandler();
            $newApplication = $handler->handle($request->request->get('application'));

            try {
                $application = $this->request($uri, 'PUT', $newApplication);
                $this->feedBack($request, "success", "L'application partenaire a correctement été modifiée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la modification de l'application partenaire.");
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
        $uri = 'api/applications/'.$id;
        $application = $this->request($uri);

        // Create an empty form with only CSRF to secure application deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $application = $this->request($uri, 'DELETE');
                $this->feedBack($request, "success", "La suppression de l'application partenaire s'est correctement effectuée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la suppression de l'application partenaire.");
            }
            return $this->redirectToRoute('applications');
        }

        return $this->render('applications/delete.html.twig', [
            'application' => $application,
            'form' => $form->createView()
        ]);
    }
}
