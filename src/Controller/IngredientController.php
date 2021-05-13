<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\IngredientRepository;
use App\Repository\RecetteRepository;
use App\Service\ParamsFilters;
use App\Traits\SerializerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


class IngredientController extends AbstractController
{
    /**
     * @var IngredientRepository
     */
    private $ingredientRepository;

    use SerializerTrait;

    public function __construct(IngredientRepository $ingredientRepository)
    {

        $this->ingredientRepository = $ingredientRepository;
    }

    /**
     * @OA\Get(
     *     tags={"Ingredients"},
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

}
