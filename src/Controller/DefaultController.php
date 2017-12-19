<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\BitsoPrivateApi;
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

    public function index()
    {
        return new Response($this->bitsoPublicApi->getTicker(['book' => 'btc_mxn']));
    }

    public function getAvailableBooksAction()
    {
        return new Response($this->bitsoPublicApi->getAvailableBooks(["book" => "btc_mxn", "aggregate" => "true"]));
    }

    public function getRecentTrades()
    {
        return new Response($this->bitsoPublicApi->getTrades(['book' => 'btc_mxn', 'limit' => '2']));
    }

    public function getAccountStatus()
    {
        return new Response($this->bitsoPrivateApi->getAccountStatus());
    }

    public function getFees()
    {
        return new Response($this->bitsoPrivateApi->getFees());
    }

    public function getLedger()
    {
        return new Response($this->bitsoPrivateApi->getLedger(["limit" => "15"]));
    }

    public function getWithdrawals()
    {
        return new Response($this->bitsoPrivateApi->getWithdrawals(["limit" => "20", "wids" => "ids"]));
    }

    public function getFundings()
    {
        return new Response($this->bitsoPrivateApi->getFundings(["fids" => "89e9e7807058d56b056b9dae42a5b643,ccacc3dafa18dffe0cc650e33b812f85"]));
    }

    public function placeOrder(Request $request)
    {
        return new Response($this->bitsoPrivateApi->placeOrder($request->request->all()));
    }
}