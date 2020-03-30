<?php


namespace App\Controller\Api;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends AbstractController
{
    protected function createApiResponse($data, $statusCode = 200)
    {
        return new Response($data, $statusCode, [
            'Content-Type' => 'application/json'
        ]);
    }
}