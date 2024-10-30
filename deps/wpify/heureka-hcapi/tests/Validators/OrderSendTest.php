<?php

namespace HeurekaDeps\Hcapi\Validators;

use HeurekaDeps\PHPUnit_Framework_TestCase;
/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class OrderSendTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider responseDataProvider
     *
     * @param $response
     * @param $expectedException
     */
    public function testIsValid($response, $expectedException)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }
        $validator = new OrderSend();
        $this->assertTrue($validator->validate($response));
    }
    /**
     * @return array
     */
    public static function responseDataProvider()
    {
        return [[['order_id' => 45641681, 'internal_id' => 'HRK-2012-0001', 'variableSymbol' => 864561354], 'expectedException' => null], [['order_id' => '35413FEQDA', 'internal_id' => 'HRK-2012-0001', 'variableSymbol' => 864354135], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\ExpectedIntegerException'], [['order_id' => 45641681, 'internal_id' => 'HRK-2012-0001', 'variableSymbol' => 8.641354681435435E+29], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\VariableSymbolLengthException'], [['order_id' => 45641681, 'variableSymbol' => 864561354], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\MissingRequiredDataException']];
    }
}
