<?php

namespace HeurekaDeps\Heureka;

use HeurekaDeps\Heureka\ShopCertification\IRequester;
use HeurekaDeps\Mockery;
require_once __DIR__ . '/../vendor/autoload.php';
/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ShopCertificationTest extends \HeurekaDeps\PHPUnit\Framework\TestCase
{
    public function tearDown() : void
    {
        Mockery::close();
    }
    public function testLogSetOrderWithInvalidOrderId()
    {
        $requester = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\IRequester');
        $requester->shouldReceive('setApiEndpoint')->once()->with(Mockery::type('HeurekaDeps\\Heureka\\ShopCertification\\ApiEndpoint'));
        $shopCertification = new ShopCertification('xxxxxxxx', [], $requester);
        $this->expectException('HeurekaDeps\\Heureka\\ShopCertification\\InvalidArgumentException');
        $shopCertification->setOrderId(\str_repeat('x', 256));
    }
    public function testLogOrderSuccess()
    {
        $requester = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\IRequester');
        $requester->shouldReceive('setApiEndpoint')->once()->with(Mockery::type('HeurekaDeps\\Heureka\\ShopCertification\\ApiEndpoint'));
        $response = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\Response');
        $response->code = 200;
        $response->message = 'ok';
        $postData = ['apiKey' => $apiKey = 'xxxxxxxxxx', 'email' => $email = 'john@doe.com', 'orderId' => $orderId = 12345, 'productItemIds' => [$product1 = 'ab12345', $product2 = '123459']];
        $requester->shouldReceive('request')->once()->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))->andReturn($response);
        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $shopCertification->setOrderId($orderId);
        $shopCertification->addProductItemId($product1);
        $shopCertification->addProductItemId($product2);
        $result = $shopCertification->logOrder();
        $this->assertInstanceOf('HeurekaDeps\\Heureka\\ShopCertification\\Response', $result);
    }
    public function testLogOrderSuccessWithoutOptionalFields()
    {
        $requester = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\IRequester');
        $requester->shouldReceive('setApiEndpoint')->once()->with(Mockery::type('HeurekaDeps\\Heureka\\ShopCertification\\ApiEndpoint'));
        $response = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\Response');
        $response->code = 200;
        $response->message = 'ok';
        $postData = ['apiKey' => $apiKey = 'xxxxxxxxxx', 'email' => $email = 'john@doe.com'];
        $requester->shouldReceive('request')->once()->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))->andReturn($response);
        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $result = $shopCertification->logOrder();
        $this->assertInstanceOf('HeurekaDeps\\Heureka\\ShopCertification\\Response', $result);
    }
    public function testLogOrderMissingEmail()
    {
        $requester = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\IRequester');
        $requester->shouldReceive('setApiEndpoint')->once()->with(Mockery::type('HeurekaDeps\\Heureka\\ShopCertification\\ApiEndpoint'));
        $shopCertification = new ShopCertification('xxxxxxxxxx', [], $requester);
        $this->expectException('HeurekaDeps\\Heureka\\ShopCertification\\MissingInformationException');
        $shopCertification->logOrder();
    }
    public function testLogOrderDoubleSend()
    {
        $requester = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\IRequester');
        $requester->shouldReceive('setApiEndpoint')->once()->with(Mockery::type('HeurekaDeps\\Heureka\\ShopCertification\\ApiEndpoint'));
        $response = Mockery::mock('HeurekaDeps\\Heureka\\ShopCertification\\Response');
        $response->code = 200;
        $response->message = 'ok';
        $postData = ['apiKey' => $apiKey = 'xxxxxxxxxx', 'email' => $email = 'john@doe.com', 'productItemIds' => [$product1 = 'ab12345', $product2 = '123459']];
        $requester->shouldReceive('request')->once()->with(IRequester::ACTION_LOG_ORDER, Mockery::mustBe([]), Mockery::mustBe($postData))->andReturn($response);
        $shopCertification = new ShopCertification($apiKey, [], $requester);
        $shopCertification->setEmail($email);
        $shopCertification->addProductItemId($product1);
        $shopCertification->addProductItemId($product2);
        $shopCertification->logOrder();
        $this->expectException('HeurekaDeps\\Heureka\\ShopCertification\\Exception');
        $shopCertification->logOrder();
    }
}
