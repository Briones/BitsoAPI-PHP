<?php
declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\BitsoPrivateApi;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class BitsoPrivateApiTest extends TestCase
{
    const KEY = 'AnyKey';
    const SECRET = 'AnySecret';

    public function testGetsAccountStatus()
    {

        $mockedResponse = [
            'success' => true,
            'payload' =>
                [
                    'client_id' => '1234',
                    'first_name' => 'Claude',
                    'last_name' => 'Shannon',
                    'status' => 'active',
                    'daily_limit' => '5300.00',
                    'monthly_limit' => '32000.00',
                    'daily_remaining' => '3300.00',
                    'monthly_remaining' => '31000.00',
                    'cellphone_number' => 'verified',
                    'cellphone_number_stored' => '+525555555555',
                    'email_stored' => 'shannon@maxentro.py',
                    'official_id' => 'submitted',
                    'proof_of_residency' => 'submitted',
                    'signed_contract' => 'unsubmitted',
                    'origin_of_funds' => 'unsubmitted'
                ]
        ];

        $bitsoPrivateApi = new BitsoPrivateApi($this::KEY, $this::SECRET, $this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPrivateApi->getAccountStatus();
        $this->assertEquals($mockedResponse['payload'], json_decode($actualResponse, true));
    }

    public function testGetsBalance()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [
                'balances' => [
                    [
                        'currency' => 'mxn',
                        'total' => '100.1234',
                        'locked' => '25.1234',
                        'available' => '75.0000'
                    ],
                    [
                        'currency' => 'btc',
                        'total' => '4.12345678',
                        'locked' => '25.00000000',
                        'available' => '75.12345678'
                    ],
                    [
                        'currency' => 'eth',
                        'total' => '50.1234',
                        'locked' => '40.1234',
                        'available' => '10.0000'
                    ]
                ],
            ]
        ];

        $bitsoPrivateApi = new BitsoPrivateApi($this::KEY, $this::SECRET, $this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPrivateApi->getBalance();
        $this->assertEquals($mockedResponse['payload'], json_decode($actualResponse, true));
    }


    public function testGetsFees()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [
                'fees' => [
                    [
                        'book' => 'btc_mxn',
                        'fee_decimal' => '0.0001',
                        'fee_percent' => '0.01'
                    ],
                    [
                        'book' => 'eth_mxn',
                        'fee_decimal' => '0.001',
                        'fee_percent' => '0.1'
                    ]
                ],
                'withdrawal_fees' => [
                    'btc' => '0.001',
                    'eth' => '0.0025'
                ]
            ]
        ];

        $bitsoPrivateApi = new BitsoPrivateApi($this::KEY, $this::SECRET, $this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPrivateApi->getFees();
        $this->assertEquals($mockedResponse['payload'], json_decode($actualResponse, true));
    }

    public function testGetsLedger()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [[
                'eid' => 'c4ca4238a0b923820dcc509a6f75849b',
                'operation' => 'trade',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'btc',
                    'amount' => '-0.25232073'
                ], [
                    'currency' => 'mxn',
                    'amount' => '1013.540958479115'
                ]],
                'details' => [
                    'tid' => 51756,
                    'oid' => 'wri0yg8miihs80ngk'
                ]
            ], [
                'eid' => '6512bd43d9caa6e02c990b0a82652dca',
                'operation' => 'fee',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'mxn',
                    'amount' => '-10.237787459385'
                ]],
                'details' => [
                    'tid' => 51756,
                    'oid' => '19vaqiv72drbphig'
                ]
            ], [
                'operation' => 'trade',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'eth',
                    'amount' => '4.86859395'
                ], [
                    'currency' => 'mxn',
                    'amount' => '-626.77'
                ]],
                'details' => [
                    'tid' => 51757,
                    'oid' => '19vaqiv72drbphig'
                ]
            ], [
                'eid' => '698d51a19d8a121ce581499d7b701668',
                'operation' => 'fee',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'eth',
                    'amount' => '0.04917771'
                ]],
                'details' => [
                    'tid' => 51757,
                    'oid' => '19vaqiv72drbphig'
                ]
            ], [
                'eid' => 'b59c67bf196a4758191e42f76670ceba',
                'operation' => 'funding',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'btc',
                    'amount' => '0.48650929'
                ]],
                'details' => [
                    'fid' => 'fc23c28a23905d8614499816c3ade455',
                    'method' => 'btc',
                    'funding_address' => '18MsnATiNiKLqUHDTRKjurwMg7inCrdNEp'
                ]
            ], [
                'eid' => 'b0baee9d279d34fa1dfd71aadb908c3f',
                'operation' => 'funding',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'mxn',
                    'amount' => '300.15'
                ]],
                'details' => [
                    'fid' => '3ef729ccf0cc56079ca546d58083dc12',
                    'method' => 'sp'
                ]

            ], [
                'eid' => '96e79218965eb72c92a549dd5a330112',
                'operation' => 'withdrawal',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'balance_updates' => [[
                    'currency' => 'mxn',
                    'amount' => '-200.15'
                ]],
                'details' => [
                    'wid' => 'c5b8d7f0768ee91d3b33bee648318688',
                    'method' => 'sp'
                ]
            ]]
        ];

        $bitsoPrivateApi = new BitsoPrivateApi($this::KEY, $this::SECRET, $this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPrivateApi->getLedger(['limit' => '15']);
        $this->assertEquals($mockedResponse['payload'], json_decode($actualResponse, true));
    }

    public function testGetsWithdrawals()
    {
        $mockedResponse = [
            'success' => true,
            'payload' => [[
                'wid' => 'c5b8d7f0768ee91d3b33bee648318688',
                'status' => 'pending',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'currency' => 'btc',
                'method' => 'Bitcoin',
                'amount' => '0.48650929',
                'details' => [
                    'withdrawal_address' => '18MsnATiNiKLqUHDTRKjurwMg7inCrdNEp',
                    'tx_hash' => 'd4f28394693e9fb5fffcaf730c11f32d1922e5837f76ca82189d3bfe30ded433'
                ]
            ], [
                'wid' => 'p4u8d7f0768ee91d3b33bee6483132i8',
                'status' => 'complete',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'currency' => 'mxn',
                'method' => 'sp',
                'amount' => '2612.70',
                'details' => [
                    'beneficiary_name' => 'BERTRAND RUSSELL',
                    'beneficiary_bank' => 'BANAMEX',
                    'beneficiary_clabe' => '002320700708015728',
                    'numeric_reference' => '99548',
                    'concepto' => 'Por los 🌮 del viernes',
                    'clave_rastreo' => 'BNET01001604080002076841',
                    'cep' => [
                        'return' => [
                            'cda' => [
                                'cadenaOriginal' => '||1|13062016|13062016|172053|40002|STP|Bitso - BERTRAND RUSSELL|40|646180115400000002|BIT140123U70|BANAMEX|BERTRAND RUSSELL|40|002320700708015728|ND|-|0.00|2612.70|00001000000401205824||',
                                'conceptoPago' => '-',
                                'cuentaBeneficiario' => '002320700708015728',
                                'cuentaOrdenante' => '646180115400000002',
                                'fechaCaptura' => '20160613',
                                'fechaOperacion' => '20160613',
                                'hora' => '17 =>08 =>42',
                                'iva' => '0.00',
                                'monto' => '2612.70',
                                'nombreBeneficiario' => 'BERTRAND RUSSELL',
                                'nombreInstBeneficiaria' => 'BANAMEX',
                                'nombreInstOrdenante' => 'STP',
                                'nombreOrdenante' => 'Bitso - Russell',
                                'referenciaNumerica' => '99548',
                                'rfcCurpBeneficiario' => 'ND',
                                'rfcCurpOrdenante' => 'BIT140123U70',
                                'selloDigital' => 'cd7yUrnmUQ7CG6M+LX7WOZeizOpkTyMlEAunJaP2j5MAaNPZxy+vAJtgiVL73i1LNSrwK10eBb66Rh4\/RxU6AT2S03chQ\/BS1beknH5xPpGQg+wEXeANtnF2lp71lAD6QZ2O0NE4MIDvLhGGjTGklSP+2fS6joTAaV+tLbtrIp8JiR0MOX1rGPC5h+0ZHNvXQkcHJz3s68+iUAvDnQBiSu768b2C4zpHzteGEnJhU8sAdk83spiWogKALAVAuN4xfSXni7GTk9HObTTRdY+zehfWVPdE\/7uQSmMTzOKfPbQU02Jn\/5DdE3gYk6JZ5m70JsUSFBTF\/EVX8hhg0pu2iA==',
                                'serieCertificado' => '',
                                'tipoOperacion' => 'C',
                                'tipoPago' => '1'
                            ],
                            'estadoConsulta' => '1',
                            'url' => 'http =>\/\/www.banxico.org.mx\/cep?i=90646&s=20150825&d=viAKjS0GVYB8qihmG9I%2B9O1VUvrR2td%2Fuo3GyVDn8vBp371tVx5ltRnk4QsWP6KP%2BQvlWjT%2BzfwWWTA3TMk4tg%3D%3D'
                        ]
                    ]
                ]
            ], [
                'wid' => 'of40d7f0768ee91d3b33bee64831jg73',
                'status' => 'complete',
                'created_at' => '2016-04-08T17 =>52 =>31.000+00 =>00',
                'currency' => 'mxn',
                'method' => 'sp',
                'amount' => '500.00',
                'details' => [
                    'beneficiary_name' => 'ALFRED NORTH WHITEHEAD',
                    'beneficiary_bank' => 'BANAMEX',
                    'beneficiary_clabe' => '5204165009315197',
                    'numeric_reference' => '30535',
                    'concepto' => '-',
                    'clave_rastreo' => 'BNET01001604080002076841',
                    'cep' => [
                        'return' => [
                            'cda' => [
                                'cadenaOriginal' => '||1|07042016|07042016|095656|40002|STP|Bitso - Al|40|646180115400000002|BIT140123U70|BANAMEX|ALFRED NORTH WHITEHEAD|3|5204165009315197|ND|-|0.00|500.00|00001000000401205824||',
                                'conceptoPago' => '-',
                                'cuentaBeneficiario' => '5204165009315197',
                                'cuentaOrdenante' => '646180115400000002',
                                'fechaCaptura' => '20160407',
                                'fechaOperacion' => '20160407',
                                'hora' => '09 =>56 =>51',
                                'iva' => '0.00',
                                'monto' => '500.00',
                                'nombreBeneficiario' => 'ALFRED NORTH WHITEHEAD',
                                'nombreInstBeneficiaria' => 'BANAMEX',
                                'nombreInstOrdenante' => 'STP',
                                'nombreOrdenante' => 'Bitso - RUSSELL',
                                'referenciaNumerica' => '30535',
                                'rfcCurpBeneficiario' => 'ND',
                                'rfcCurpOrdenante' => 'BIT140123U70',
                                'selloDigital' => 'GaXpeaKgkc+gc0w9XgBbRCMmKWLNdSTV5C4CNQ4DL4ZVT+1OBSqNtX\/pv2IGjI7bKjCkaNrKUdaCdFwG6SdZ0nS9KtYSx1Ewg2Irg6x4kSzeHdlzBDr6ygT+bb+weizxcXMARKkciPuSQlyltCrEwSi07yVzachKfcEN8amj2fsEzim7gSyUc3ecKA1n8DX89158fwukKTIg4ECfOLsgueKF8unwbICWHXwRaaxIAA6PVw7O6WwGXxMtMBTCdiT202c8I2SnULFqK9QVJlQ\/YDRXFI4IMMAwGQZWbbmk8gf\/J3Fixy+0lcQV35TBBrbHyFPiaHaRN95yK\/BUxPOhag==',
                                'serieCertificado' => '',
                                'tipoOperacion' => 'C',
                                'tipoPago' => '1'
                            ],
                            'estadoConsulta' => '1',
                            'url' => 'http =>\/\/www.banxico.org.mx\/cep?i=90646&s=20150825&d=3AeATtn9mM9yySMqwClgSTnKIddFN7JVwo38kDBVjOBRtcYVENx1LblV%2BXOHnKEGTfp0g%2BVLM76C3ewQ0c9vpA%3D%3D'
                        ]
                    ],
                    'folio_origen' => 'BITSO4405016499736144'
                ]
            ]]
        ];

        $bitsoPrivateApi = new BitsoPrivateApi($this::KEY, $this::SECRET, $this->createMockClient(json_encode($mockedResponse)));
        $actualResponse = $bitsoPrivateApi->getWithdrawals(['limit'=> '20','wids'=>'p4u8d7f0768ee91d3b33bee6483132i8,of40d7f0768ee91d3b33bee64831jg73']);
        $this->assertEquals($mockedResponse['payload'], json_decode($actualResponse, true));
    }

    public function createMockClient(string $body, int $statusCode = 200): Client
    {
        $mock = new MockHandler([new Response($statusCode, [], $body)]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
