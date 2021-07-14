<?php

namespace Tests\Unit\Client;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\InvalidArgumentException as GuzzleInvalidArgumentException;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Hounslow\ApiClient\Client\Client as ApiClient;
use Hounslow\ApiClient\Entity\AccessToken;
use Hounslow\ApiClient\Enum\HttpStatusCodeEnum;
use Hounslow\ApiClient\Enum\MonologEnum;
use Hounslow\ApiClient\Exception\ApiException;
use Hounslow\ApiClient\Response\ApiResponse;
use Hounslow\ApiClient\Session\Session;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Unit\ApiClientTestCase;

class ClientTest extends ApiClientTestCase
{
    /**
     * @var ApiClient|MockObject
     */
    private $apiClient;

    /**
     * @var GuzzleClient|MockObject
     */
    private $mockGuzzleClient;

    /**
     * @var Session|MockObject
     */
    private $mockSession;

    public function setUp(): void
    {
        $this->mockGuzzleClient = $this->getMockBuilder(GuzzleClient::class)
            ->addMethods(['post', 'get'])
            ->getMock();
        $this->mockSession = $this->createMock(Session::class);
        $this->apiClient = $this->createPartialMock(ApiClient::class, ['getBearerToken']);
        $this->apiClient
            ->setGuzzleClient($this->mockGuzzleClient)
            ->setUsername(self::USERNAME)
            ->setPassword(self::PASSWORD);
        $this->apiClient
            ->method('getBearerToken')
            ->willReturn(self::ACCESS_TOKEN);
        parent::setUp();
    }

