<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Support;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Illuminate\Config\Repository as Config;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class TokenServiceTest extends TestCase
{
    private TokenService $sut;

    private MockObject|Config $config;

    private const int USER_ID = 10;

    private const int STORE_ID = 2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = $this->createStub(Config::class);

        $this->sut = new TokenService(
            $this->config,
        );
    }

    #[Test]
    public function tokenStructure(): void
    {
        $this->config->method('get')->willReturnMap([
            ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
            ['mm-cq.access-token-ttl', 28800],
        ]);

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);

        $this->assertNotEmpty($token);
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
        $this->assertEquals('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9', $parts[0]);
    }

    #[Test]
    public function encodeDecode(): void
    {
        $this->config->method('get')->willReturnMap([
            ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
            ['mm-cq.access-token-ttl', 28800],
        ]);

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);
        $payload = $this->sut->decodeAccessToken($token);

        $this->assertNotEmpty($payload);
        $this->assertEquals('acc', $payload['sub']);
        $this->assertEquals(self::USER_ID, $payload['uid']);
        $this->assertEquals(self::STORE_ID, $payload['stid']);
        $this->assertNotEmpty($payload['exp']);
    }

    /**
     * @param  array<int, array<int, string|int>>  $getConfigResponse
     */
    #[Test, DataProvider('dataProviderException')]
    public function exception(
        array $getConfigResponse,
        string $token,
        string $expected,
        string $message,
    ): void {
        $this->config->method('get')->willReturnMap($getConfigResponse);

        $this->expectException($expected);
        $this->expectExceptionMessageMatches('/^' . preg_quote($message, '/') . '$/');

        $this->sut->decodeAccessToken($token);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderException(): array
    {
        return [
            'invalid app.key' => [
                'getConfigResponse' => [
                    ['app.key', 'invalid key'],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => '',
                'expected' => AppNeverException::class,
                'message' => 'Not found configuration parameter "app.key"',
            ],
            'invalid token' => [
                'getConfigResponse' => [
                    ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => 'invalid token',
                'expected' => \UnexpectedValueException::class,
                'message' => 'Wrong number of segments',
            ],
            'invalid token 2' => [
                'getConfigResponse' => [
                    ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => 'part1.part2.part3',
                'expected' => \DomainException::class,
                'message' => 'Malformed UTF-8 characters',
            ],
            'invalid token 3' => [
                'getConfigResponse' => [
                    ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
                    ['mm-cq.access-token-ttl', 28800],
                ],
                'token' => base64_encode('part1') . '.' . base64_encode('part2') . '.' . base64_encode('part3'),
                'expected' => \DomainException::class,
                'message' => 'Syntax error, malformed JSON',
            ],
        ];
    }

    #[Test]
    public function expiredToken(): void
    {
        $this->config->method('get')->willReturnMap([
            ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
            ['mm-cq.access-token-ttl', -10],
        ]);

        $this->expectException(ExpiredException::class);
        $this->expectExceptionMessageMatches('/^Expired token$/');

        $token = $this->sut->newAccessToken(self::USER_ID, self::STORE_ID);
        $this->sut->decodeAccessToken($token);
    }

    #[Test]
    public function invalidPayload(): void
    {
        $this->config->method('get')->willReturnMap([
            ['app.key', 'base64:' . base64_encode('TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET')],
        ]);

        $payload = [
            'exp' => (new \DateTime)->getTimestamp() + 100,
        ];
        $token = JWT::encode($payload, 'TESTSECRETTESTSECRETTESTSECRETTESTSECRETTESTSECRET', 'HS256');

        $this->expectException(AppValidationException::class);
        $this->expectExceptionMessageMatches('/^JWT token is not access token[.]$/');

        $this->sut->decodeAccessToken($token);
    }
}
