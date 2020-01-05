<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Application;
use App\Entity\Commentaire;
use Twig\Environment;


/**
 * @Route("/KTN")
 */
class Modify extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
    * @Route("/modify/{id}", name="modify", requirements={"id" = "\d+"})
    */
    public function modify(Environment $twig, Request $request, $id)
    {
        $application_allreadymade = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();
        $application_allreadymade = $application_allreadymade[$id];

        $application = new Application();

        $form = $this->createFormBuilder($application)
            ->add('Title', TextType::class, array('label' => false, 'data' => $application_allreadymade->getTitle()))
            ->add('Description', TextType::class, array('label' => false, 'data' => $application_allreadymade->getDescription()))
            ->add('Etoiles', NumberType::class, array('label' => false, 'data' => $application_allreadymade->getEtoiles()))
            ->add('photo', FileType::class, array('label' => false, 'required' => false))
            ->add('files', FileType::class, array('label' => false, 'required' => false))
            ->add('save', SubmitType::class, array('label' => "Publier l'application"))
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
            {
                $application = $form->getData();

                $fileName = $application->getTitle();
                $Description = $application->getDescription();
                $Etoiles = $application->getEtoiles();

                $application_allreadymade->setTitle($fileName);
                $application_allreadymade->setDescription($Description);
                $application_allreadymade->setEtoiles($Etoiles);

                $file = $application->getPhoto();
                if (!is_null($file))
                    {
                        $filename = $this->params->get('kernel.project_dir').'\public\photos'.'\\'.$application_allreadymade->getPhoto();
                        $filesystem = new Filesystem();
                        $filesystem->remove($filename);

                        $application_allreadymade->setPhoto($fileName.'.'.$file->getClientOriginalExtension());
                        $file->move($this->getParameter('photos_directory'), $fileName.'.'.$file->getClientOriginalExtension());
                    }

                $file = $application->getFiles();
                if (!is_null($file))
                    {
                        $filename = $this->params->get('kernel.project_dir').'\public\files'.'\\'.$application_allreadymade->getFiles();
                        $filesystem = new Filesystem();
                        $filesystem->remove($filename);

                        $application_allreadymade->setFiles($fileName.'.'.$file->getClientOriginalExtension());
                        $file->move($this->getParameter('files_directory'), $fileName.'.'.$file->getClientOriginalExtension());
                    }

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($application_allreadymade);
                $entityManager->flush();

                return $this->redirectToRoute('home');
            }

        $content = $twig->render('newapplication.html.twig', array('form' => $form->createView()));

        return new Response($content);
    }
}