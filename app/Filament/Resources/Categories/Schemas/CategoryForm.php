<?php

namespace App\Filament\Resources\Categories\Schemas;

use App\Filament\Schemas\SeoForm;
use App\Services\RouteGenerator;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Category')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(1),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule(fn () => fn (string $attribute, $value, $fail) => RouteGenerator::isReserved($value)
                                ? $fail('This slug is reserved and cannot be used.')
                                : null)
                            ->columnSpan(1),
                        Select::make('parent_id')
                            ->label('Parent category')
                            ->relationship(
                                'parent',
                                'name',
                                modifyQueryUsing: fn ($query, $record) => $record ? $query->whereKeyNot($record->id) : $query,
                            )
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                    ]),
                ...SeoForm::components(),
            ]);
    }
}
