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
        }
    }
}