<?php
declare(strict_types=1);

namespace Module\Shared\Attribute;

use Abrha\LaravelDataDocs\Pipeline\PipelineFactory;
use Abrha\LaravelDataDocs\Services\ParameterGenerator;
use Attribute;
use Illuminate\Support\Arr;
use Knuckles\Scribe\Attributes\Response;
use Spatie\LaravelData\Support\DataConfig;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_FUNCTION | Attribute::TARGET_METHOD)]
class ResponseDoc extends Response
{
    /**
     * @param  class-string  $dtoClass
     */
    public function __construct(string $dtoClass, int $status = 200, ?string $description = '')
    {
        $parameterGenerator = new ParameterGenerator(
            PipelineFactory::createDefault(config('data-docs', [])),
            app(DataConfig::class)
        );

        $result = $this->convert(array_map(fn ($param) => $param->toArray(), $parameterGenerator($dtoClass)));

        parent::__construct($result, $status, $description);
    }

    /**
     * @param  array<string, array<string, int|float|bool|string>>  $data
     * @return array<string, int|float|bool|string|array<string, mixed>>
     */
    private function convert(array $data): array
    {
        $result = [];

        foreach (Arr::undot($data) as $key => $value) {
            if (str_ends_with($key, '[]')) {
                $result[substr($key, 0, -2)] = [$this->convert($value)];

                continue;
            }
            if ($value['type'] === 'object[]') {
                continue;
            }
            $result[$key] = $value['example'];
        }

        return $result;
    }
}
