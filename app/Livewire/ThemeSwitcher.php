<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class ThemeSwitcher extends Component
{
    public string $theme = 'light';

    public function mount(): void
    {
        $this->theme = session('theme', 'light');
    }

    public function updatedTheme(string $value): void
    {
        session(['theme' => $value]);
    }

    public function render(): View
    {
        return view('livewire.theme-switcher');
    }
}
