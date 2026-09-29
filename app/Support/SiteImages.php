<?php

declare(strict_types=1);

namespace App\Support;

use FilamentCraft\Media\MediaLibrary;
use FilamentCraft\Models\Site;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class SiteImages
{
    /** @var array<string, string> */
    public const ALT = [
        'throwing' => 'A potter centring clay on the wheel, hands wet with slip',
        'trimming' => 'Trimming the foot of a bowl on the wheel',
        'kiln-glow' => 'Cups glowing orange between the kiln bricks at the end of a firing',
        'studio-shelves' => 'Studio shelves stacked with pots waiting for glaze',
        'greenware' => 'Rows of freshly thrown cups drying on boards',
        'drying-bowls' => 'Bowls drying upside down on a ware board in low light',
        'grey-vessels' => 'A group of ash-glazed jugs, bottles and bowls',
        'vase-row' => 'A row of vases in cream, oat and speckled glazes',
        'green-set' => 'A celadon plate, bowl and cups laid out from above',
    ];

    private const FOLDER = 'kiln-street';

    public static function path(string $name): string
    {
        return trim((string) config('filamentcraft.uploads.directory'), '/').'/'.self::FOLDER.'/'.$name.'.jpg';
    }

    public static function publish(): void
    {
        $disk = Storage::disk(MediaLibrary::uploadsDisk());

        foreach (array_keys(self::ALT) as $name) {
            if (! $disk->exists(self::path($name))) {
                $disk->put(self::path($name), File::get(resource_path("images/{$name}.jpg")));
            }
        }
    }

    public static function register(Site $site): void
    {
        $library = app(MediaLibrary::class);

        foreach (self::ALT as $name => $alt) {
            $library->register($site, self::path($name), name: "{$name}.jpg", mime: 'image/jpeg')
                ?->update(['title' => Str::headline($name), 'alt' => $alt]);
        }
    }
}
