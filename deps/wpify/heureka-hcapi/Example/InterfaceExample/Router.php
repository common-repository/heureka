<?php

namespace HeurekaDeps\Example\InterfaceExample;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/OrderCancel.php';
require_once __DIR__ . '/OrderSend.php';
require_once __DIR__ . '/OrderStatus.php';
require_once __DIR__ . '/PaymentDelivery.php';
require_once __DIR__ . '/PaymentStatus.php';
require_once __DIR__ . '/ProductsAvailability.php';
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
    function listenRouteInterfaceExample()
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
            $service = new \HeurekaDeps\Hcapi\Services\ProductsAvailability();
            $productsAvailability = new \HeurekaDeps\Example\InterfaceExample\ProductsAvailability();
            return $service->processData($productsAvailability, $receiveData);
        }
        if ($routeUrl === '/api/1/payment/delivery') {
            $service = new \HeurekaDeps\Hcapi\Services\PaymentDelivery();
            $paymentDelivery = new \HeurekaDeps\Example\InterfaceExample\PaymentDelivery();
            return $service->processData($paymentDelivery, $receiveData);
        }
        if ($routeUrl === '/api/1/order/status') {
            $service = new \HeurekaDeps\Hcapi\Services\OrderStatus();
            $orderStatus = new \HeurekaDeps\Example\InterfaceExample\OrderStatus();
            return $service->processData($orderStatus, $receiveData);
        }
        if ($routeUrl === '/api/1/order/send') {
            $service = new \HeurekaDeps\Hcapi\Services\OrderSend();
            $orderSend = new \HeurekaDeps\Example\InterfaceExample\OrderSend();
            return $service->processData($orderSend, $receiveData);
        }
        if ($routeUrl === '/api/1/order/cancel') {
            $service = new \HeurekaDeps\Hcapi\Services\OrderCancel();
            $orderCancel = new \HeurekaDeps\Example\InterfaceExample\OrderCancel();
            return $service->processData($orderCancel, $receiveData);
        }
        if ($routeUrl === '/api/1/payment/status') {
            $service = new \HeurekaDeps\Hcapi\Services\PaymentStatus();
            $paymentStatus = new \HeurekaDeps\Example\InterfaceExample\PaymentStatus();
            return $service->processData($paymentStatus, $receiveData);
        }
        return null;
    }
}
