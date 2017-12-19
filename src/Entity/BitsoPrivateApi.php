<?php
declare(strict_types=1);

namespace App\Entity;

use App\Exception\BitsoException;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
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

    public function __construct(string $key = '', string $secret = '', ClientInterface $client)
    {
        $this->key = $key;
        $this->secret = $secret;
        $this->client = $client;
    }


    /**
     * @throws BitsoException
     */
    public function request(string $path, string $method = 'GET', array $params = []): string
    {
        if (!in_array($method, $this::ALLOWED_METHODS)) {
            throw new MethodNotAllowedException($this::ALLOWED_METHODS);
        }

        $payload = '';

        if (!empty($params) && $method == 'POST') {
            $payload = json_encode($params);
            $headers['Content-Type'] = 'application/json';
            $params = [];
        }

        $apiPath = $this->parseApiPath($method, $path, $params);
        $authHeader = $this->createAuthenticationHeader($apiPath, $method, $payload);
        $headers['Authorization'] = $authHeader;

        try {
            $response = $this->client->request($method, $path, ['query' => $params, 'headers' => $headers, 'body' => $payload]);
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $jsonBody = (string)$response->getBody();
            throw new BitsoException($jsonBody);
        }

        $responseBody = json_decode((string)$response->getBody(), true);

        return json_encode($responseBody['payload']);
    }

    private function getNonceTimestamp(): float
    {
        $time = round(microtime(true) * 1000);
        return $time;
    }

    private function parseApiPath(string $method, string &$path, array &$params)
    {
        $parsedUrl = parse_url($this->client->getConfig('base_uri') . $path);
        $apiPath = $parsedUrl['path'];

        if (isset($params['extra'])) {
            $paramsExtra = str_replace(',', '-', $params['extra']);
            unset($params['extra']);
            $path .= '/' . $paramsExtra;
            $apiPath .= '/' . $paramsExtra;
        }

        if ($method == 'GET' && !empty($params)) {
            $apiPath .= '?' . http_build_query($params);
        }

        return $apiPath;
    }

    private function createAuthenticationHeader(string $apiPath, string $method, string $payload): string
    {
        $nonce = $this->getNonceTimestamp();
        $message = sprintf('%s%s%s%s', $nonce, $method, $apiPath, $payload);
        $signature = hash_hmac('sha256', $message, $this->secret);

        return sprintf('Bitso %s:%s:%s', $this->key, $nonce, $signature);
    }

    public function getAccountStatus(array $params = []): string
    {
        try {
            return $this->request('account_status', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getBalance(array $params = []): string
    {
        try {
            return $this->request('balance', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getFees(array $params = []): string
    {
        try {
            return $this->request('fees', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getLedger(array $params = []): string
    {
        try {
            return $this->request('ledger', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getWithdrawals(array $params = []): string
    {
        if (isset($params['wids'])) {
            $params['extra'] = $params['wids'];
            unset($params['wids']);
        }

        try {
            return $this->request('withdrawals', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function getFundings(array $params = []): string
    {
        if (isset($params['fids'])) {
            $params['extra'] = $params['fids'];
            unset($params['fids']);
        }
        try {
            return $this->request('fundings', 'GET', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

    public function placeOrder(array $params = []): string
    {
        try {
            return $this->request('orders', 'POST', $params);
        } catch (BitsoException | MethodNotAllowedException $exception) {
            return $exception->getMessage();
        }
    }

}