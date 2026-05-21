<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace Tests\Offer\Access\Controller;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Laravel\Sanctum\Sanctum;
use Module\Offer\Access\Controller\OfferAdminController;
use Module\Offer\Action\Command\ChangeVisibilityCmd;
use Module\Offer\Action\Query\AdminGetListFiltersQry;
use Module\Offer\Action\Query\AdminGetOfferDetailsQry;
use Module\Offer\Action\Query\AdminGetOfferListQry;
use Module\User\Model\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(OfferAdminController::class)]
class AdminOfferControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @param  class-string  $action
     * @param  array<string, mixed>  $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function blockAccessWithoutValidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->instance($action, $mock);

        $response = $this->withoutToken()->json($method, '/api' . $path, $dto);
        $response->assertUnauthorized();
    }

    /**
     * @param  class-string  $action
     * @param  array<string, mixed>  $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function blockAccessWithInvalidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->instance($action, $mock);

        $response = $this->withToken('BAD_TOKEN')->json($method, '/api' . $path, $dto);
        $response->assertUnauthorized();
    }

    /**
     * @param  class-string  $action
     * @param  array<string, mixed>  $dto
     */
    #[Test, DataProvider('dataProviderEndpoints')]
    public function allowWithValidToken(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $mock = $this->createStub($action);
        $this->instance($action, $mock);

        $response = $this->json($method, '/api' . $path, $dto);

        $this->assertEquals($expected, $response->getStatusCode());
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderEndpoints(): array
    {
        return [
            [
                'method' => 'get',
                'path' => '/admin/offer/filters',
                'action' => AdminGetListFiltersQry::class,
                'dto' => [],
                'expected' => 200,
            ],
            [
                'method' => 'get',
                'path' => '/admin/offer/list',
                'action' => AdminGetOfferListQry::class,
                'dto' => ['page' => 1],
                'expected' => 200,
            ],
            [
                'method' => 'get',
                'path' => '/admin/offer/5',
                'action' => AdminGetOfferDetailsQry::class,
                'dto' => [],
                'expected' => 200,
            ],
            [
                'method' => 'post',
                'path' => '/admin/offer/change-visibility',
                'action' => ChangeVisibilityCmd::class,
                'dto' => ['id' => 5, 'version' => 2, 'visible' => false],
                'expected' => 200,
            ],
        ];
    }
}
