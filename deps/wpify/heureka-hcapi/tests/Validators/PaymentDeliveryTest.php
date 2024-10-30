<?php

namespace HeurekaDeps\Hcapi\Validators;

use HeurekaDeps\Hcapi\Codes\DeliveryType;
use HeurekaDeps\Hcapi\Codes\PaymentType;
use HeurekaDeps\Hcapi\Codes\StoreType;
use HeurekaDeps\PHPUnit_Framework_TestCase;
/**
 * @author Oldřich Taufer <oldrich.taufer@heureka.cz>
 */
class PaymentDeliveryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider responseDataProvider
     *
     * @param array $response
     * @param       $expectedException
     *
     * @throws ExpectedResponseDataException
     * @throws MissingRequiredDataException
     */
    public function testIsValid($response, $expectedException)
    {
        if (!empty($expectedException)) {
            $this->setExpectedException($expectedException);
        }
        $validator = new PaymentDelivery();
        $this->assertTrue($validator->validate($response));
    }
    /**
     * @return array
     */
    public static function responseDataProvider()
    {
        return [
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => 120.2, 'description' => 'Casem to treba dojde'], ['id' => 2, 'type' => DeliveryType::DELIVERY_TYPE_SPECIAL, 'name' => 'Specialni', 'price' => 100.0, 'description' => 'Specialni doprava do 2h'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['type' => StoreType::STORE_TYPE_INTERNAL, 'id' => 2045]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 547, 'type' => PaymentType::PAYMENT_CREDIT_CARD, 'name' => 'Platba kartou', 'price' => 40.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123], ['id' => 2, 'transportId' => 2, 'paymentId' => 547], ['id' => 3, 'transportId' => 3, 'paymentId' => 321]]], 'expectedException' => null],
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'description' => 'Casem to treba dojde'], ['id' => 2, 'type' => DeliveryType::DELIVERY_TYPE_SPECIAL, 'name' => 'Specialni', 'price' => 100.0, 'description' => 'Specialni doprava do 2h'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...']], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 547, 'type' => PaymentType::PAYMENT_CREDIT_CARD, 'price' => 40.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['transportId' => 1, 'paymentId' => 123], ['id' => 2, 'transportId' => 1, 'paymentId' => 547], ['id' => 3, 'transportId' => 3, 'paymentId' => 321]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\MissingRequiredDataException'],
            //missing binding pairs
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => 120.2, 'description' => 'Casem to treba dojde'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['type' => StoreType::STORE_TYPE_INTERNAL, 'id' => 2045]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\UnusedTransportException'],
            //invalid binding pair
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => 120.2, 'description' => 'Casem to treba dojde'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['type' => StoreType::STORE_TYPE_INTERNAL, 'id' => 2045]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123], ['id' => 22, 'transportId' => 45648, 'paymentId' => 46845]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\BindingException'],
            //transport 3, missing store type
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => 120.2, 'description' => 'Casem to treba dojde'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['id' => 54]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123], ['id' => 2, 'transportId' => 3, 'paymentId' => 321]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\MissingRequiredDataException'],
            //transport id 3 && payment id 321 are`t used
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => 120.2, 'description' => 'Casem to treba dojde'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 0.0, 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['type' => StoreType::STORE_TYPE_INTERNAL, 'id' => 2045]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\UnusedTransportException'],
            //bad type of variable 'price'
            [['transport' => [['id' => 1, 'type' => DeliveryType::DELIVERY_TYPE_CZECH_POST, 'name' => 'Ceska posta', 'price' => '120.20', 'description' => 'NIkdy to nedojde'], ['id' => 2, 'type' => DeliveryType::DELIVERY_TYPE_SPECIAL, 'name' => 'Specialni', 'price' => 100.0, 'description' => 'Specialni doprava do 2h'], ['id' => 3, 'type' => DeliveryType::DELIVERY_TYPE_PERSONAL_PICKUP, 'name' => 'Osobni odber Liberec', 'price' => 'tady ma byt cena', 'description' => 'O pripravenosti zbozi vas ...', 'store' => ['type' => StoreType::STORE_TYPE_INTERNAL, 'id' => 2045]]], 'payment' => [['id' => 123, 'type' => PaymentType::PAYMENT_BANK_TRANSFER, 'name' => 'Platba bankovnim prevodem', 'price' => 0.0], ['id' => 547, 'type' => PaymentType::PAYMENT_CREDIT_CARD, 'name' => 'Platba kartou', 'price' => 40.0], ['id' => 321, 'type' => PaymentType::PAYMENT_CASH_ON_DELIVERY, 'name' => 'Dobirka PPL', 'price' => 120.0]], 'binding' => [['id' => 1, 'transportId' => 1, 'paymentId' => 123], ['id' => 2, 'transportId' => 2, 'paymentId' => 547], ['id' => 3, 'transportId' => 3, 'paymentId' => 321]]], 'expectedException' => 'HeurekaDeps\\Hcapi\\Validators\\ExpectedFloatException'],
        ];
    }
}
