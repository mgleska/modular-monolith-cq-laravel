<?php

declare(strict_types=1);

namespace Module\Customer\Support;

use DateTime;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Config\Repository as Config;
use Module\Shared\Exception\AppNeverException;
use Module\Shared\Exception\AppValidationException;

use function str_starts_with;

class TokenService
{
    private const string TYPE_ACCESS = 'acc';

    // private const string TYPE_REFRESH = 'rfr';

    private const string ALGORITHM = 'HS256';

    public function __construct(
        private readonly Config $config,
    ) {}

    public function newAccessToken(int $customerId, int $storeId): string
    {
        $payload = [
            'sub' => self::TYPE_ACCESS,
            'uid' => $customerId,
            'stid' => $storeId,
            'exp' => (new DateTime)->getTimestamp() + $this->config->get('mm-cq.access-token-ttl'),
        ];

        return JWT::encode($payload, $this->getKey(), self::ALGORITHM);
    }

    /**
     * @return array<string, string|int>
     */
    public function decodeAccessToken(string $jwt): array
    {
        $decoded = (array)JWT::decode($jwt, new Key($this->getKey(), self::ALGORITHM));
        if (($decoded['sub'] ?? '') !== self::TYPE_ACCESS) {
            throw AppValidationException::withMessages(['jwt.sub' => 'JWT token is not access token.']);
        }

        return $decoded;
    }

    private function getKey(): string
    {
        $key = $this->config->get('app.key');
        if (! str_starts_with($key, 'base64:')) {
            throw new AppNeverException('Not found configuration parameter "app.key"');
        }

        return base64_decode(substr($key, 7));
    }
}
