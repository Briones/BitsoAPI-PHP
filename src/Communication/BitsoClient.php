<?php
declare(strict_types=1);

namespace App\Communication;

use Symfony\Component\HttpFoundation\RequestStack;

class BitsoClient extends \GuzzleHttp\Client
{

    public function __construct($apiUrl, array $config = [])
    {
        $handlerStack = $config['handler'] ?? \GuzzleHttp\HandlerStack::create();

        $config['handler'] = $handlerStack;
        $config['base_uri'] = $apiUrl;

        parent::__construct($config);
    }

    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }


}