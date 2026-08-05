<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->components([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('roles')
                            ->label('Role')
                            ->relationship('roles', 'name')
                            ->options(fn (): array => Role::pluck('name', 'id')->all())
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpanFull(),
                        Select::make('vendor_id')
                            ->label('Perusahaan Vendor (opsional)')
                            ->relationship('vendor', 'nama_vendor')
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                TextInput::make('nama_vendor')
                                    ->label('Nama Vendor')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('kontak')
                                    ->maxLength(255),
                                TextInput::make('telepon')
                                    ->maxLength(255),
                            ]),
                        TextInput::make('password')
                            ->label('Kata Sandi')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::defaults())
                            ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                            ->helperText('Kosongkan jika tidak ingin mengubah kata sandi.'),
                    ]),
            ]);
    }
}
