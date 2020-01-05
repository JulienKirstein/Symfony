<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Application;
use App\Entity\Commentaire;
use Twig\Environment;


/**
 * @Route("/KTN")
 */
class Home extends AbstractController
{
    /**
    * @Route("/", name="home")
    */
    public function index(Environment $twig, EntityManagerInterface $em)
    {
        $session = new Session();
        $IsAdmin = $session->get('IsAdmin');

        $db = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        $content = $twig->render('home.html.twig', array('db' => $db, 'IsAdmin' => $IsAdmin));

        return new Response($content);
    }
}