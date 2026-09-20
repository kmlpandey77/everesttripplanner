<?php

namespace App\Filament\Resources\Blogs\Schemas;

use App\Enums\PageStatus;
use App\Filament\Schemas\SeoForm;
use App\Services\RouteGenerator;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class BlogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Post')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
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
                        Select::make('status')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        DateTimePicker::make('published_at')
                            ->visible(fn (Get $get): bool => $get('status') === PageStatus::Scheduled->value)
                            ->columnSpan(1),
                        CuratorPicker::make('image_id')
                            ->label('Cover image')
                            ->columnSpanFull(),
                        Textarea::make('excerpt')
                            ->helperText('Shown on the blog index and used as the fallback meta description.')
                            ->rows(2)
                            ->columnSpanFull(),
                        RichEditor::make('content')
                            ->label(false)
                            ->columnSpanFull(),
                    ]),
                ...SeoForm::components(),
            ]);
    }
}
