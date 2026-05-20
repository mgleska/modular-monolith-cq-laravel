<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Command;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Action\Dto\LoginRequestDto;
use Module\Customer\Action\Enum\CustomerStatusEnum;
use Module\Customer\Model\Customer;
use Module\Customer\Support\TokenService;
use Module\Shared\Exception\AppValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginCmdTest extends TestCase
{
    use DatabaseTransactions;

    private LoginCmd $sut;

    protected function setUp(): void
    {
        parent::setUp();

        $tokenService = $this->createStub(TokenService::class);
        $tokenService->method('newAccessToken')->willReturn('new token');

        $this->sut = new LoginCmd(
            $tokenService
        );
        $this->prepareDb();
    }

    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        int $customerId,
        string $expectedExceptionMessage,
    ): void {
        if ($expectedExceptionMessage) {
            $this->expectException(AppValidationException::class);
            $this->expectExceptionMessageMatches('/^' . preg_quote($expectedExceptionMessage, '/') . '$/');
        }

        $result = $this->sut->handle(new LoginRequestDto($customerId));

        $this->assertDatabaseHas(Customer::class, ['id' => $customerId, 'status' => CustomerStatusEnum::ACTIVE->value]);
        $this->assertEquals('new token', $result->token);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'new' => [
                'customerId' => 5,
                'expectedExceptionMessage' => '',
            ],
            'active' => [
                'customerId' => 10,
                'expectedExceptionMessage' => '',
            ],
            'inactive' => [
                'customerId' => 11,
                'expectedExceptionMessage' => 'Customer is inactive.',
            ],
            'deactivating->active' => [
                'customerId' => 12,
                'expectedExceptionMessage' => '',
            ],
        ];
    }

    private function prepareDb(): void
    {
        Customer::forceCreate(['id' => 10, 'name' => 'name 10', 'status' => CustomerStatusEnum::ACTIVE]);
        Customer::forceCreate(['id' => 11, 'name' => 'name 11', 'status' => CustomerStatusEnum::INACTIVE]);
        Customer::forceCreate(['id' => 12, 'name' => 'name 12', 'status' => CustomerStatusEnum::DEACTIVATING]);
    }
}
