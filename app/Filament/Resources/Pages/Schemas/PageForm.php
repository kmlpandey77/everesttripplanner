<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Enums\PageStatus;
use App\Filament\Schemas\SeoForm;
use App\Models\Category;
use App\Services\RouteGenerator;
use Awcodes\Curator\Components\Forms\CuratorPicker;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state ?? '')))
                            ->columnSpan(1),
                        TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->rule(fn() => fn(string $attribute, $value, $fail) => RouteGenerator::isReserved($value)
                                ? $fail('This slug is reserved and cannot be used.')
                                : null)
                            ->helperText(fn(Get $get): ?string => $get('is_home')
                                ? 'Ignored while this page is set as the homepage — it will be served at / instead.'
                                : null)
                            ->columnSpan(1),
                        Select::make('parent_id')
                            ->label('Parent page')
                            ->relationship(
                                'parent',
                                'title',
                                modifyQueryUsing: fn ($query, $record) => $record ? $query->whereKeyNot($record->id) : $query,
                            )
                            ->searchable()
                            ->preload()
                            ->columnSpan(1),
                        Select::make('status')
                            ->options(PageStatus::class)
                            ->default(PageStatus::Draft)
                            ->required()
                            ->live()
                            ->columnSpan(1),
                        DateTimePicker::make('published_at')
                            ->visible(fn(Get $get): bool => $get('status') === PageStatus::Scheduled->value)
                            ->columnSpan(1),
                        Toggle::make('is_home')
                            ->label('Set as homepage')
                            ->helperText(
                                'Serves this page at the site root (/). Turning this on unsets it on any other page.'
                            )
                            ->live()
                            ->columnSpanFull(),
                    ]),
                ...SeoForm::components(),
                Section::make('Content')
                    ->schema([
                        Builder::make('content')
                            ->label(false)
                            ->blockNumbers(false)
                            ->blocks([
                                Block::make('hero')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->required()
                                            ->columnSpanFull(),
                                        Textarea::make('subheading')
                                            ->rows(2)
                                            ->columnSpanFull(),
                                        CuratorPicker::make('media_id')
                                            ->label('Background image')
                                            ->columnSpanFull(),
                                        TextInput::make('primary_button_label'),
                                        TextInput::make('primary_button_url')
                                            ->label('Primary button URL')
                                            ->url(),
                                        TextInput::make('secondary_button_label'),
                                        TextInput::make('secondary_button_url')
                                            ->label('Secondary button URL')
                                            ->url(),
                                    ])
                                    ->columns(2),
                                Block::make('trust')
                                    ->label('Trust strip')
                                    ->schema([
                                        Repeater::make('items')
                                            ->schema([
                                                Select::make('icon')
                                                    ->options([
                                                        'heroicon-o-calendar-days' => 'Calendar (tenure)',
                                                        'heroicon-o-shield-check' => 'Shield (license/safety)',
                                                        'heroicon-o-user-group' => 'User group (membership)',
                                                        'heroicon-o-identification' => 'ID card (registration)',
                                                        'heroicon-o-check-badge' => 'Badge (certification)',
                                                        'heroicon-o-globe-asia-australia' => 'Globe (local presence)',
                                                    ])
                                                    ->default('heroicon-o-check-badge')
                                                    ->required(),
                                                TextInput::make('label')
                                                    ->required(),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Add credential')
                                            ->collapsible(),
                                    ]),
                                Block::make('contact')
                                    ->label('Contact card')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('Call or message us directly')
                                            ->helperText(
                                                'Phone and WhatsApp numbers are pulled from Settings automatically.'
                                            ),
                                    ]),
                                Block::make('featured_trips')
                                    ->label('Featured trips')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('Featured Trips'),
                                        TextInput::make('count')
                                            ->label('Number of trips')
                                            ->numeric()
                                            ->default(6),
                                        Select::make('category_slug')
                                            ->label('Limit to category')
                                            ->options(fn() => Category::query()->orderBy('name')->pluck('name', 'slug'))
                                            ->searchable()
                                            ->helperText('Leave blank to show trips from any category.'),
                                        Select::make('columns')
                                            ->label('Display columns')
                                            ->options([
                                                2 => '2 columns',
                                                3 => '3 columns',
                                                4 => '4 columns',
                                            ])
                                            ->default(3)
                                            ->required(),
                                    ])
                                    ->columns(4),
                                Block::make('blog_index')
                                    ->label('Blog index')
                                    ->schema([
                                        TextInput::make('count')
                                            ->label('Number of posts')
                                            ->numeric()
                                            ->default(12),
                                    ]),
                                Block::make('features')
                                    ->label('Feature list')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('Why trek with us'),
                                        Repeater::make('items')
                                            ->schema([
                                                Select::make('icon')
                                                    ->options([
                                                        'heroicon-o-map' => 'Map',
                                                        'heroicon-o-user-group' => 'Guides',
                                                        'heroicon-o-shield-check' => 'Safety',
                                                        'heroicon-o-calendar-days' => 'Flexible dates',
                                                        'heroicon-o-currency-dollar' => 'Pricing',
                                                        'heroicon-o-heart' => 'Care',
                                                        'heroicon-o-globe-asia-australia' => 'Local knowledge',
                                                        'heroicon-o-check-badge' => 'Trust',
                                                    ])
                                                    ->default('heroicon-o-check-badge')
                                                    ->required()
                                                    ->columnSpanFull(),
                                                TextInput::make('title')
                                                    ->required(),
                                                Textarea::make('description')
                                                    ->rows(2),
                                            ])
                                            ->addActionLabel('Add point')
                                            ->collapsible(),
                                    ]),
                                Block::make('testimonials')
                                    ->label('Testimonials')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('What Trekkers Say'),
                                        Repeater::make('items')
                                            ->schema([
                                                CuratorPicker::make('avatar_id')
                                                    ->label('Photo (optional)'),
                                                TextInput::make('name')
                                                    ->required(),
                                                TextInput::make('role')
                                                    ->label('Trek / date (optional)')
                                                    ->helperText('e.g. "Everest Base Camp Trek, 2024"'),
                                                Textarea::make('quote')
                                                    ->rows(3)
                                                    ->required(),
                                                Select::make('rating')
                                                    ->label('Rating (optional)')
                                                    ->options([
                                                        5 => '5 stars',
                                                        4 => '4 stars',
                                                        3 => '3 stars',
                                                        2 => '2 stars',
                                                        1 => '1 star',
                                                    ])
                                                    ->helperText('Leave blank to hide the star rating for this quote.'),
                                            ])
                                            ->columns(2)
                                            ->addActionLabel('Add testimonial')
                                            ->collapsible(),
                                    ]),
                                Block::make('featured_reviews')
                                    ->label('Featured reviews')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('What Trekkers Say'),
                                        TextInput::make('count')
                                            ->label('Number of reviews')
                                            ->numeric()
                                            ->default(6),
                                    ])
                                    ->columns(2),
                                Block::make('faq')
                                    ->label('FAQ')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('Frequently Asked Questions'),
                                        Repeater::make('items')
                                            ->schema([
                                                TextInput::make('question')
                                                    ->required(),
                                                Textarea::make('answer')
                                                    ->rows(2)
                                                    ->required(),
                                            ])
                                            ->addActionLabel('Add question')
                                            ->collapsible(),
                                    ]),
                                Block::make('heading')
                                    ->schema([
                                        TextInput::make('text')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                            ])
                                            ->default('h2')
                                            ->required(),
                                        Select::make('width')
                                            ->options([
                                                'container' => 'Container (max width)',
                                                'full' => 'Full width',
                                            ])
                                            ->default('container')
                                            ->required(),
                                        Select::make('align')
                                            ->label('Text align')
                                            ->options([
                                                'left' => 'Left',
                                                'center' => 'Center',
                                                'right' => 'Right',
                                            ])
                                            ->default('left')
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Block::make('text')
                                    ->schema([
                                        RichEditor::make('content')
                                            ->label('Content')
                                            ->required()
                                            ->columnSpanFull(),
                                        Select::make('width')
                                            ->options([
                                                'container' => 'Container (max width)',
                                                'full' => 'Full width',
                                            ])
                                            ->default('container')
                                            ->required(),
                                        Select::make('align')
                                            ->label('Text align')
                                            ->options([
                                                'left' => 'Left',
                                                'center' => 'Center',
                                                'right' => 'Right',
                                            ])
                                            ->default('left')
                                            ->required(),
                                        CuratorPicker::make('media_id')
                                            ->label('Image (optional)'),
                                        Select::make('image_position')
                                            ->label('Image position')
                                            ->options([
                                                'top' => 'Top',
                                                'right' => 'Right',
                                                'bottom' => 'Bottom',
                                                'left' => 'Left',
                                            ])
                                            ->default('top')
                                            ->visible(fn (Get $get) => filled($get('media_id'))),
                                        Select::make('image_fit')
                                            ->label('Image fit')
                                            ->options([
                                                'cover' => 'Cover (crop to fill)',
                                                'fit' => 'Fit (no crop, letterboxed)',
                                                'full' => 'Full (natural size)',
                                            ])
                                            ->default('cover')
                                            ->visible(fn (Get $get) => filled($get('media_id'))),
                                        TextInput::make('alt')
                                            ->label('Alt text override')
                                            ->helperText('Falls back to the media\'s own alt text if left blank.')
                                            ->visible(fn (Get $get) => filled($get('media_id'))),
                                    ])
                                    ->columns(2),
                                Block::make('image')
                                    ->schema([
                                        CuratorPicker::make('media_id')
                                            ->label('Image')
                                            ->required(),
                                        TextInput::make('alt')
                                            ->label('Alt text override')
                                            ->helperText('Falls back to the media\'s own alt text if left blank.'),
                                    ]),
                                Block::make('enquiry_form')
                                    ->label('Enquiry form')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->default('Send Us Your Enquiry'),
                                        Textarea::make('description')
                                            ->rows(2),
                                    ]),
                                Block::make('cta')
                                    ->label('Call to action')
                                    ->schema([
                                        TextInput::make('heading')
                                            ->required(),
                                        TextInput::make('button_label')
                                            ->label('Button label')
                                            ->required(),
                                        TextInput::make('button_url')
                                            ->label('Button URL')
                                            ->url()
                                            ->required(),
                                    ])
                                    ->columns(3),
                                Block::make('html')
                                    ->label('Custom HTML')
                                    ->schema([
                                        Textarea::make('html')
                                            ->label(false)
                                            ->rows(6)
                                            ->required(),
                                    ]),
                            ])
                            ->addActionLabel('Add block')
                            ->collapsible()
                            ->reorderable()
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
