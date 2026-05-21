<?php
/** @noinspection PhpUnhandledExceptionInspection */
declare(strict_types=1);

namespace Tests\User\Access\Controller;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Module\User\Access\Controller\UserAdminController;
use Module\User\Action\Command\AdminLoginCmd;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(UserAdminController::class)]
class UserAdminControllerTest extends TestCase
{
    use DatabaseTransactions;

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
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function dataProviderUnauthenticatedEndpoints(): array
    {
        return [
            [
                'method' => 'post',
                'path' => '/admin/user/login',
                'action' => AdminLoginCmd::class,
                'dto' => ['email' => 'user@my.company.com'],
                'expected' => 200,
            ],
        ];
    }
}
