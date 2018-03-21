<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use AppBundle\Form\MobileType;
use AppBundle\Handler\MobileHandler;

class MobileController extends BaseController
{
    /**
     * @Route(
     *     "/mobiles/p{page}",
     *     name="mobiles",
     *     requirements = { "page" = "\d+" },
     *     defaults = { "page" = 1 }
     * )
     */
    public function indexAction(Request $request, $page)
    {
        $uri = sprintf('api/mobiles?limit=%d&offset=%s', $this->container->getParameter('mobile_limit'), $page);
        
        $mobiles = $this->request($uri);

        return $this->render('mobiles/index.html.twig', [
            'mobiles' => $mobiles
        ]);
    }

    /**
     * @Route("/mobiles/view/{id}", name="mobiles_view", requirements = { "id": "\d+" })
     */
    public function viewAction(Request $request, $id)
    {
        $uri = 'api/mobiles/'.$id;

        $mobile = $this->request($uri);

        return $this->render('mobiles/view.html.twig', [
            'mobile' => $mobile
        ]);
    }

    /**
     * @Route("/mobiles/create", name="mobiles_create")
     */
    public function createAction(Request $request)
    {
        $uri = 'api/mobiles';

        $manufacturers = $this->request('api/manufacturers');
        $oss = $this->request('api/os');

        // Create form
        $mobile['manufacturers'] = $manufacturers;
        $mobile['oss'] = $oss;
        $form = $this->createForm(MobileType::class, $mobile);

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $handler = new MobileHandler();
            $newMobile = $handler->handle($request->request->get('mobile'), $manufacturers, $oss);
            try {
                $this->request($uri, 'POST', $newMobile);
                $this->feedBack($request, "success", "Le nouveau mobile a correctement été enregistré.");             
                return $this->redirectToRoute('mobiles');
            } catch (RequestException $e) {                
                $this->feedBack($request, "danger", "Une erreur est survenue lors de l'enregistrement du nouveau mobile.");
            }
        }

        return $this->render('mobiles/create.html.twig', [
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/mobiles/update/{id}", name="mobiles_update", requirements = { "id": "\d+" })
     */
    public function updateAction(Request $request, $id)
    {
        $uri = 'api/mobiles/'.$id;

        $mobile = $this->request($uri);
        $manufacturers = $this->request('api/manufacturers');
        $oss = $this->request('api/os');

        // Create form
        $mobile['manufacturers'] = $manufacturers;
        $mobile['oss'] = $oss;
        $form = $this->createForm(MobileType::class, $mobile);        

        // On form submit
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $handler = new MobileHandler();
            $newMobile = $handler->handle($request->request->get('mobile'), $manufacturers, $oss);
            try {
                $this->request($uri, 'PUT', $newMobile);
                $this->feedBack($request, "success", "Le mobile a correctement été modifié.");             
                return $this->redirectToRoute('mobiles');
            } catch (RequestException $e) {                
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la modification du mobile.");
            }
        }

        return $this->render('mobiles/update.html.twig', [
            'mobile' => $mobile,
            'form' => $form->createView() 
        ]);
    }

    /**
     * @Route("/mobiles/delete/{id}", name="mobiles_delete", requirements = { "id": "\d+" })
     */
    public function deleteAction(Request $request, $id)
    {
        $uri = 'api/mobiles/'.$id;

        // Get mobile
        $mobile = $this->request($uri);

        // Create an empty form with only CSRF to secure mobile deletion
        $form = $this->get('form.factory')->create();

        // On deletion confirm
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            try {
                $this->request($uri, 'DELETE');
                $this->feedBack($request, "success", "La suppression du mobile s'est correctement déroulée.");
            } catch (RequestException $e) {
                $this->feedBack($request, "danger", "Une erreur est survenue lors de la suppression du mobile.");
            }
            return $this->redirectToRoute('mobiles');
        }

        return $this->render('mobiles/delete.html.twig', [
            'mobile' => $mobile,
            'form' => $form->createView() 
        ]);
    }
}
