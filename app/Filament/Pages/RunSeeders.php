<?php

namespace App\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Throwable;
use UnitEnum;

/**
 * Runs content seeders (blog posts, SEO fixes) from /admin, so shipping a post to production
 * doesn't need shell access. Every seeder in database/seeders is listed except the ones below.
 */
class RunSeeders extends Page
{
    /**
     * DatabaseSeeder creates a test user and EverestSeeder rebuilds the whole site, so neither belongs on production.
     */
    private const EXCLUDED = ['DatabaseSeeder', 'EverestSeeder'];

    protected string $view = 'filament.pages.run-seeders';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlayCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Run seeders';

    /**
     * @return array<string, class-string<Seeder>>
     */
    public function seeders(): array
    {
        return collect(glob(database_path('seeders/*.php')))
            ->mapWithKeys(fn (string $file): array => [basename($file, '.php') => 'Database\\Seeders\\'.basename($file, '.php')])
            ->except(self::EXCLUDED)
            ->filter(fn (string $class): bool => is_subclass_of($class, Seeder::class))
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('run')
                ->label('Run a seeder')
                ->icon(Heroicon::Play)
                ->schema([
                    Select::make('seeder')
                        ->options(fn (): array => array_combine(array_keys($this->seeders()), array_keys($this->seeders())))
                        ->required()
                        ->searchable(),
                ])
                ->modalDescription('This writes to the live database. Seeders here are safe to run more than once.')
                ->modalSubmitActionLabel('Run')
                ->action(function (array $data): void {
                    // Re-check against the list so a tampered request can't run an excluded seeder
                    $class = $this->seeders()[$data['seeder']] ?? null;

                    if ($class === null) {
                        Notification::make()->danger()->title('Unknown seeder')->send();

                        return;
                    }

                    try {
                        Artisan::call('db:seed', ['--class' => $class, '--force' => true]);
                    } catch (Throwable $exception) {
                        report($exception);
                        Notification::make()->danger()->title("{$data['seeder']} failed")->body($exception->getMessage())->persistent()->send();

                        return;
                    }

                    Notification::make()->success()->title("{$data['seeder']} ran successfully")->send();
                }),
        ];
    }
}
