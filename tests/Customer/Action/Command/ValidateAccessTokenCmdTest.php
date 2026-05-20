<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Command;

use Illuminate\Http\Request;
use Module\Customer\Action\Command\ValidateAccessTokenCmd;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppNeverException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class ValidateAccessTokenCmdTest extends TestCase
{
    private ValidateAccessTokenCmd $sut;

    private MockObject|TokenService $tokenService;

    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenService = $this->createStub(TokenService::class);
        $this->request = resolve(Request::class);

        $this->sut = new ValidateAccessTokenCmd(
            $this->tokenService,
            $this->request,
        );
    }

    /**
     * @param  array<string, int>  $payload
     * @param  array<string, int>  $expected
     */
    #[Test, DataProvider('dataProviderValidate')]
    public function validate(
        array $payload,
        array $expected,
        string $expectedException
    ): void {
        $this->tokenService->method('decodeAccessToken')->willReturn($payload);

        if ($expectedException) {
            $this->expectException(AppNeverException::class);
            $this->expectExceptionMessageMatches('/^' . preg_quote($expectedException, '/') . '$/');
        }

        $this->sut->validate('token');

        $this->assertInstanceOf(CustomerBag::class, $this->request->attributes->get('customerBag'));
        $this->assertEquals($expected['customerId'], $this->request->attributes->get('customerBag')->customerId);
        $this->assertEquals($expected['selectedStoreId'], $this->request->attributes->get('customerBag')->selectedStoreId);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderValidate(): array
    {
        return [
            'valid' => [
                'payload' => ['uid' => 10, 'stid' => 20],
                'expected' => ['customerId' => 10, 'selectedStoreId' => 20],
                'expectedException' => '',
            ],
            'store-not-set' => [
                'payload' => ['uid' => 10],
                'expected' => ['customerId' => 10, 'selectedStoreId' => 0],
                'expectedException' => '',
            ],
            'uid-not-set' => [
                'payload' => ['stid' => 20],
                'expected' => ['customerId' => 0, 'selectedStoreId' => 0],
                'expectedException' => 'Missing "uid" in JWT token',
            ],
        ];
    }
}
