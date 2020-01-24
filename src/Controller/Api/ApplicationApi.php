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


class ApplicationApi extends AbstractController
{
    private $params;
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }


    /**
     * @Route("/GetApplicationApi/{id}", name="GetApplicationApi", methods={"GET"})
     */
    public function getProducts1(Environment $twig, EntityManagerInterface $em, int $id)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        if ( $id < sizeof($repository))
            {
                $content = $repository[$id];
                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => true, 'Application n°'.$id => $content), 'json');
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
     * @Route("/AddCommentToApplicationApi/{id}", name="AddCommentToApplicationApi", methods={"POST"})
     */
    public function AddCommentToApplication(Request $request, int $id)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        if ( $id < sizeof($repository))
            {
                $comment = new Commentaire();
                $comment->setCommentaire(json_decode($request->getContent(), true)['Comment']);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);

                $repository[$id]->addCommentaire($comment);
                $entityManager->persist($repository[$id]);
                $entityManager->flush();


                $content = $repository[$id];
                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => true), 'json');
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
     * @Route("/DeleteApplicationApi/{id}", name="DeleteApplicationApi", methods={"DELETE"}, requirements={"id" = "\d+"})
     */
    public function DeleteApplication(int $id)
    {
        $to_delete = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        if ( $id < sizeof($to_delete))
            {
                $em = $this->getDoctrine()->getManager();
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
                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => true), 'json');
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
     * @Route("/ModifyApplicationApi/{id}", name="ModifyApplicationApi", methods={"POST"}, requirements={"id" = "\d+"})
     */
    public function ModifyApplication(Request $request, int $id)
    {
        $repository = $this->getDoctrine()
            ->getRepository(Application::class)
            ->findAll();

        if ( $id < sizeof($repository))
            {
                $application = $repository[$id];
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
        else
            {
                $serializer = SerializerBuilder::create()->build();
                $jsonObject = $serializer->serialize(array('result' => false), 'json');
                $products = new JsonResponse();
                $products->setContent($jsonObject);
                return $products;
            }
    }
}