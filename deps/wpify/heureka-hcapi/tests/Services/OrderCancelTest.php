<?php

namespace HeurekaDeps\Hcapi\Services;

use HeurekaDeps\Mockery\Mock;
use HeurekaDeps\PHPUnit_Framework_TestCase;
/**
 * @author Oldrich Taufer <oldrich.taufer@heureka.cz>
 */
class OrderCancelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param array $parameters
     * @param array $response
     *
     * @throws ServiceException
     * @throws UndefinedCallbackException
     */
    public function testProcessDataCallable($parameters, $response)
    {
        $orderCancel = \HeurekaDeps\Mockery::mock('OrderCancel');
        $orderCancel->shouldReceive('cancelOrder')->once()->andReturn($response);
        $service = new OrderCancel();
        $callback = [$orderCancel, 'cancelOrder'];
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
        $orderCancel = \HeurekaDeps\Mockery::mock('HeurekaDeps\\Hcapi\\Interfaces\\IShopImplementation');
        $orderCancel->shouldReceive('getResponse')->with($parameters)->once()->andReturn($response);
        $service = new OrderCancel();
        $this->assertNotNull($service->processData($orderCancel, $parameters));
    }
    /**
     * @return array
     */
    public static function receiveDataProvider()
    {
        return [[['order_id' => 123, 'reason' => 6], ['status' => \true]]];
    }
}
