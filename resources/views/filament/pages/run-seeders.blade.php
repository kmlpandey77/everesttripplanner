<x-filament-panels::page>
    <x-filament::section heading="Available seeders" description="Use “Run a seeder” above to apply one to this database.">
        <ul class="divide-y divide-gray-200 dark:divide-white/10">
            @foreach (array_keys($this->seeders()) as $name)
                <li class="py-2 text-sm font-medium text-gray-950 dark:text-white">{{ $name }}</li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-panels::page>
