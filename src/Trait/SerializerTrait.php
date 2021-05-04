<?php
namespace App\Trait;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

trait SerializerTrait
{
    public function serializer(): Serializer
    {
        $jsonEncoder = new JsonEncoder();
        $dateTimeNormalizer = new DateTimeNormalizer();
        $objectNormalizer = new ObjectNormalizer();

        return new Serializer([$dateTimeNormalizer,$objectNormalizer], [$jsonEncoder]);

    }
}