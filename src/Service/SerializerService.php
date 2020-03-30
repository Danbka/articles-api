<?php


namespace App\Service;


use Symfony\Component\Serializer\SerializerInterface;

class SerializerService
{
    const FORMAT = 'json';
    
    private $serializer;
    
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    
    public function serialize($data, array $context = [])
    {
        return $this->serializer->serialize($data, self::FORMAT, $context);
    }
    
    public function deserialize($data, string $type, array $context = [])
    {
        return $this->serializer->deserialize($data, $type, self::FORMAT, $context);
    }
}