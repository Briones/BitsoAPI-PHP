<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\BitsoException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;

class BitsoPublicApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @throws BitsoException
     */
    public function request(string $url, array $params, $method = 'GET'): string
    {
        try {
            $response = $this->client->request($method, $url, ['query' => $params]);
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $jsonBody = (string)$response->getBody();
            throw new BitsoException($jsonBody);
        }

        $responseBody = json_decode((string)$response->getBody(), true);

        return json_encode($responseBody['payload']);
    }

    public function getTicker(array $params): string
    {
        try {
            return $this->request('ticker', $params);
        } catch (BitsoException $exception) {
            return $exception->getMessage();
        }
    }

    public function getAvailableBooks(array $params): string
    {
        try {
            return $this->request('available_books', $params);
        } catch (BitsoException $exception) {
            return $exception->getMessage();
        }
    }

    public function getOrderBook(array $params): string
    {
        try {
            return $this->request('order_book', $params);
        } catch (BitsoException $exception) {
            return $exception->getMessage();
        }
    }

    public function getTrades(array $params): string
    {
        try {
            return $this->request('trades', $params);
        } catch (BitsoException $exception) {
            return $exception->getMessage();
        }
    }
}