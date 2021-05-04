<?php


namespace App\Controller;


use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Trait\SerializerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RecetteController extends AbstractController
{
    /**
     * @var RecetteRepository
     */
    private $recetteRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    use SerializerTrait;


    public function __construct(RecetteRepository $recetteRepository, EntityManagerInterface $entityManager)
    {
        $this->recetteRepository = $recetteRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/recettes", name="recettes_list", methods={"GET"})
     * @return Response
     */
    public function index() : Response
    {
        $recettesList = $this->recetteRepository->findAll();

        $recettesListSerialized = $this->serializer()->serialize($recettesList, "json");

        $response = new Response($recettesListSerialized);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
     * @Route("/recettes/{id}", name="recettes_item", methods={"GET"})
     * @param Recette $recette
     * @return Response
     */
    public function show(Recette $recette) : Response
    {
        $recetteSerialized = $this->serializer()->serialize($recette, "json");

        $response = new Response($recetteSerialized);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
     * @Route("/recettes", name="recettes_add", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function add(Request $request) : Response
    {
        $data = $request->getContent();
        $dataDeserialized = $this->serializer()->deserialize($data,Recette::class,"json");


        $this->entityManager->persist($dataDeserialized);
        $this->entityManager->flush();

        $dataArray = $this->serializer()->serialize(["data" => $dataDeserialized, "message" => "slkdkfhjslkfj"], "json");
        $response = new Response($dataArray);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
     * @Route("/recettes/{id}", name="recettes_update", methods={"PUT"})
     * @param Recette $recette
     * @return Response
     */

    public function update2(Recette $recette, Request $request)
    {
        $data = $request->getContent();
        /**
         * @var Recette $dataDeserialized
         */
        $dataDeserialized = $this->serializer()->deserialize($data, Recette::class, "json");

        $recette->setCout($dataDeserialized->getCout());
        $recette->setTempsPreparation($dataDeserialized->getTempsPreparation());
        $recette->setNbPersonne($dataDeserialized->getNbPersonne());
        $recette->setNom($dataDeserialized->getNom());
        $recette->setPublic($dataDeserialized->getPublic());

        $this->entityManager->flush();

        $recetteNormalized = $this->serializer()->normalize($recette, "json");

        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"]);

        //dd($dataDeserialized);
    }

//    public function update(Recette $recette, Request $request)
//    {
//        $data = $request->getContent();
//        $dataDeserialized = $this->serializer()->decode($data, "json");
//
//        foreach ($dataDeserialized as $key => $value)
//        {
//            $methode = "set".ucfirst($key);
//            if(method_exists($recette, $methode))
//            {
//                $recette->$methode($value);
//            }
//            else
//            {
//                return new JsonResponse(["error" => "Cette propriété " . $methode . " n'existe pas"]);
//            }
//        }
//
//        $this->entityManager->flush();
//        $recetteNormalized = $this->serializer()->normalize($recette, "json");
//
//        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"]);
//
//        //dd($recetteNormalized, $recette);
//    }


}