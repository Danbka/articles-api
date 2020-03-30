<?php


namespace App\EventListener;


use App\Exception\ApiException;
use App\Service\SerializerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    private $serializer;
    
    public function __construct(SerializerService $serializer)
    {
        $this->serializer = $serializer;
    }
    
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException'
        ];
    }
    
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if (!$exception instanceof ApiException) {
            return;
        }
        
        $response = new JsonResponse([
            "errors" => $exception->getErrors()
        ], $exception->getStatusCode());
        
        $response->setContent($this->serializer->serialize([
            'error' => $exception->getMessage()
        ]));
        
        $response->setStatusCode($exception->getStatusCode());
        
        $response->headers->set("Content-Type", "application/problem+json");
        
        $event->setResponse($response);
    }
}