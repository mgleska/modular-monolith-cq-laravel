<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Customer\Action\Query;

use Illuminate\Http\Request;
use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Customer\Support\CustomerBag;
use Module\Shared\Exception\AppNeverException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetCurrentCustomerStoreIdQryTest extends TestCase
{
    private GetCurrentCustomerStoreIdQry $sut;

    private Request $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = resolve(Request::class);

        $this->sut = new GetCurrentCustomerStoreIdQry(
            $this->request,
        );
    }

    /**
     * @param  array{int, int}|null  $bagData
     */
    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        ?array $bagData,
        int $expected,
        string $exceptionMessage
    ): void {
        if ($bagData) {
            $this->request->attributes->set('customerBag', new CustomerBag(...$bagData));
        }

        if ($exceptionMessage) {
            $this->expectException(AppNeverException::class);
            $this->expectExceptionMessageMatches('/^' . preg_quote($exceptionMessage, '/') . '/');
        }

        $result = $this->sut->handle();

        $this->assertSame($expected, $result);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'unauthenticated' => [
                'bagData' => null,
                'expected' => 0,
                'exceptionMessage' => 'Customer is not authenticated.',
            ],
            'valid' => [
                'bagData' => [5, 15],
                'expected' => 15,
                'exceptionMessage' => '',
            ],
        ];
    }
}
