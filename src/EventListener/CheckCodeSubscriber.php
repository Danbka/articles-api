<?php


namespace App\EventListener;


use App\Service\SerializerService;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CheckCodeSubscriber implements EventSubscriberInterface
{
    private $params;
    
    private $serializer;
    
    public function __construct(ParameterBagInterface $params, SerializerService $serializer)
    {
        $this->params = $params;
        $this->serializer = $serializer;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
    
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        
        if (in_array($request->getMethod(), [Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_DELETE])) {
            
            $authorizationHeader = $request->headers->get('Authorization') ?? "";
            
            $token = substr($authorizationHeader, 7);
            
            if ($token !== $this->params->get('app_secret_key')) {
                $response = new JsonResponse([
                    "error" => "Unauthrorized",
                ], 401);
                
                $response->setContent($this->serializer->serialize([
                    'error' => "Operation not permitted"
                ]));
                
                $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
    
                $response->headers->set("Content-Type", "application/problem+json");
    
                $event->setResponse($response);
            }
        }
    }
}