<?php

namespace App\Controller\Api;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use App\Entity\Application;
use App\Entity\Commentaire;
use Twig\Environment;


class ApplicationsApi extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }


    /**
     * @Route("/AllApplicationsApi", name="AllApplicationsApi", methods={"GET"})
     */
    public function getAllApplications(Environment $twig, EntityManagerInterface $em)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        $serializer = SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize(array('db' => $repository), 'json');
        $products = new JsonResponse();
        $products->setContent($jsonObject);
        return $products;
    }


    /**
     * @Route("/AccessStateApi", name="AccessStateApi", methods={"GET"})
     */
    public function getAccessState(Environment $twig, EntityManagerInterface $em)
    {
        $session = new Session();
        $IsAdmin = $session->get('IsAdmin');

        if ($IsAdmin != true)
        {
            $IsAdmin = false;
        }

        $serializer = SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize(array('IsAdmin' => $IsAdmin), 'json');
        $products = new JsonResponse();
        $products->setContent($jsonObject);
        return $products;
    }


    /**
     * @Route("/SetAccessStateApi", name="SetAccessStateApi", methods={"PUT"})
     */
    public function SetAccessState(Request $request)
    {
        $access = json_decode($request->getContent(), true)['IsAdmin'];

        $session = new Session();
        $IsAdmin = $session->get('IsAdmin');

        if (($access == "true") || ($access == "false"))
            {
                $IsAdmin = $session->set('IsAdmin', $access);

                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => true, 'IsAdmin' => $access), 'json');
                $products = new JsonResponse();
                $products->setContent($jsonObject);
                return $products;
            }
        else
            {
                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => false), 'json');
                $products = new JsonResponse();
                $products->setContent($jsonObject);
                return $products;
            }
    }


    /**
     * @Route("/NewApplicationApi", name="NewApplicationApi", methods={"POST"})
     */
    public function NewApplication(Request $request)
    {
        $application = new Application();
        $application->setEtoiles(json_decode($request->getContent(), true)["Etoiles"]);
        $application->setDescription(json_decode($request->getContent(), true)["Description"]);
        $application->setTitle(json_decode($request->getContent(), true)["Title"]);

        $urlPhoto = json_decode($request->getContent(), true)["Photo"];
        $urlFiles = json_decode($request->getContent(), true)["Files"];

        $fileName = $application->getTitle();

        $application->setPhoto($fileName.'.'.pathinfo($urlPhoto, PATHINFO_EXTENSION));
        $application->setFiles($fileName.'.'.pathinfo($urlFiles, PATHINFO_EXTENSION));

        $time = new \DateTime('now');
        $application->setDateMiseEnLigne($time->format('d/m/Y'));

        file_put_contents('photos/'.$fileName.'.'.pathinfo($urlPhoto, PATHINFO_EXTENSION), file_get_contents($urlPhoto));
        file_put_contents('files/'.$fileName.'.'.pathinfo($urlFiles, PATHINFO_EXTENSION), file_get_contents($urlFiles));

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($application);
        $entityManager->flush();


        $serializer = SerializerBuilder::create()->build();
        $jsonObject = $serializer->serialize(array('result' => true), 'json');
        $products = new JsonResponse();
        $products->setContent($jsonObject);
        return $products;
    }
}