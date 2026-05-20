<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Command;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Module\Customer\Action\Command\ChangeStoreCmd;
use Module\Customer\Action\Dto\ChangeStoreRequestDto;
use Module\Customer\Model\Customer;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppValidationException;
use Module\Store\Action\Query\CheckStoreExistsQry;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

class ChangeStoreCmdTest extends TestCase
{
    use DatabaseTransactions;

    private ChangeStoreCmd $sut;

    private MockObject|CheckStoreExistsQry $checkStoreExistsQry;

    private Request $request;

    private const int CUSTOMER_ID = 10;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkStoreExistsQry = $this->createStub(CheckStoreExistsQry::class);
        $this->request = resolve(Request::class);
        $tokenService = $this->createStub(TokenService::class);
        $tokenService->method('newAccessToken')->willReturn('new token');

        $this->sut = new ChangeStoreCmd(
            $this->checkStoreExistsQry,
            $this->request,
            $tokenService,
        );
        $this->prepareDb();
    }

    /**
     * @param  array{int, int}|null  $bagData
     */
    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        bool $storeExists,
        ?array $bagData,
        int $storeId,
        string $expectedException,
        string $expectedExceptionMessage,
    ): void {
        $this->checkStoreExistsQry->method('check')->willReturn($storeExists);

        if ($bagData) {
            $this->request->attributes->set('customerBag', new CustomerBag(...$bagData));
        }

        if ($expectedException) {
            $this->expectException($expectedException);
            $this->expectExceptionMessageMatches('/^' . preg_quote($expectedExceptionMessage, '/') . '$/');
        }

        if (! $expectedException) {
            $this->assertDatabaseMissing(Customer::class, ['id' => $bagData[0], 'selected_store' => $storeId]);
        }

        $result = $this->sut->handle(new ChangeStoreRequestDto($storeId));

        $this->assertDatabaseHas(Customer::class, ['id' => $bagData[0], 'selected_store' => $storeId]);
        $this->assertEquals('new token', $result->token);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'invalid-store-id' => [
                'storeExists' => false,
                'bagData' => null,
                'storeId' => 0,
                'expectedException' => AppValidationException::class,
                'expectedExceptionMessage' => 'Invalid store ID.',
            ],
            'not-authenticated' => [
                'storeExists' => true,
                'bagData' => null,
                'storeId' => 0,
                'expectedException' => AppNeverException::class,
                'expectedExceptionMessage' => 'Customer is not authenticated.',
            ],
            'customer-not-found' => [
                'storeExists' => true,
                'bagData' => [15, 5],
                'storeId' => 7,
                'expectedException' => AppNeverException::class,
                'expectedExceptionMessage' => 'Customer not found.',
            ],
            'success' => [
                'storeExists' => true,
                'bagData' => [self::CUSTOMER_ID, 5],
                'storeId' => 7,
                'expectedException' => '',
                'expectedExceptionMessage' => '',
            ],
        ];
    }

    private function prepareDb(): void
    {
        Customer::forceCreate(['id' => self::CUSTOMER_ID, 'name' => 'name']);
    }
}
