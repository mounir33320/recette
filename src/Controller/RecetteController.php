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
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Annotations as OA;


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
     * @OA\Get(
     *     tags={"Recette"},
     *     path="/recettes",
     *     summary="Collection of Recette",
     *     description="Get a collection of Recette",
     *     @OA\Parameter(ref="#/components/parameters/orderBy[nom]"),
     *     @OA\Parameter(ref="#/components/parameters/orderBy[cout]"),
     *     @OA\Parameter(ref="#/components/parameters/orderBy[nbPersonne]"),
     *     @OA\Parameter(ref="#/components/parameters/orderBy[dateCreation]"),
     *     @OA\Parameter(ref="#/components/parameters/orderBy[tempsPreparation]"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/query"),
     *     @OA\Response(
     *          response="200",
     *          description="Get a collection of Recette",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Recette"))
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     * )
     *
     * @Route("/recettes", name="recettes_list", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request) : Response
    {
        $currentUser= $this->getUser();

//        if($user != $recette->getUser()){
//            throw new UnauthorizedHttpException(null);
//        }

        $context = ["groups" => ["read:recette"]];
        $paramsURL = $request->query->all();
        $page = (isset($paramsURL["page"]) && $paramsURL["page"] >=0) ? (int)$paramsURL["page"] : 1;
        $limit = (isset($paramsURL["limit"]) && $paramsURL["limit"] >=0) ? (int)$paramsURL["limit"] : 3;
        $query = isset($paramsURL["query"]) && $paramsURL["query"] != "" ? $paramsURL["query"] : null;
        $orderBy = [];

        $criteria = [];

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

        $recettesList = $this->recetteRepository->findAllRecettesPaginated($query,$orderBy,$page,$limit,$currentUser);

        $recettesListSerialized = $this->serializer()->serialize($recettesList, "json", $context);

        $response = new Response($recettesListSerialized, Response::HTTP_OK);
        $response->headers->set("Content-type","application/json");

        return $response;

    }

    /**
     *  @OA\Get(
     *     tags={"Recette"},
     *     path="/recettes/{id}",
     *     summary="Item of Recette",
     *     description="Get an item of Recette",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID de la ressource",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Get an item of Recette",
     *          @OA\JsonContent(ref="#/components/schemas/Recette")
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="401",
     *          ref="#/components/responses/unauthorized"
     *     )
     * )
     * @Route("/recettes/{id}", name="recettes_item", methods={"GET"})
     * @param Recette $recette
     * @return Response
     */
    public function show(Recette $recette) : Response
    {

            $context = ["groups" => ["read:recette"]];
            $recetteSerialized = $this->serializer()->serialize($recette, "json", $context);

            $response = new Response($recetteSerialized, Response::HTTP_OK);
            $response->headers->set("Content-type","application/json");

            return $response;

    }

    /**
     * @OA\Post(
     *     tags={"Recette"},
     *     path="/recettes",
     *     summary="Add an item Recette",
     *     description="Create an item of Recette",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"tempsPreparation", "cout", "nbpersonne", "nom", "public"},
     *              @OA\Property(type="integer", property="tempsPreparation"),
     *              @OA\Property(type="integer", property="cout"),
     *              @OA\Property(type="integer", property="nbPersonne"),
     *              @OA\Property(type="string", property="nom"),
     *              @OA\Property(type="boolean", property="public"),
     *              @OA\Property(type="array", property="categories", @OA\Items(ref="#/components/schemas/CategoryPostRecette")),
     *          )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Create an item of Recette",
     *          @OA\JsonContent(ref="#/components/schemas/Recette")
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="401",
     *          ref="#/components/responses/unauthorized"
     *     ),
     *     @OA\Response(
     *          response="400",
     *          ref="#/components/responses/badRequest"
     *     )
     * )
     *
     *
     *
     * @Route("/recettes", name="recettes_add", methods={"POST"})
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request) : Response
    {
        $user = $this->getUser();
        $data = $request->getContent();
        $dataDecode = $this->serializer()->decode($data,"json", ["groups" => ["post:recette"]]);
        $context = ["groups" => ["read:recette"]];

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
     * @OA\Put(
     *     tags={"Recette"},
     *     path="/recettes/{id}",
     *     summary="Update an item Recette",
     *     description="Update an item of recette",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"tempsPreparation", "cout", "nbpersonne", "nom", "public"},
     *              @OA\Property(type="integer", property="tempsPreparation"),
     *              @OA\Property(type="integer", property="cout"),
     *              @OA\Property(type="integer", property="nbpersonne"),
     *              @OA\Property(type="string", property="nom"),
     *              @OA\Property(type="boolean", property="public"),
     *              @OA\Property(type="array", property="categories", @OA\Items(ref="#/components/schemas/CategoryPostRecette")),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Update an item of recette",
     *          @OA\JsonContent(ref="#/components/schemas/Recette")
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="401",
     *          ref="#/components/responses/unauthorized"
     *     )
     * )
     *
     *
     *
     * @Route("/recettes/{id}", name="recettes_update", methods={"PUT"})
     * @param Recette $recette
     * @param Request $request
     * @return Response
     * @throws ExceptionInterface
     * @IsGranted("ROLE_USER")
     */

    public function update(Recette $recette, Request $request) : JsonResponse
    {
        $user = $this->getUser();

        if($user != $recette->getUser()){
            throw new UnauthorizedHttpException(null);
        }

        $context = ["groups" => ["read:recette"]];
        $data = $request->getContent();
        /**
         * @var Recette $dataDeserialized
         */
        $dataDeserialized = $this->serializer()->decode($data, "json");

        $recette->setCout($dataDeserialized["cout"]);
        $recette->setTempsPreparation($dataDeserialized["tempsPreparation"]);
        $recette->setNbPersonne($dataDeserialized["nbPersonne"]);
        $recette->setNom($dataDeserialized["nom"]);
        $recette->setPublic($dataDeserialized["public"]);

        $recetteCategories = $recette->getCategories();
        foreach ($recetteCategories as $category){
            $recette->removeCategory($category);
        }
        foreach ($dataDeserialized["categories"] as $value){
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


        $this->entityManager->flush();

        $recetteNormalized = $this->serializer()->normalize($recette, "json", $context);

        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"],Response::HTTP_OK);
    }

    /**
     * @OA\Patch (
     *     tags={"Recette"},
     *     summary="Update partially an item Recette",
     *     description="Update partially an item of recette",
     *     path="/recettes/{id}",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *     ),
     *     @OA\RequestBody(
     *          @OA\JsonContent(
     *              @OA\Property(type="integer", property="tempsPreparation"),
     *              @OA\Property(type="integer", property="cout"),
     *              @OA\Property(type="integer", property="nbpersonne"),
     *              @OA\Property(type="string", property="nom"),
     *              @OA\Property(type="boolean", property="public"),
     *              @OA\Property(type="array", property="categories", @OA\Items(ref="#/components/schemas/CategoryPostRecette")),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Update an item of recette",
     *          @OA\JsonContent(ref="#/components/schemas/Recette")
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="401",
     *          ref="#/components/responses/unauthorized"
     *     )
     * )
     *
     *
     *
     *
     * @Route("/recettes/{id}", name="recettes_partial_update", methods={"PATCH"})
     * @param Recette $recette
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     * @IsGranted("ROLE_USER")
     */
    public function partialUpdate(Recette $recette, Request $request): JsonResponse
    {
        $user = $this->getUser();

        if($user != $recette->getUser()){
            throw new UnauthorizedHttpException(null);
        }

        $context = ["groups" => ["read:recette"]];
        $data = $request->getContent();
        $dataDeserialized = $this->serializer()->decode($data, "json");

        foreach ($dataDeserialized as $key => $value)
        {

            $methodeSet = "set".ucfirst($key);

            if(method_exists($recette, $methodeSet))
            {
                $recette->$methodeSet($value);
            }
            //pour gérer catégories
//            else if(!method_exists($recette, $methodeSet)) {
//                switch ($key) {
//                    case "categories" :
//
//                        $recetteCategories = $recette->getCategories();
//                        foreach ($recetteCategories as $category) {
//                            $recette->removeCategory($category);
//                        }
//
//                        foreach ($value as $category) {
//
//                            /**
//                             * @var Categorie $checkCategorieInDb
//                             */
//                            $checkCategorieInDb = $this->entityManager->find(Categorie::class, $category);
//                            if ($checkCategorieInDb != null) {
//                                $recette->addCategory($checkCategorieInDb);
//                            }
//
//                            break;
//
//                        }
//                }
//            }
            else
            {
                return new JsonResponse(["error" => "Cette propriété n'existe pas"],Response::HTTP_NOT_FOUND);
            }
        }

        $this->entityManager->flush();
        $recetteNormalized = $this->serializer()->normalize($recette, "json", $context);

        return new JsonResponse(["data"=>$recetteNormalized,"message"=>"Success"],Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     tags={"Recette"},
     *     path="/recettes/{id}",
     *     summary="Delete an item Recette",
     *     description="Delete an item of Recette",
     *     @OA\Parameter(
     *          name="id",
     *          required=true,
     *          in="path"
     *     ),
     *     @OA\Response(
     *          response="204",
     *          description="Success - Item is deleted",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Success")
     *          )
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     *     @OA\Response(
     *          response="401",
     *          ref="#/components/responses/unauthorized"
     *     )
     *
     * )
     *
     *
     *
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
