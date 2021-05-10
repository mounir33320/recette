<?php
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="API Recette", version="1", description="Cette API participative permet de partager des recettes entre utilisateurs.")
 *
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Mon API"
 * )
 *
 * @OA\Response(
 *     response="notFound",
 *     description="This resource not found",
 *     @OA\JsonContent(
 *          @OA\Property(property="message", type="string", example="Cette ressource n'existe pas")
 *     )
 * )
 *
 * @OA\Response(
 *     response="unauthorized",
 *     description="Unauthorized",
 *     @OA\JsonContent(
 *          @OA\Property(property="message", type="string", example="Vous devez être authentifié")
 *     )
 * )
 *
 * @OA\Response(
 *     response="badRequest",
 *     description="Bad request",
 *     @OA\JsonContent(
 *          @OA\Property(property="message", type="string", example="bad request")
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="basic",
 *     type="http",
 *     scheme="basic"
 * )
 *
 */
