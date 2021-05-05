<?php


namespace App\Controller;


use App\Entity\Recette;
use App\Repository\RecetteRepository;
use App\Traits\SerializerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;


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
    public function index(Request $request) : Response
    {
        $currentUser= $this->getUser();
        $paramsURL = $request->query->all();
        $keyFilters = ["nom", "cout", "nbPersonne", "dateCreation", "tempsPreparation"];


        if($paramsURL != null && isset($paramsURL["order"]) && in_array(key($paramsURL["order"]), $keyFilters))
        {
            $criteria = $currentUser == null? ["public" => true] : [];
        }

        else if($currentUser)
        {
            $recettesList = $this->recetteRepository->findBy([], ["nom" => "asc"]);
        }
        else
        {
            $recettesList = $this->recetteRepository->findBy(["public" => true]);
        }


        $recettesListSerialized = $this->serializer()->serialize($recettesList, "json");

        $response = new Response($recettesListSerialized, Response::HTTP_OK);
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

            $response = new Response($recetteSerialized, Response::HTTP_OK);
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

        $dataJson = $this->serializer()->serialize(["data" => $dataDeserialized, "message" => "Success"], "json");
        $response = new Response($dataJson,Response::HTTP_CREATED);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
     * @Route("/recettes/{id}", name="recettes_update", methods={"PUT"})
     * @param Recette $recette
     * @param Request $request
     * @return Response
     * @throws ExceptionInterface
     */

    public function update(Recette $recette, Request $request) : JsonResponse
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

        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"],Response::HTTP_OK);
    }

    /**
     * @Route("/recettes/{id}", name="recettes_partial_update", methods={"PATCH"})
     * @param Recette $recette
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function partialUpdate(Recette $recette, Request $request): JsonResponse
    {
        $data = $request->getContent();
        $dataDeserialized = $this->serializer()->decode($data, "json");

        foreach ($dataDeserialized as $key => $value)
        {
            $methode = "set".ucfirst($key);
            if(method_exists($recette, $methode))
            {
                $recette->$methode($value);
            }
            else
            {
                return new JsonResponse(["error" => "Cette propriété " . $methode . " n'existe pas"],Response::HTTP_NOT_FOUND);
            }
        }

        $this->entityManager->flush();
        $recetteNormalized = $this->serializer()->normalize($recette, "json");

        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"],Response::HTTP_OK);
    }

    /**
     * @Route("/recettes/{id}", name="recettes_delete", methods={"DELETE"})
     * @param Recette $recette
     * @return JsonResponse
     */
    public function delete(Recette $recette) : JsonResponse
    {

        $this->entityManager->remove($recette);
        $this->entityManager->flush();
        return new JsonResponse(["message"=>"Success"],Response::HTTP_NO_CONTENT);

    }
}