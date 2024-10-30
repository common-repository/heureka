<?php

namespace HeurekaDeps\Heureka\ShopCertification;

use HeurekaDeps\Heureka\ShopCertification;
/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ApiEndpointTest extends \HeurekaDeps\PHPUnit\Framework\TestCase
{
    public function testGetEndpoint()
    {
        $apiEndpoint = new ApiEndpoint(ShopCertification::HEUREKA_CZ);
        $this->assertSame(ApiEndpoint::API_ENDPOINT_CZ, $apiEndpoint->getUrl());
        $apiEndpoint = new ApiEndpoint(ShopCertification::HEUREKA_SK);
        $this->assertSame(ApiEndpoint::API_ENDPOINT_SK, $apiEndpoint->getUrl());
        $this->expectException('HeurekaDeps\\Heureka\\ShopCertification\\UnknownServiceException');
        new ApiEndpoint(15);
    }
}
