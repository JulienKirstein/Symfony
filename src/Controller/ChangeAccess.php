<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/KTN")
 */
class ChangeAccess extends AbstractController
{
    /**
     * @Route("/changeaccess", name="changeaccess")
    **/
    public function changeaccess()
    {
        // Change the state of the user to admin if it was a basic user before else to basic
        $session = new Session();
        $IsAdmin = $session->get('IsAdmin');
        if ($IsAdmin != true)
        {
            $IsAdmin = $session->set('IsAdmin', true);
        }
        else
        {
            $IsAdmin = $session->set('IsAdmin', false);
        }

        return $this->redirectToRoute('home');
    }
}