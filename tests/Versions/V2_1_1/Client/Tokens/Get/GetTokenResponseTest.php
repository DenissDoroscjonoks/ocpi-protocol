<?php

declare(strict_types=1);

namespace Tests\Chargemap\OCPI\Versions\V2_1_1\Client\Tokens\Get;

use Chargemap\OCPI\Versions\V2_1_1\Client\Tokens\Get\GetTokenResponse;
use DateTime;
use Http\Discovery\Psr17FactoryDiscovery;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class GetTokenResponseTest extends TestCase
{
    public function validPayloadProvider(): iterable
    {
        foreach (scandir(__DIR__ . '/payloads/') as $filename) {
            if (substr($filename, 0, 3) === 'ok_') {
                yield basename(substr($filename, 3), '.json') => [__DIR__ . '/payloads/' . $filename];
            }
        }
    }

    /**
     * @dataProvider validPayloadProvider
     * @param string $filename
     */
    public function testJsonSchema(string $filename): void
    {
        $this->expectNotToPerformAssertions();
        $serverResponse = Psr17FactoryDiscovery::findResponseFactory()->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                Psr17FactoryDiscovery::findStreamFactory()->createStream(file_get_contents($filename))
            );

        GetTokenResponse::from($serverResponse)->getToken();
    }

    public function testWithAllFields(): void
    {
        $serverResponse = Psr17FactoryDiscovery::findResponseFactory()->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                Psr17FactoryDiscovery::findStreamFactory()->createStream(file_get_contents(__DIR__ . '/payloads/ok_all_fields.json'))
            );

        $token = GetTokenResponse::from($serverResponse)->getToken();

        $this->assertSame('012345678', $token->getUid());
        $this->assertSame('RFID', $token->getType()->getValue());
        $this->assertSame('DE8ACC12E46L89', $token->getAuthId());
        $this->assertSame('DF000-2001-8999', $token->getVisualNumber());
        $this->assertSame('TheNewMotion', $token->getIssuer());
        $this->assertTrue($token->isValid());
        $this->assertSame('ALWAYS', $token->getWhiteList()->getValue());
        $this->assertSame('FR', $token->getLanguage());
        $this->assertEquals(new DateTime('2015-06-29T22:39:09Z'), $token->getLastUpdated());
    }

    public function invalidPayloadProvider(): iterable
    {
        foreach (scandir(__DIR__ . '/payloads/') as $filename) {
            if (substr($filename, 0, 3) === 'ko_') {
                yield basename(substr($filename, 3), '.json') => [__DIR__ . '/payloads/' . $filename];
            }
        }
    }

    /**
     * @dataProvider invalidPayloadProvider
     * @param string $filename
     */
    public function testShouldThrowExceptionWithInvalidPayload(string $filename): void
    {
        $serverResponse = Psr17FactoryDiscovery::findResponseFactory()->createResponse(200)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(
                Psr17FactoryDiscovery::findStreamFactory()->createStream(file_get_contents($filename))
            );

        $this->expectException(UnexpectedValueException::class);

        GetTokenResponse::from($serverResponse)->getToken();
    }
}
