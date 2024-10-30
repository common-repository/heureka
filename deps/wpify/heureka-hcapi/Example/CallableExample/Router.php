<?php

namespace HeurekaDeps\Example\CallableExample;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/OrderCancel.php';
require_once __DIR__ . '/OrderSend.php';
require_once __DIR__ . '/OrderStatus.php';
require_once __DIR__ . '/PaymentDelivery.php';
require_once __DIR__ . '/PaymentStatus.php';
require_once __DIR__ . '/ProductsAvailability.php';
use HeurekaDeps\Hcapi\Services\OrderCancel;
use HeurekaDeps\Hcapi\Services\OrderSend;
use HeurekaDeps\Hcapi\Services\OrderStatus;
use HeurekaDeps\Hcapi\Services\PaymentDelivery;
use HeurekaDeps\Hcapi\Services\PaymentStatus;
use HeurekaDeps\Hcapi\Services\ProductsAvailability;
/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class Router
{
    /**
     * @return array|null
     * @throws \Hcapi\Services\ServiceException
     * @throws \Hcapi\Services\UndefinedCallbackException
     */
    function listenRouteCallbackExample()
    {
        /**
         * Specific url required by Heureka
         */
        $routeUrl = $_SERVER['REQUEST_URI'];
        /**
         * Data received from Heureka
         */
        $receiveData = $_POST;
        if ($routeUrl === '/api/1/products/availability') {
            $service = new ProductsAvailability();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\ProductsAvailability', 'getActualData'], $receiveData);
        }
        if ($routeUrl === '/api/1/payment/delivery') {
            $service = new PaymentDelivery();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\PaymentDelivery', 'getTransportsPayments'], $receiveData);
        }
        if ($routeUrl === '/api/1/order/status') {
            $service = new OrderStatus();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\OrderStatus', 'checkStatus'], $receiveData);
        }
        if ($routeUrl === '/api/1/order/send') {
            $service = new OrderSend();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\OrderSend', 'processOrder'], $receiveData);
        }
        if ($routeUrl === '/api/1/order/cancel') {
            $service = new OrderCancel();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\OrderCancel', 'cancelOrder'], $receiveData);
        }
        if ($routeUrl === '/api/1/payment/status') {
            $service = new PaymentStatus();
            return $service->processData(['HeurekaDeps\\Example\\CallableExample\\PaymentStatus', 'setPaymentStatus'], $receiveData);
        }
        return null;
    }
}
