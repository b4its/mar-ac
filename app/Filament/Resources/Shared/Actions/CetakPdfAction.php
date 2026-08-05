<?php

namespace App\Filament\Resources\Shared\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;

class CetakPdfAction extends Action
{
    protected ?Closure $previewUrl = null;

    public static function getDefaultName(): ?string
    {
        return 'cetak-pdf';
    }

    public function previewUrl(Closure $url): static
    {
        $this->previewUrl = $url;

        return $this;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Cetak PDF')
            ->icon(Heroicon::OutlinedPrinter)
            ->color('gray')
            ->url(fn ($record): string => ($this->previewUrl)($record))
            ->openUrlInNewTab();
    }
}
