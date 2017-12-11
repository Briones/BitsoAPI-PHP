<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\BitsoPrivateApi;
use App\Exception\BitsoException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\BitsoPublicApi;

class DefaultController
{
    /**
     * @var BitsoPublicApi
     */
    protected $bitsoPublicApi;

    /**
     * @var BitsoPrivateApi
     */
    protected $bitsoPrivateApi;

    public function __construct(BitsoPublicApi $bitsoPublicApi, BitsoPrivateApi $bitsoPrivateApi)
    {
        $this->bitsoPublicApi = $bitsoPublicApi;
        $this->bitsoPrivateApi = $bitsoPrivateApi;
    }

    public function index() {
        return new Response(json_encode($this->bitsoPublicApi->getTicker(['book'=>'btc_mxn'])));
    }

    public function getAvailableBooksAction() {
        return new Response(json_encode($this->bitsoPublicApi->getAvailableBooks(["book"=>"btc_mxn","aggregate"=> "true"])));
    }

    public function getRecentTrades() {
        return new Response(json_encode($this->bitsoPublicApi->getTrades(['book'=>'btc_mxn', 'limit' => '2'])));
    }

    public function getAccountStatus() {
        return new Response(json_encode($this->bitsoPrivateApi->getAccountStatus()));
    }

    public function getFees() {
        return new Response(json_encode($this->bitsoPrivateApi->getFees()));
    }

    public function getLedger() {
        return new Response(json_encode($this->bitsoPrivateApi->getLedger(["limit"=>"15"])));
    }

    public function placeOrder(Request $request) {
        try{
            $trades = $this->bitsoPublicApi->makePrivateRequest('orders/', 'POST', $request->request->all());
        } catch (BitsoException $exception){
            return new Response('Algo salió mal :' .$exception->getMessage() );
        }

        return new Response(\GuzzleHttp\json_encode($trades));
    }
}