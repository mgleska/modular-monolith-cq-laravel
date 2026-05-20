<?php
/** @noinspection PhpUnusedParameterInspection */
declare(strict_types=1);

namespace Tests\Customer\Access\Controller;

use Illuminate\Http\Request;
use Module\Customer\Access\Controller\CustomerController;
use Module\Customer\Action\Command\ChangeStoreCmd;
use Module\Customer\Action\Command\LoginCmd;
use Module\Customer\Support\CustomerBag;
use Module\Customer\Support\TokenService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CustomerController::class)]
class CustomerControllerTest extends TestCase
{
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
        $token = new TokenService(app('config'))->newAccessToken(1, 10);
        $mock = $this->createStub($action);
        $this->instance($action, $mock);

        $response = $this->withToken($token)->json($method, '/api' . $path, $dto);

        $this->assertEquals($expected, $response->getStatusCode());
        $bag = resolve(Request::class)->attributes->get('customerBag');
        $this->assertInstanceOf(CustomerBag::class, $bag);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/customer/change-store',
                'action' => ChangeStoreCmd::class,
                'dto' => ['storeId' => 20],
                'expected' => 200,
            ],
        ];
    }

    /**
     * @param  class-string  $action
     * @param  array<string, mixed>  $dto
     */
    #[Test, DataProvider('dataProviderUnauthenticatedEndpoints')]
    public function unauthenticated(
        string $method,
        string $path,
        string $action,
        array $dto,
        int $expected,
    ): void {
        $mock = $this->createStub($action);
        $this->instance($action, $mock);

        $response = $this->withoutToken()->json($method, '/api' . $path, $dto);

        $this->assertEquals($expected, $response->getStatusCode());
        $bag = resolve(Request::class)->attributes->get('customerBag');
        $this->assertNull($bag);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderUnauthenticatedEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/customer/login',
                'action' => LoginCmd::class,
                'dto' => ['customerId' => 2],
                'expected' => 200,
            ],
        ];
    }
}
