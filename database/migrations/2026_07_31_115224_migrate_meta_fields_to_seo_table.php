<?php

use App\Models\Category;
use App\Models\Page;
use App\Models\Trip;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_TYPES = [
        'pages' => Page::class,
        'categories' => Category::class,
        'trips' => Trip::class,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::TABLE_TYPES as $table => $type) {
            DB::table($table)->select('id', 'meta_title', 'meta_description')->orderBy('id')
                ->each(function (object $record) use ($type): void {
                    if (blank($record->meta_title) && blank($record->meta_description)) {
                        return;
                    }

                    DB::table('seos')->insert([
                        'seoable_type' => $type,
                        'seoable_id' => $record->id,
                        'meta_title' => $record->meta_title,
                        'meta_description' => $record->meta_description,
                        'robots_index' => true,
                        'robots_follow' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

            Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropColumn(['meta_title', 'meta_description']));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::TABLE_TYPES as $table => $type) {
            Schema::table($table, function (Blueprint $blueprint): void {
                $blueprint->string('meta_title')->nullable();
                $blueprint->string('meta_description')->nullable();
            });

            DB::table('seos')->where('seoable_type', $type)->orderBy('id')
                ->each(function (object $seo) use ($table): void {
                    DB::table($table)->where('id', $seo->seoable_id)->update([
                        'meta_title' => $seo->meta_title,
                        'meta_description' => $seo->meta_description,
                    ]);
                });

            DB::table('seos')->where('seoable_type', $type)->delete();
        }
    }
};
