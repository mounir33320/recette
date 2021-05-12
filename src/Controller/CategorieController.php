<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use App\Service\RecetteFilters;
use App\Traits\SerializerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class CategorieController extends AbstractController
{

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;

    use SerializerTrait;

    public function __construct(CategorieRepository $categorieRepository)
    {
        $this->categorieRepository = $categorieRepository;
    }

    /**
     * @OA\Get(
     *     tags={"Categorie"},
     *     path="/categories",
     *     summary="Collection of Categorie",
     *     description="Get a collection of Categorie",
     *     @OA\Parameter(
     *          name="orderBy[nom]",
     *          description="Order by nom",
     *          in="query",
     *          required=false,
     *          @OA\Schema(type="array", @OA\Items(type="string", enum={"asc","desc"}))
     *     ),
     *
     *     @OA\Response(
     *          response="200",
     *          description="Get a collection of Categorie",
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Categorie"))
     *     ),
     *     @OA\Response(
     *          response="404",
     *          ref="#/components/responses/notFound"
     *     ),
     * )
     *
     *
     *
     *
     * @Route("/categories", name="categories_list", methods={"GET"})
     * @return JsonResponse
     */
    public function index(Request $request, RecetteFilters $recetteFilters): JsonResponse
    {
        $context = ["groups" => ["read:recette"]];
        $paramsURL = $request->query->all();
        $keyFilters = ["nom", "id"];
        $orderBy = $recetteFilters->getOrderBy($paramsURL,$keyFilters,["nom" => "asc"]);

        $categories = $this->categorieRepository->findBy($criteria = [], $orderBy);

        $categoriesNormalized = $this->serializer()->normalize($categories, "json", $context);

        return new JsonResponse($categoriesNormalized, Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     tags={"Categorie"},
     *     path="/categories/{id}",
     *     summary="Item of Categorie",
     *     description="Get an item of Categorie",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="ID de la ressource",
     *          required=true,
     *          @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Get an item of Categorie",
     *          @OA\JsonContent(ref="#/components/schemas/Categorie")
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
     * @Route("/categories/{id}", name="categories_item", methods={"GET"})
     * @param Categorie $categorie
     * @return JsonResponse
     */
    public function show(Categorie $categorie): JsonResponse
    {
        $context = ["groups" => ["read:categorie"]];
        $categorieSerialized = $this->serializer()->normalize($categorie, "json", $context);


        return new JsonResponse($categorieSerialized, Response::HTTP_OK);
    }
}
