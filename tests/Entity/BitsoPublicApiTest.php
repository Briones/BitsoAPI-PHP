<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\BitsoPublicApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class BitsoPublicApiTest extends TestCase
{
    public function testGetsTicker()
    {
        $mockedResponse = [
            'success' => true,
            'payload' =>
                [
                    'high' => '394999.00',
                    'last' => '382000.00',
                    'created_at' => '2017-12-18T05=>37=>05+00=>00',
                    'book' => 'btc_mxn',
                    'volume' => '231.80326304',
                    'vwap' => '379069.51333992',
                    'low' => '363360.00',
                    'ask' => '381980.61',
                    'bid' => '380400.07'
                ]
        ];

        $bitsoPublicApi = new BitsoPublicApi($this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPublicApi->getTicker(['book' => 'btc_mxn']);
        $this->assertEquals($mockedResponse['payload'], $actualResponse);
    }

    public function testGetsAvailableBooks()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [
                'book' => 'btc_mxn',
                'minimum_amount' => '.003',
                'maximum_amount' => '1000.00',
                'minimum_price' => '100.00',
                'maximum_price' => '1000000.00',
                'minimum_value' => '25.00',
                'maximum_value' => '1000000.00'
            ],
            [
                'book' => 'eth_mxn',
                'minimum_amount' => '.003',
                'maximum_amount' => '1000.00',
                'minimum_price' => '100.0',
                'maximum_price' => '1000000.0',
                'minimum_value' => '25.0',
                'maximum_value' => '1000000.0'
            ]
        ];

        $bitsoPublicApi = new BitsoPublicApi($this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPublicApi->getAvailableBooks(["book" => "btc_mxn", "aggregate" => "true"]);
        $this->assertEquals($mockedResponse['payload'], $actualResponse);
    }

    public function testGetsOrderBook()
    {
        $mockedResponse = [
           'success' => true,
           'payload' => [
               'asks' => [
                    [
                       'book' =>'btc_mxn',
                       'price' =>'5632.24',
                       'amount' =>'1.34491802'
                    ],
                    [
                       'book' =>'btc_mxn',
                       'price' =>'5633.44',
                       'amount' =>'0.4259'
                    ],
                    [
                       'book' =>'btc_mxn',
                       'price' =>'5642.14',
                       'amount' =>'1.21642'
                    ]
                ],
               'bids' => [
                    [
                       'book' =>'btc_mxn',
                       'price' =>'6123.55',
                       'amount' =>'1.12560000'
                    ],
                    [
                       'book' =>'btc_mxn',
                       'price' =>'6121.55',
                       'amount' =>'2.23976'
                    ]
                ],
               'updated_at' =>'2016-04-08T17 =>52 =>31.000+00 =>00',
               'sequence' =>'27214'
            ]
        ];

        $bitsoPublicApi = new BitsoPublicApi($this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPublicApi->getOrderBook(['book'=>'btc_mxn','aggregate'=> 'true']);
        $this->assertEquals($mockedResponse['payload'], $actualResponse);
    }

    public function testGetsPublicTrades()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [
                [
                    'book' => 'btc_mxn',
                    'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                    'amount' => '0.02000000',
                    'maker_side' => 'buy',
                    'price' => '5545.01',
                    'tid' => 55845,
                ],
                [
                    'book' => 'btc_mxn',
                    'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                    'amount' => '0.33723939',
                    'maker_side' => 'sell',
                    'price' => '5633.98',
                    'tid' => 55844,
                ],
            ],
        ];

        $bitsoPublicApi = new BitsoPublicApi($this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPublicApi->getTrades(['book'=>'btc_mxn']);
        $this->assertEquals($mockedResponse['payload'], $actualResponse);
    }

    public function createMockClient(string $body, int $statusCode = 200): Client
    {
        $mock = new MockHandler([new Response($statusCode, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
