<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\BitsoException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class BitsoPrivateApi
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $secret;

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

    public function __construct(string $key='', string $secret = '', ClientInterface $client)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->client = $client;
    }


    /**
     * @throws BitsoException
     */
    public function request(string $url, string $method = 'GET', array $params = []): array
    {
        if (!in_array($method, $this::ALLOWED_METHODS)) {
            throw new MethodNotAllowedException($this::ALLOWED_METHODS);
        }

        $payload = '';

        if (!empty($params) && $method == 'POST') {
            $payload = json_encode($params);
            $headers['Content-Type'] = 'application/json';
        }

        $authHeader = $this->createAuthenticationHeader($url,$method, $payload, $params);
        $headers['Authorization'] = $authHeader;

        try {
            $response = $this->client->request($method, $url, ['query' => $params, 'headers' => $headers, 'body' => $payload, 'debug' => false]);
        } catch (GuzzleException $exception) {
            throw new BitsoException($exception->getMessage());
        }

        $responseBody = json_decode((string)$response->getBody(), true);

        return $responseBody['payload'];
    }

    private function getNonceTimestamp(): float
    {
        $time = round(microtime(true)*1000);
        return $time;
    }

    private function createAuthenticationHeader(string $url, string $method, string $payload, array $params): string {
        $path = parse_url($this->client->getConfig('base_uri').$url)['path'];

        if ($method == 'GET' && !empty($params)) {
            $path .= '?' . http_build_query($params);
        }

        $nonce = $this->getNonceTimestamp();
        $message = sprintf('%s%s%s%s', $nonce , $method , $path , $payload);
        $signature = hash_hmac('sha256', $message, $this->secret);

        return sprintf('Bitso %s:%s:%s', $this->key, $nonce, $signature);
    }

    public function getAccountStatus(array $params = []): array{
        try {
            return $this->request('account_status', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getFees(array $params = []): array{
        try {
           return $this->request('fees', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

    public function getLedger(array $params = []): array{
        try {
            return $this->request('ledger', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception){
            return ['error' => $exception->getMessage()];
        }
    }

}