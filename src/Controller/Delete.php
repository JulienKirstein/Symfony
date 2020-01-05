<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Application;
use App\Entity\Commentaire;


/**
 * @Route("/KTN")
 */
class Delete extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @Route("/delete/{id}", name="delete", requirements={"id" = "\d+"})
     */
    public function Remove($id)
    {
        // Remove the application by id in the db and files that are bind with the id
        $em = $this->getDoctrine()->getManager();

        $to_delete = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();
        $to_delete = $to_delete[$id];

        $filename = $this->params->get('kernel.project_dir').'\public\files'.'\\'.$to_delete->getFiles();
        $filesystem = new Filesystem();
        $filesystem->remove($filename);

        $filename = $this->params->get('kernel.project_dir').'\public\photos'.'\\'.$to_delete->getPhoto();
        $filesystem = new Filesystem();
        $filesystem->remove($filename);

        $comments = $to_delete->getCommentaire();
        foreach ($comments as $comment){
            $em->remove($comment);
        }

        $em->remove($to_delete);
        $em->flush();

        return $this->redirectToRoute('home');
    }
}