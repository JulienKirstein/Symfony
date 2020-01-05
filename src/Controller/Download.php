<?php

namespace App\Controller;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Application;
use App\Entity\Commentaire;


/**
 * @Route("/KTN")
 */
class Download extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    /**
     * @Route("/download/{id}", name="download_file", requirements={"id" = "\d+"})
    **/
    public function downloadFileAction($id)
    {
        // download the file by id
        $db = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        $filepath = $this->params->get('kernel.project_dir').'\public\files'.'\\'.$db[$id]->getFiles();
        $response = new BinaryFileResponse($filepath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
        return $response;
    }
}