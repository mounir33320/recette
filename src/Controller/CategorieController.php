<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Recette;
use App\Repository\CategorieRepository;
use App\Service\RecetteFilters;
use App\Traits\SerializerTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CategorieController extends AbstractController
{

    /**
     * @var CategorieRepository
     */
    private $categorieRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    use SerializerTrait;

    public function __construct(CategorieRepository $categorieRepository, EntityManagerInterface $entityManager)
    {
        $this->categorieRepository = $categorieRepository;
        $this->entityManager = $entityManager;
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
     *          @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/CategorieReadRecette"))
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
        $context = ["groups" => ["read:categorie"]];
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

    /**
     * @OA\Post(
     *     tags={"Categorie"},
     *     path="/categories",
     *     summary="Add an item Categorie",
     *     description="Create an item of Categorie",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(type="string", property="nom"),
     *              @OA\Property(type="array", property="recette", @OA\Items(ref="#/components/schemas/Recette")),
     *          )
     *     ),
     *     @OA\Response(
     *          response="201",
     *          description="Create an item of Categorie",
     *          @OA\JsonContent(ref="#/components/schemas/Categorie")
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
     * @Route("/categories", name="categorie_add", methods={"POST"})
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */

    public function add(Request $request):Response
    {
        //$context = ["groups" => ["read:categorie"]];
        //$recette = $this->getDoctrine()->getRepository(Recette::class)->find(2);
        $data = $request->getContent();
        $dataDecode = $this->serializer()->decode($data,"json");

        $categorie = new Categorie();
        $categorie->setNom($dataDecode["nom"]);
                    //->addRecette($recette);

        $this->entityManager->persist($categorie);
        $this->entityManager->flush();

        $dataJson = $this->serializer()->serialize(["data" => $categorie, "message" => "Success"], "json");
        $response = new Response($dataJson,Response::HTTP_CREATED);
        $response->headers->set("Content-type","application/json");

        return $response;
    }

    /**
     * @OA\Put(
     *     tags={"Categorie"},
     *     path="/categories/{id}",
     *     summary="Update an item Categorie",
     *     description="Update an item of Categorie",
     *     @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(type="string", property="nom"),
     *              @OA\Property(type="array", property="recettes", @OA\Items(ref="#/components/schemas/Recette")),
     *          )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Update an item of categorie",
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
     *
     *
     *
     * @Route("/categories/{id}", name="categorie_update", methods={"PUT"})
     * @param Categorie $categorie
     * @param Request $request
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */
    public function update(Categorie $categorie, Request $request) :JsonResponse
    {
        $data = $request->getContent();
        $dataDeserialized = $this->serializer()->decode($data, "json");

        $categorie->setNom($dataDeserialized["nom"]);

        $this->entityManager->flush();

        $categorieNormalized = $this->serializer()->normalize($categorie, "json");

        return new JsonResponse(["data"=>$categorieNormalized,"message"=>"Success"],Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     tags={"Categorie"},
     *     path="/categories/{id}",
     *     summary="Delete an item Categorie",
     *     description="Delete an item of Categorie",
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
     *
     * @Route("/categories/{id}", name="delete_categorie", methods={"DELETE"})
     * @param Categorie $categorie
     * @return JsonResponse
     * @IsGranted("ROLE_ADMIN")
     */

    public function delete(Categorie $categorie){

        $this->entityManager->remove($categorie);
        $this->entityManager->flush();

        return new JsonResponse(["message"=>"Success"],Response::HTTP_NO_CONTENT);
    }
}
