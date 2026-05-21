<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\Offer\Action\Query;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Module\Customer\Action\Query\GetCurrentCustomerStoreIdQry;
use Module\Offer\Action\Dto\ListParamDto;
use Module\Offer\Action\Dto\OfferShortDto;
use Module\Offer\Action\Query\GetOfferListQry;
use Module\Offer\Model\Offer;
use Module\Product\Action\Query\JoinProductByIdSqlQry;
use Module\Product\Model\Product;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetOfferListQryTest extends TestCase
{
    use DatabaseTransactions;

    private GetOfferListQry $sut;

    private const int STORE_ID = 10;

    private const int STORE2_ID = 11;

    protected function setUp(): void
    {
        parent::setUp();

        $getCurrentCustomerStoreIdQry = $this->createStub(GetCurrentCustomerStoreIdQry::class);
        $getCurrentCustomerStoreIdQry->method('handle')->willReturn(self::STORE_ID);

        $this->sut = new GetOfferListQry(
            $getCurrentCustomerStoreIdQry,
            resolve(JoinProductByIdSqlQry::class),
        );
        $this->prepareDb();
    }

    /**
     * @param  OfferShortDto[]  $expectedItems
     */
    #[Test, DataProvider('dataProviderHandle')]
    public function handle(
        ?int $page,
        array $expectedItems,
        int $expectedPage
    ): void {

        $result = $this->sut->handle(new ListParamDto($page));

        $this->assertEquals($expectedPage, $result->page);
        $this->assertEquals($expectedItems, $result->items);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function dataProviderHandle(): array
    {
        return [
            'default first page' => [
                'page' => null,
                'expectedItems' => [
                    new OfferShortDto(20, 'ean-1', 'Red square', 10000, 12000),
                    new OfferShortDto(23, 'ean-3', 'product 3', 10003, null),
                ],
                'expectedPage' => 1,
            ],
            'first page' => [
                'page' => 1,
                'expectedItems' => [
                    new OfferShortDto(20, 'ean-1', 'Red square', 10000, 12000),
                    new OfferShortDto(23, 'ean-3', 'product 3', 10003, null),
                ],
                'expectedPage' => 1,
            ],
            'second page' => [
                'page' => 2,
                'expectedItems' => [],
                'expectedPage' => 2,
            ],
            'negative page' => [
                'page' => -1,
                'expectedItems' => [
                    new OfferShortDto(20, 'ean-1', 'Red square', 10000, 12000),
                    new OfferShortDto(23, 'ean-3', 'product 3', 10003, null),
                ],
                'expectedPage' => 1,
            ],
        ];
    }

    private function prepareDb(): void
    {
        Offer::forceCreate(['id' => 20, 'version' => 2, 'store_id' => self::STORE_ID, 'external_id' => 'ex-1', 'product_ean' => 'ean-1',
            'product_name' => null, 'price' => 10000, 'lowest_price' => 12000, 'visible' => true, 'product_id' => 50]);
        Offer::forceCreate(['id' => 21, 'version' => 2, 'store_id' => self::STORE_ID, 'external_id' => 'ex-2', 'product_ean' => 'ean-2',
            'product_name' => 'product 2', 'price' => 10001, 'lowest_price' => 12001, 'visible' => false, 'product_id' => 51]);
        Offer::forceCreate(['id' => 23, 'version' => 2, 'store_id' => self::STORE_ID, 'external_id' => 'ex-3', 'product_ean' => 'ean-3',
            'product_name' => 'product 3', 'price' => 10003, 'lowest_price' => null, 'visible' => true, 'product_id' => null]);
        Offer::forceCreate(['id' => 24, 'version' => 3, 'store_id' => self::STORE2_ID, 'external_id' => 'ex-24', 'product_ean' => 'ean-1',
            'product_name' => 'product 1', 'price' => 10000, 'lowest_price' => 12000, 'visible' => true, 'product_id' => 50]);

        Product::forceCreate(['id' => 50, 'ean' => 'ean-1', 'name' => 'Red square', 'image_url' => 'red-square.png']);
        Product::forceCreate(['id' => 51, 'ean' => 'ean-2', 'name' => 'Red circle', 'image_url' => 'red-circle.png']);
    }
}
