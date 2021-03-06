<?php

namespace Tests\Unit\Exception;

use LBHounslow\ApiClient\Enum\HttpStatusCodeEnum;
use LBHounslow\ApiClient\Exception\ApiException;
use Tests\Unit\ApiClientTestCase;

class ApiExceptionTest extends ApiClientTestCase
{
    public function testItSetsStatusCodeCorrectly()
    {
        $result = new ApiException(HttpStatusCodeEnum::BAD_REQUEST, 'error', '{"unexpected":"response"}', 13);
        $this->assertInstanceOf(\Exception::class, $result);
        $this->assertEquals(HttpStatusCodeEnum::BAD_REQUEST, $result->getStatusCode());
        $this->assertEquals('error', $result->getMessage());
        $this->assertEquals(13, $result->getCode());
    }
}
