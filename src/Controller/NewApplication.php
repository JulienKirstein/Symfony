<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Application;
use App\Entity\Commentaire;
use Twig\Environment;


/**
 * @Route("/KTN")
 */
class NewApplication extends AbstractController
{
    /**
    * @Route("/newapplication", name="new_application")
    */
    public function new_application(Environment $twig, Request $request)
    {
        $application = new Application();

        $form = $this->createFormBuilder($application)
            ->add('Title', TextType::class, array('label' => false))
            ->add('Description', TextType::class, array('label' => false))
            ->add('Etoiles', NumberType::class, array('label' => false))
            ->add('photo', FileType::class, array('label' => false))
            ->add('files', FileType::class, array('label' => false))
            ->add('save', SubmitType::class, array('label' => "Publier l'application"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
            {
                $application = $form->getData();

                $fileName = $application->getTitle();

                $time = new \DateTime('now');
                $application->setDateMiseEnLigne($time->format('d/m/Y'));

                $file = $application->getPhoto();
                $application->setPhoto($fileName.'.'.$file->getClientOriginalExtension());
                $file->move($this->getParameter('photos_directory'), $fileName.'.'.$file->getClientOriginalExtension());

                $file = $application->getFiles();
                $application->setFiles($fileName.'.'.$file->getClientOriginalExtension());
                $file->move($this->getParameter('files_directory'), $fileName.'.'.$file->getClientOriginalExtension());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($application);
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }

        $content = $twig->render('newapplication.html.twig', array('form' => $form->createView()));

        return new Response($content);
    }
}