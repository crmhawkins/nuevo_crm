<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->tableExists('clients') || ! $this->tableExists('clients_phones')) {
            return;
        }

        $parentColumn = $this->getColumn('clients', 'id');
        $childColumn = $this->getColumn('clients_phones', 'client_id');

        if (! $parentColumn || ! $childColumn) {
            return;
        }

        $foreignKeyName = 'clients_phones_client_id_foreign';

        if ($this->foreignKeyExists('clients_phones', $foreignKeyName)) {
            DB::statement("ALTER TABLE `clients_phones` DROP FOREIGN KEY `{$foreignKeyName}`");
        }

        $parentDefinition = $this->buildIntegerDefinition($parentColumn);
        $childDefinition = $this->buildIntegerDefinition($childColumn);

        if (! $parentDefinition || ! $childDefinition) {
            return;
        }

        if (strtolower($parentDefinition) !== strtolower($childDefinition)) {
            $maxClientId = DB::table('clients_phones')->max('client_id');

            if ($this->wouldOverflow($parentColumn, $maxClientId)) {
                throw new RuntimeException('No se puede ajustar clients_phones.client_id al tipo de clients.id porque hay valores fuera de rango.');
            }

            $nullability = $childColumn->IS_NULLABLE === 'YES' ? 'NULL' : 'NOT NULL';

            DB::statement("ALTER TABLE `clients_phones` MODIFY `client_id` {$parentDefinition} {$nullability}");
        }

        $orphans = DB::table('clients_phones as client_phone')
            ->leftJoin('clients as client', 'client.id', '=', 'client_phone.client_id')
            ->whereNotNull('client_phone.client_id')
            ->whereNull('client.id')
            ->count();

        if ($orphans > 0) {
            Log::warning('No se ha recreado la FK clients_phones_client_id_foreign porque existen registros huérfanos en clients_phones.', [
                'orphans' => $orphans,
            ]);

            return;
        }

        if (! $this->foreignKeyExists('clients_phones', $foreignKeyName)) {
            DB::statement('ALTER TABLE `clients_phones` ADD CONSTRAINT `clients_phones_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`)');
        }
    }

    public function down(): void
    {
        if (! $this->tableExists('clients_phones')) {
            return;
        }

        $foreignKeyName = 'clients_phones_client_id_foreign';

        if ($this->foreignKeyExists('clients_phones', $foreignKeyName)) {
            DB::statement("ALTER TABLE `clients_phones` DROP FOREIGN KEY `{$foreignKeyName}`");
        }
    }

    private function tableExists(string $table): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS total FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?',
            [$table],
        );

        return (int) ($result->total ?? 0) > 0;
    }

    private function getColumn(string $table, string $column): ?object
    {
        return DB::selectOne(
            'SELECT column_type, data_type, is_nullable, column_key, extra
            FROM information_schema.columns
            WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?',
            [$table, $column],
        );
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $result = DB::selectOne(
            'SELECT COUNT(*) AS total
            FROM information_schema.table_constraints
            WHERE table_schema = DATABASE()
                AND table_name = ?
                AND constraint_type = ?
                AND constraint_name = ?',
            [$table, 'FOREIGN KEY', $constraintName],
        );

        return (int) ($result->total ?? 0) > 0;
    }

    private function buildIntegerDefinition(object $column): ?string
    {
        $columnType = strtolower((string) ($column->column_type ?? ''));

        if (str_contains($columnType, 'bigint')) {
            return str_contains($columnType, 'unsigned') ? 'BIGINT UNSIGNED' : 'BIGINT';
        }

        if (preg_match('/\bint\b/', $columnType) === 1) {
            return str_contains($columnType, 'unsigned') ? 'INT UNSIGNED' : 'INT';
        }

        return null;
    }

    private function wouldOverflow(object $targetColumn, int|string|null $maxValue): bool
    {
        if ($maxValue === null) {
            return false;
        }

        $maxValue = (int) $maxValue;
        $columnType = strtolower((string) ($targetColumn->column_type ?? ''));

        if (str_contains($columnType, 'unsigned') && str_contains($columnType, 'int') && ! str_contains($columnType, 'bigint')) {
            return $maxValue > 4294967295;
        }

        if (! str_contains($columnType, 'unsigned') && str_contains($columnType, 'int') && ! str_contains($columnType, 'bigint')) {
            return $maxValue > 2147483647;
        }

        return false;
    }
};
