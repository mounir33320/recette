<?php


namespace App\Controller;


use App\Entity\Categorie;
use App\Entity\Recette;
use App\Repository\CategorieRepository;
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
    private $categorieRepository;

    use SerializerTrait;


    public function __construct(RecetteRepository $recetteRepository,
                                EntityManagerInterface $entityManager,
                                CategorieRepository $categorieRepository)
    {
        $this->recetteRepository = $recetteRepository;
        $this->entityManager = $entityManager;
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @Route("/recettes", name="recettes_list", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        $currentUser= $this->getUser();

        $paramsURL = $request->query->all();
        $page = (isset($paramsURL["page"]) && $paramsURL["page"] >=0) ? (int)$paramsURL["page"] : 1;
        $limit = (isset($paramsURL["limit"]) && $paramsURL["limit"] >=0) ? (int)$paramsURL["limit"] : 3;
        $orderBy = [] ;

        $criteria = $currentUser == null ? ["public" => true] : [];

        $keyFilters = ["nom", "cout", "nbPersonne", "dateCreation", "tempsPreparation"];

        // On vérifie que les clés et les valeurs des paramètres orderBy sont valides
        if(isset($paramsURL["orderBy"]))
        {
            foreach ($paramsURL["orderBy"] as $key => $value)
            {
                if(in_array($key, $keyFilters) && in_array(strtoupper($value), ["ASC","DESC"]))
                {
                    $orderBy[$key] = $value;
                }
            }
        }

        if (empty($orderBy)) {
            $orderBy = ["nom" => "asc"];
        }

        $recettesList = $this->recetteRepository->findAllRecettesPaginated($criteria,$orderBy,$page,$limit);

        dd($recettesList);
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
        $user = $this->getUser();
        $data = $request->getContent();
        $dataDecode = $this->serializer()->decode($data,"json");
        $context = ["circular_reference_handler" => function($object){
            if($object instanceof Categorie || $object instanceof Recette){
                return $object->getNom();
            }
            return $object->getUsername();
        }];

        $recette = new Recette();
        $recette->setNom($dataDecode["nom"])
                ->setNbPersonne($dataDecode["nbPersonne"])
                ->setTempsPreparation($dataDecode["tempsPreparation"])
                ->setPublic($dataDecode["public"])
                ->setCout($dataDecode["cout"])
                ->setUser($user);


        foreach ($dataDecode["categories"] as $value){
            //methode 1
            //$categories = $this->categorieRepository->find($value);

            //methode 2
            /**
             * @var Categorie $checkCategorieInDb
             */
            $checkCategorieInDb = $this->entityManager->find(Categorie::class, $value);
            if($checkCategorieInDb!= null){
                $recette->addCategory($checkCategorieInDb);
            }
        }

        //dd($recette);

        $this->entityManager->persist($recette);
        $this->entityManager->flush();

        $dataJson = $this->serializer()->serialize(["data" => $recette, "message" => "Success"], "json", $context);
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