<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends BaseController
{
    /**
     * @Route("/users", name="users")
     */
    public function indexAction(Request $request)
    {
        $uri = 'api/users';
        $users = $this->request($uri);

        return $this->render('users/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/users/view/{id}", name="users_view", requirements = { "id": "\d+" })
     */
    public function viewAction(Request $request, $id)
    {
        $uri = 'api/users/'.$id;
        $user = $this->request($uri);

        return $this->render('users/view.html.twig', [
            'user' => $user
        ]);
    }
}
