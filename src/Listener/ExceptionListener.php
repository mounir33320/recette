<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $exceptionEvent) : void
    {

        $throwable = $exceptionEvent->getThrowable();

        if (method_exists($throwable,"getStatusCode") && $throwable->getStatusCode() == 404)
        {
            $jsonResponse = new JsonResponse(["message" => "Cette ressource n'existe pas."]);
            $exceptionEvent->setResponse($jsonResponse);
            return;
        }

        if(method_exists($throwable,"getStatusCode") && $throwable->getStatusCode() == 401)
        {
            $jsonResponse = new JsonResponse(["message" => "Vous devez être authentifié."]);
            $exceptionEvent->setResponse($jsonResponse);
            return;
        }

        if(method_exists($throwable,"getStatusCode") && $throwable->getStatusCode() == 403)
        {
            $jsonResponse = new JsonResponse(["message" => "Vous n'êtes pas autorisé à réaliser cette action."]);
            $exceptionEvent->setResponse($jsonResponse);
            return;
        }

        if(method_exists($throwable,"getStatusCode") && $throwable->getStatusCode() == 405)
        {
            $jsonResponse = new JsonResponse(["message" => "Cette méthode HTTP n'est pas autorisée."]);
            $exceptionEvent->setResponse($jsonResponse);
            return;
        }

        /*$jsonResponse = new JsonResponse(["message" => "Une erreur est survenue au niveau du serveur."]);
        $exceptionEvent->setResponse($jsonResponse);*/


    }
}
