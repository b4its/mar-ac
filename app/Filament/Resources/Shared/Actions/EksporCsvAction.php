<?php

namespace App\Filament\Resources\Shared\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EksporCsvAction extends Action
{
    protected ?Closure $query = null;

    public static function getDefaultName(): ?string
    {
        return 'ekspor-csv';
    }

    public function query(Closure $query): static
    {
        $this->query = $query;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Ekspor CSV')
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('gray')
            ->action(function (): StreamedResponse {
                return ($this->query)();
            });
    }
}
