<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Application;
use App\Entity\Commentaire;
use Twig\Environment;


/**
 * @Route("/KTN")
 */
class Article extends AbstractController
{
    /**
    * @Route("/article/{id}", name="article", requirements={"id" = "\d+"})
    */
    public function article(Environment $twig, $id, Request $request)
    {
        $db = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        $comment = new Commentaire();

        $form = $this->createFormBuilder($comment)
            ->add('Commentaire', TextType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => 'Publier le commentaire'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            {
                $comment = $form->getData();
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $db[$id]->addCommentaire($comment);
                $entityManager->persist($db[$id]);
                $entityManager->flush();

                $content = $twig->render('article.html.twig', array('db' => $db, 'id' => $id, 'form' => $form->createView(),
                'CommentAdded' => 1));

                return new Response($content);
            }

        $content = $twig->render('article.html.twig', array('db' => $db, 'id' => $id, 'form' => $form->createView(),
        'CommentAdded' => 0));

        return new Response($content);
    }
}