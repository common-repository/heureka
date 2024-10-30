<?php

namespace HeurekaDeps\Hcapi\Validators;

use HeurekaDeps\PHPUnit_Framework_TestCase;
/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class ProductsAvailabilityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider responseDataProvider
     *
     * @param $response
     * @param $expectedException
     *
     * @throws ExpectedResponseDataException
     * @throws MissingRequiredDataException
     * @cover        OrderCancelValidator::isValid
     */
    public function testIsValid($response, $expectedException)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }
        $validator = new ProductsAvailability();
        $this->assertTrue($validator->validate($response));
    }
    /**
     * @return array
     */
    public static function responseDataProvider()
    {
        return [
            [['products' => [['id' => 'ABC123', 'available' => \true, 'count' => 1, 'delivery' => 0, 'name' => 'Diesel Zero Plus Masculine', 'price' => 100.0, 'related' => [['title' => 'Zdarma darkova taska']], 'params' => [['id' => 2, 'type' => 'input', 'name' => 'Delka', 'unit' => 'm', 'values' => []], ['id' => 2, 'type' => 'selectbox', 'name' => 'barva', 'unit' => '', 'values' => [['id' => 2, 'default' => \false, 'value' => 'cervena', 'price' => 10.6], ['id' => 2, 'default' => \true, 'value' => 'cerna', 'price' => 0.0]]]], 'priceTotal' => 100.0], ['id' => 'ABC1234', 'available' => \true, 'count' => 2, 'delivery' => 'na dotaz', 'name' => 'Mikrovlnna trouba Ariete-Scarlett 933 nerez', 'price' => 200.0, 'related' => [['title' => 'Vynaska do 5 patra zdarma'], ['title' => 'Propiska zdarma']], 'priceTotal' => 400.0]], 'priceSum' => 500.0], 'expectedException' => null],
            //missing variable
            [['products' => [['count' => 1, 'delivery' => 0, 'name' => 'Diesel Zero Plus Masculine', 'price' => 100.0, 'related' => [['title' => 'Zdarma darkova taska']], 'params' => [['id' => 2, 'values' => []], ['id' => 2, 'type' => 'selectbox', 'name' => 'barva', 'unit' => '', 'values' => [['id' => 2, 'default' => \false, 'value' => 'cervena', 'price' => 10.6], ['id' => 2, 'default' => \true, 'value' => 'cerna', 'price' => 0.0]]]], 'priceTotal' => 100.0]], ['id' => 'ABC1234', 'avaible' => \true, 'count' => 2, 'delivery' => 'na dotaz', 'name' => 'Mikrovlnna trouba Ariete-Scarlett 933 nerez', 'price' => 200.0, 'related' => [['title' => 'Vynaska do 5 patra zdarma'], ['title' => 'Propiska zdarma']], 'priceTotal' => 400], 'priceSum' => 500.0], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\MissingRequiredDataException'],
            //bad variable type
            [['products' => [['id' => 'ABC123', 'available' => \true, 'count' => 1, 'delivery' => 'nedelivery', 'name' => 'Diesel Zero Plus Masculine', 'price' => 100.0, 'related' => [['title' => 'Zdarma darkova taska']], 'params' => [['id' => 2, 'type' => 'input', 'name' => 'Delka', 'unit' => 'm', 'values' => []], ['id' => 2, 'type' => 'selectbox', 'name' => 'barva', 'unit' => '', 'values' => [['id' => 2, 'default' => \false, 'value' => 'cervena', 'price' => 10.6], ['id' => 2, 'default' => \true, 'value' => 57, 'price' => 0.0]]]], 'priceTotal' => 100.0]], ['id' => 'ABC1234', 'avaible' => \true, 'count' => 2, 'delivery' => 'na dotaz', 'name' => 'Mikrovlnna trouba Ariete-Scarlett 933 nerez', 'price' => 200.0, 'related' => [['title' => 'Vynaska do 5 patra zdarma'], ['title' => 'Propiska zdarma']], 'priceTotal' => 400], 'priceSum' => '500'], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\ExpectedFloatException'],
            // bad priceSum
            [['products' => [['id' => 'ABC123', 'available' => \true, 'count' => 1, 'delivery' => 0, 'name' => 'Diesel Zero Plus Masculine', 'price' => 100.0, 'related' => [['title' => 'Zdarma darkova taska']], 'params' => [['id' => 2, 'type' => 'input', 'name' => 'Delka', 'unit' => 'm', 'values' => []], ['id' => 2, 'type' => 'selectbox', 'name' => 'barva', 'unit' => '', 'values' => [['id' => 2, 'default' => \false, 'value' => 'cervena', 'price' => 10.6], ['id' => 2, 'default' => \true, 'value' => 'cerna', 'price' => 0.0]]]], 'priceTotal' => 100.0]], ['id' => 'ABC1234', 'avaible' => \true, 'count' => 2, 'delivery' => 'na dotaz', 'name' => 'Mikrovlnna trouba Ariete-Scarlett 933 nerez', 'price' => 200.0, 'related' => [['title' => 'Vynaska do 5 patra zdarma'], ['title' => 'Propiska zdarma']], 'priceTotal' => 400], 'priceSum' => 100000000.0], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\BadSumPriceException'],
        ];
    }
}
