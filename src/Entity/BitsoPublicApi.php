<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\BitsoException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class BitsoPublicApi
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var ClientInterface
     */
    protected $client;

    const ALLOWED_METHODS = [
        'GET',
        'DELETE',
        'POST',
    ];

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }
    
    /**
     * @throws BitsoException
     */
    public function request(string $url, array $params, $method = 'GET'): array
    {
        try {
            $response = $this->client->request($method, $url, ['query' => $params]);
        } catch (GuzzleException $exception) {
            throw new BitsoException($exception->getMessage());
        }

        $responseBody = json_decode((string)$response->getBody(), true);

        return $responseBody['payload'];
    }

    public function getTicker(array $params): array{
        try {
           return $this->request('ticker', $params);
        } catch (BitsoException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getAvailableBooks(array $params): array{
        try {
            return $this->request('available_books', $params);
        } catch (BitsoException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getOrderBook(array $params): array{
        try {
            return $this->request('order_book', $params);
        } catch (BitsoException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getTrades(array $params): array{
        try {
            return $this->request('trades', $params);
        } catch (BitsoException $exception){
            return ['error' => $exception->getMessage()];
        }
    }
}