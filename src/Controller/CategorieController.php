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
}
