<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Ingredient;
use App\Entity\Recette;
use App\Repository\CategorieRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Service\ParamsFilters;
use App\Traits\SerializerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Exception\ExceptionInterface;


class IngredientController extends AbstractController
{
    /**
     * @var IngredientRepository
     */
    private $ingredientRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    use SerializerTrait;

    public function __construct(IngredientRepository $ingredientRepository,EntityManagerInterface $entityManager)
    {

        $this->ingredientRepository = $ingredientRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @OA\Get(
     *     tags={"Ingredient"},
     *     path="/ingredients",
     *     summary="Collection of Ingredients",
     *     description="Get a collection of Ingredients",
     *     @OA\Parameter(ref="#/components/parameters/orderBy[nom]"),
     *     @OA\Parameter(ref="#/components/parameters/limit"),
     *     @OA\Parameter(ref="#/components/parameters/query"),
     *     @OA\Response(
     *          response="200",
     *          description="Get a collection of Ingredients",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Recette"))
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     * )
     *
     * @Route("/ingredients", name="ingredients_list", methods={"GET"})
     * @param Request $request
     * @param ParamsFilters $paramsFilters
     * @return Response
     */
    public function index(Request $request, ParamsFilters $paramsFilters) : Response
    {

        $context = ["groups" => ["read:ingredient"]];
        $paramsURL = $request->query->all();

        $keyFilters = ["nom"];

        $orderBy = $paramsFilters->getOrderBy($paramsURL,$keyFilters,["nom" => "asc"]);
        $page = $paramsFilters->getPage($paramsURL);
        $limit = $paramsFilters->getLimit($paramsURL);
        $query = $paramsFilters->getQuery($paramsURL);


        $ingredientsList = $this->ingredientRepository->findAllIngredientsPaginated($query,$orderBy,$page,$limit);

        $ingredientListSerialized = $this->serializer()->serialize($ingredientsList, "json", $context);

        $response = new Response($ingredientListSerialized, Response::HTTP_OK);
        $response->headers->set("Content-type","application/json");

        return $response;
    }


    /**
    * @OA\Post(
    *     tags={"Ingredient"},
    *     path="/ingredients",
    *     summary="Add an item Ingredient",
    *     description="Create an item of Ingredient",
    *     @OA\RequestBody(
    *          required=true,
    *          @OA\JsonContent(
    *              @OA\Property(type="string", property="nom"),
    *              @OA\Property(type="array", property="recette", @OA\Items(ref="#/components/schemas/Ingredient")),
    *          )
    *     ),
    *     @OA\Response(
    *          response="201",
    *          description="Create an item of Ingredient",
    *          @OA\JsonContent(ref="#/components/schemas/Ingredient")
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
    * @Route("/ingredients", name="ingredients_add", methods={"POST"})
    * @param Request $request
    * @return Response
    * @IsGranted("ROLE_ADMIN")
    */

    public function add(Request $request) : Response
    {

        $data = $request->getContent();
        //["groups" => ["post:recette"]]
        $dataDecode = $this->serializer()->decode($data,"json");
        //$context = ["groups" => ["read:ingredient"]];

        $ingredient = new Ingredient();
        $ingredient->setNom($dataDecode["nom"]);

        $this->entityManager->persist($ingredient);
        $this->entityManager->flush();

        $dataJson = $this->serializer()->serialize(["data" => $ingredient, "message" => "L'ingredient à bien été ajouté."], "json");
        $response = new Response($dataJson,Response::HTTP_CREATED);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
    * @OA\Put(
    *     tags={"Ingredient"},
    *     path="/ingredients/{id}",
    *     summary="Update an Ingredient",
    *     description="Update an Ingredient",
    *     @OA\Parameter(
    *          name="id",
    *          in="path",
    *          required=true
    *     ),
    *     @OA\Response(
    *          response="200",
    *          description="Update an item of Ingredient",
    *          @OA\JsonContent(ref="#/components/schemas/Ingredient")
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
    * @Route("/ingredients/{id}", name="ingredients_update", methods={"PUT"})
    * @param Ingredient $ingredient
    * @param Request $request
    * @return JsonResponse
    * @IsGranted("ROLE_ADMIN")
    */

    public function update(Ingredient $ingredient, Request $request) : JsonResponse
    {

        $data = $request->getContent();
        $dataDeserialized = $this->serializer()->decode($data, "json");

        $ingredient->setNom($dataDeserialized["nom"]);

        $this->entityManager->flush();

        $ingredientNormalized = $this->serializer()->normalize($ingredient, "json");

        return new JsonResponse(["data"=>$ingredientNormalized,"message"=>"L'ingredient à bien été modifié."],Response::HTTP_OK);
    }

    /**
    * @OA\Delete(
    *     tags={"Ingredient"},
    *     path="/ingredients/{id}",
    *     summary="Delete an Ingredient",
    *     description="Delete an Ingredient",
    *     @OA\Parameter(
    *          name="id",
    *          required=true,
    *          in="path"
    *     ),
    *     @OA\Response(
    *          response="204",
    *          description="Success - Ingredient is deleted",
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
    * @Route("/ingredients/{id}", name="ingredients_delete", methods={"DELETE"})
    * @param Ingredient $ingredient
    * @return JsonResponse
    * @IsGranted("ROLE_ADMIN")
    */

    public function delete(Ingredient $ingredient) : JsonResponse
    {
        $this->entityManager->remove($ingredient);
        $this->entityManager->flush();

        return new JsonResponse(["message"=>"http_no_content, no message"],Response::HTTP_NO_CONTENT);
    }

}
