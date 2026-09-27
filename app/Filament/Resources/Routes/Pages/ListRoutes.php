<?php

namespace App\Filament\Resources\Routes\Pages;

use App\Filament\Resources\Routes\RouteResource;
use App\Models\Route;
use App\Services\RouteGenerator;
use Closure;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListRoutes extends ListRecords
{
    protected static string $resource = RouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Add redirect')
                ->modalHeading('Add redirect')
                ->schema([
                    TextInput::make('full_path')
                        ->label('Old URL')
                        ->helperText('Old path or full URL, e.g. /everest-trek.html')
                        ->required()
                        ->rules([
                            fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                $path = self::normalizePath($value);

                                if ($path === '') {
                                    $fail('The homepage cannot be redirected.');
                                } elseif (RouteGenerator::isReserved(Str::before($path, '/'))) {
                                    $fail('This path is reserved.');
                                } elseif (Route::where('full_path', $path)->exists()) {
                                    $fail("/{$path} already exists in Routes.");
                                }
                            },
                        ]),
                    TextInput::make('redirect_to')
                        ->label('New URL')
                        ->helperText('Path on this site (e.g. /trips/everest-base-camp) or a full URL.')
                        ->required(),
                    Select::make('redirect_code')
                        ->options([
                            301 => '301 (Permanent)',
                            302 => '302 (Temporary)',
                        ])
                        ->default(301)
                        ->required(),
                ])
                ->mutateDataUsing(function (array $data): array {
                    $path = self::normalizePath($data['full_path']);

                    return [
                        ...$data,
                        'full_path' => $path,
                        'slug' => Str::afterLast($path, '/'),
                        'type' => 'redirect',
                        'is_active' => true,
                    ];
                }),
        ];
    }

    /**
     * Strip scheme/host/query so "https://old.com/foo/?x=1" becomes "foo",
     * matching how RouteResolver looks paths up.
     */
    private static function normalizePath(string $value): string
    {
        return trim(rawurldecode(parse_url(trim($value), PHP_URL_PATH) ?? ''), '/');
    }
}