    public function testPostMethodPreventsErrorLogging()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Please use the logError method to log errors');
        $this->apiClient->post(ApiClient::LOG_ERROR_ENDPOINT);
    }

    public function testPostMethodHandlesGuzzleExceptions()
    {
        $this->mockGuzzleClient
            ->method('post')
            ->willThrowException(new GuzzleInvalidArgumentException('Guzzle error'));
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Guzzle error');
        $this->apiClient->post('/api/endpoint');
    }

    /**
     * @param mixed $response
     * @dataProvider invalidPostResponseDataProvider
     */
    public function testPostMethodHandlesInvalidResponses($response)
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unrecognised response from API');
        $this->mockGuzzleClient
            ->method('post')
            ->willReturn($response);
        $this->apiClient->post('/api/endpoint');
    }

    public function invalidPostResponseDataProvider()
    {
        return [
            [null],
            [new \stdClass()],
            [self::RANDOM_ERROR_STRING],
            [123]
        ];
    }

    /**
     * @param GuzzleResponse $response
     * @param bool $isSuccessful
     * @param array $expectedPayload
     * @dataProvider successfulPostResponseDataProvider
     */
    public function testSuccessfulPostMethodReturnsApiResponse(GuzzleResponse $response, bool $isSuccessful, array $expectedPayload)
    {
        $this->mockGuzzleClient
            ->method('post')
            ->willReturn($response);

        $result = $this->apiClient->post('/api/endpoint');

        $this->assertInstanceOf(ApiResponse::class, $result);
        $this->assertEquals($isSuccessful, $result->isSuccessful());
        $this->assertEquals($expectedPayload, $result->getPayload());
    }

    public function successfulPostResponseDataProvider()
    {
        return [
            [new GuzzleResponse(HttpStatusCodeEnum::OK, [], self::SUCCESSFUL_API_JSON), true, [['foo' => 'bar']]],
            [new GuzzleResponse(HttpStatusCodeEnum::BAD_REQUEST, [], self::UNSUCCESSFUL_API_JSON), false, []],
            [new GuzzleResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR, [], self::UNSUCCESSFUL_API_JSON), false, []]
        ];
    }

    public function testGetMethodHandlesGuzzleExceptions()
    {
        $this->mockGuzzleClient
            ->method('get')
            ->willThrowException(new GuzzleInvalidArgumentException('Guzzle error'));
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Guzzle error');
        $this->apiClient->get('/api/endpoint');
    }

    /**
     * @param mixed $response
     * @dataProvider invalidGetResponseDataProvider
     */
    public function testGetMethodHandlesInvalidResponses($response)
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unrecognised response from API');
        $this->mockGuzzleClient
            ->method('get')
            ->willReturn($response);
        $this->apiClient->get('/api/endpoint');
    }

    public function invalidGetResponseDataProvider()
    {
        return [
            [null],
            [new \stdClass()],
            [self::RANDOM_ERROR_STRING],
            [123]
        ];
    }

    /**
     * @param GuzzleResponse $response
     * @param bool $isSuccessful
     * @param array $expectedPayload
     * @dataProvider successfulGetResponseDataProvider
     */
    public function testSuccessfulGetMethodReturnsApiResponse(GuzzleResponse $response, bool $isSuccessful, array $expectedPayload)
    {
        $this->mockGuzzleClient
            ->method('get')
            ->willReturn($response);

        $result = $this->apiClient->get('/api/endpoint');

        $this->assertInstanceOf(ApiResponse::class, $result);
        $this->assertEquals($isSuccessful, $result->isSuccessful());
        $this->assertEquals($expectedPayload, $result->getPayload());
    }

    public function successfulGetResponseDataProvider()
    {
        return [
            [new GuzzleResponse(HttpStatusCodeEnum::OK, [], self::SUCCESSFUL_API_JSON), true, [['foo' => 'bar']]],
            [new GuzzleResponse(HttpStatusCodeEnum::BAD_REQUEST, [], self::UNSUCCESSFUL_API_JSON), false, []],
            [new GuzzleResponse(HttpStatusCodeEnum::INTERNAL_SERVER_ERROR, [], self::UNSUCCESSFUL_API_JSON), false, []]
        ];
    }

    public function testRequestAccessTokenFailsWithNoUsernameAndPasswordSet()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Username and Password must be set');
        $this->apiClient->setUsername('')->setPassword('');
        $this->apiClient->requestAccessToken();
    }

    public function testRequestAccessTokenHandlesGuzzleException()
    {
        $this->mockGuzzleClient
            ->method('post')
            ->willThrowException(new GuzzleInvalidArgumentException('Guzzle error'));
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Guzzle error');
        $this->apiClient->requestAccessToken();
    }

    /**
     * @param mixed $response
     * @dataProvider invalidRequestAccessTokenDataProvider
     */
    public function testRequestAccessTokenHandlesInvalidResponses($response)
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Unrecognised response from API');
        $this->mockGuzzleClient
            ->method('post')
            ->willReturn($response);
        $this->apiClient->requestAccessToken();
    }

    public function invalidRequestAccessTokenDataProvider()
    {
        return [
            [null],
            [new \stdClass()],
            [self::RANDOM_ERROR_STRING],
            [123]
        ];
    }

    public function testItReturnsAnAccessTokenForAValidResponse()
    {
        $this->mockGuzzleClient
            ->method('post')
            ->willReturn(new GuzzleResponse(HttpStatusCodeEnum::OK, [], self::ACCESS_TOKEN_RESPONSE_JSON));

        $result = $this->apiClient->requestAccessToken();

        $this->assertInstanceOf(AccessToken::class, $result);
        $this->assertEquals(self::BEARER, $result->getType());
        $this->assertEquals(self::ACCESS_TOKEN, $result->getToken());
        $this->assertEquals(self::REFRESH_TOKEN, $result->getRefreshToken());
        $this->assertInstanceOf(\DateTimeImmutable::class, $result->getExpiry());
    }

    public function testLogErrorFailsWithInvalidMonologLevel()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid level (see: ' . MonologEnum::LINK . ')');
        $this->apiClient->logError('INVALID', 'Error message');
    }

    public function testLogErrorFailsWithEmptyMessage()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error message is required');
        $this->apiClient->logError(MonologEnum::ERROR, '');
    }

    public function testUploadFailsWithInvalidEndpoint()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid upload endpoint');
        $this->apiClient->upload(new \SplFileInfo('path'), '/api/xyz');
    }
}
