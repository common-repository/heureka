<?php

namespace HeurekaDeps\Hcapi\Services;

/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class PaymentStatusTest extends \HeurekaDeps\PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider receiveDataProvider
     *
     * @param array $parameters
     * @param array $response
     *
     * @throws ServiceException
     * @throws UndefinedCallbackException
     */
    public function testProcessDataCallable($parameters, $response)
    {
        $paymentStatus = \HeurekaDeps\Mockery::mock('PaymentStatus');
        $paymentStatus->shouldReceive('setPaymentStatus')->with($parameters)->once()->andReturn($response);
        $service = new \HeurekaDeps\Hcapi\Services\PaymentStatus();
        $callback = [$paymentStatus, 'setPaymentStatus'];
        $this->assertNotNull($service->processData($callback, $parameters));
    }
    /**
     * @dataProvider receiveDataProvider
     *
     * @param array $parameters
     * @param array $response
     *
     * @throws ServiceException
     * @throws UndefinedCallbackException
     */
    public function testProcessDataInterface($parameters, $response)
    {
        $paymentStatus = \HeurekaDeps\Mockery::mock('HeurekaDeps\\Hcapi\\Interfaces\\IShopImplementation');
        $paymentStatus->shouldReceive('getResponse')->with($parameters)->once()->andReturn($response);
        $service = new \HeurekaDeps\Hcapi\Services\PaymentStatus();
        $this->assertNotNull($service->processData($paymentStatus, $parameters));
    }
    /**
     * @return array
     */
    public static function receiveDataProvider()
    {
        return [[['order_id' => 321], ['status' => \false]]];
    }
}
