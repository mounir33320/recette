<?php

namespace App\Serializer\Normalizer;

use App\Entity\Category;
use App\Entity\Recette;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TestNormalizer extends ObjectNormalizer implements NormalizerInterface, DenormalizerInterface, ContextAwareNormalizerInterface, ContextAwareDenormalizerInterface
{
    private $normalizer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(ObjectNormalizer $normalizer, EntityManagerInterface $entityManager)
    {
        $this->normalizer = $normalizer;
        $this->entityManager = $entityManager;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize($object, $format, $context);

        // Here: add, edit, or delete some data

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof \App\Entity\Test;
    }

    public function denormalize($data, $type = null, $format = null, array $context = []): array
    {

        $test = $this->entityManager->find(Category::class,$data["id"]);
        if($test != null){
            return $test;
            dd("caca");
        }
        throw new \Exception("aladla");
    }

    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {

        return str_contains(Category::class, $type);
    }
}
