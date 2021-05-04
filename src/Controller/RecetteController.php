<?php


namespace App\Controller;


use App\Entity\Recette;
use App\Repository\RecetteRepository;
use Cassandra\Date;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class RecetteController extends AbstractController
{
    /**
     * @var RecetteRepository
     */
    private $recetteRepository;
    /**
     * @var JsonEncoder
     */
    private $jsonEncoder;
    /**
     * @var DateTimeNormalizer
     */
    private $dateTimeNormalizer;
    /**
     * @var ObjectNormalizer
     */
    private $objectNormalizer;
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(RecetteRepository $recetteRepository)
    {
        $this->recetteRepository = $recetteRepository;

        $this->jsonEncoder = new JsonEncoder();
        $this->dateTimeNormalizer = new DateTimeNormalizer();
        $this->objectNormalizer = new ObjectNormalizer();

        $this->serializer = new Serializer([$this->dateTimeNormalizer,$this->objectNormalizer], [$this->jsonEncoder]);
    }

    /**
     * @Route("/recettes", name="recettes_list", methods={"GET"})
     * @return Response
     */
    public function index() : Response
    {
        $recettesList = $this->recetteRepository->findAll();

        $recettesListSerialized = $this->serializer->serialize($recettesList, "json");

        $response = new Response($recettesListSerialized);
        $response->headers->set("Content-type","json");

        return $response;
    }

    /**
     * @Route("/recettes/{id}", name="recettes_item", methods={"GET"})
     * @param Recette $recette
     * @return Response
     */
    public function show(Recette $recette) : Response
    {
        $recetteSerialized = $this->serializer->serialize($recette, "json");

        $response = new Response($recetteSerialized);
        $response->headers->set("Content-type","json");

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
        $dataDeserialized = $this->serializer->deserialize($data,Recette::class,"json");

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($dataDeserialized);
        $entityManager->flush();

        $dataArray = $this->serializer->normalize($dataDeserialized);

        return new JsonResponse(["data"=>$dataArray,"message"=>"Success"]);
    }
}