<?php

declare(strict_types=1);

namespace Module\Shared\SqlDto;

use Closure;
use Module\Shared\Exception\AppNeverException;

class JoinSqlDto
{
    /**
     * @param  array<string, string>  $providesColumns
     */
    public function __construct(
        public readonly string $joinTable,
        public readonly Closure $joinClause,
        public readonly array $providesColumns,
        public readonly string $table,
        public readonly string $builderName,
    ) {}

    /**
     * @param  array<string, string>  $required
     */
    public function confirmRequiredColumns(array $required): void
    {
        $diff = array_diff_assoc($required, $this->providesColumns);

        if (count($diff) > 0) {
            throw new AppNeverException("SQL column list supplied by builder $this->builderName do not contain all required columns. Missing columns: " .
                implode(', ', array_keys($diff)));
        }
    }

    public function column(string $column, string $as = ''): string
    {
        return $this->table . '.' . $column . ($as ? " AS $as" : '');
    }
}
