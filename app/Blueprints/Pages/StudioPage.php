<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use App\Blueprints\KilnStreetSite;
use FilamentCraft\Sections\Builtin\GallerySection;
use FilamentCraft\Sections\Builtin\ImageTextSection;
use FilamentCraft\Sections\Builtin\LocationsSection;
use FilamentCraft\Sections\Builtin\TimelineSection;

final class StudioPage extends Page
{
    public function name(): string
    {
        return 'The studio';
    }

    public function slug(): string
    {
        return 'studio';
    }

    public function description(): string
    {
        return 'How Kiln Street pots are thrown, glazed and fired, and when you can visit the studio in Bristol.';
    }

    public function sections(): array
    {
        return [
            $this->section(ImageTextSection::class, [
                'eyebrow' => 'The studio',
                'title' => 'Two wheels, one kiln and a shop window on Picton Street.',
                'body' => 'Kiln Street is Tom Hallett and Idris Bello. We met on an evening class in 2016, started selling mugs at the Saturday market two years later, and took the lease on a former bike shop in 2020. Everything we sell is made in the back room.',
                'url' => '/shop',
                'label' => 'Shop the pottery',
                'image' => $this->image('studio-shelves'),
                'media_placement' => 'end',
                'align' => 'start',
            ]),

            $this->section(TimelineSection::class, [
                'eyebrow' => '',
                'title' => 'How a mug is made.',
                'intro' => 'About three weeks from a bag of clay to a mug on the shelf, most of it spent waiting.',
                'style' => 'steps',
                'marker' => 'number',
                'align' => 'start',
            ], $this->blocks('step', [
                ['title' => 'Throw', 'description' => 'Around 450 g of stoneware clay, centred and pulled up on the wheel in a couple of minutes.'],
                ['title' => 'Trim and handle', 'description' => 'A day later, leather-hard, the foot is trimmed and a pulled handle is joined on.'],
                ['title' => 'Bisque', 'description' => 'A week drying, then a first firing to 1000 °C so it can take a glaze.'],
                ['title' => 'Glaze', 'description' => 'Dipped by hand in one of our six glazes, which we mix ourselves from raw materials.'],
                ['title' => 'Glaze firing', 'description' => 'A second firing to 1280 °C. The kiln takes two days to cool before we can open it.'],
            ])),

            $this->section(GallerySection::class, [
                'eyebrow' => '',
                'title' => 'In the back room.',
                'intro' => '',
                'layout' => 'masonry',
                'ratio' => 'adapt',
                'captions' => 'overlay',
                'align' => 'start',
            ], $this->blocks('image', [
                ['image' => $this->image('greenware'), 'caption' => 'Cups drying before their first firing'],
                ['image' => $this->image('trimming'), 'caption' => 'Trimming a foot ring'],
                ['image' => $this->image('grey-vessels'), 'caption' => 'Ash glaze, spring firing'],
                ['image' => $this->image('drying-bowls'), 'caption' => 'Bowls on a ware board'],
            ])),

            $this->section(LocationsSection::class, [
                'eyebrow' => '',
                'title' => 'Come and see.',
                'intro' => 'The shop is open at the weekend. Seconds are on a table by the door.',
                'layout' => 'split',
                'columns' => '1',
                'align' => 'start',
            ], $this->blocks('location', [
                [
                    'name' => 'Kiln Street',
                    'lat' => '51.4666',
                    'lng' => '-2.5901',
                    'address' => "14 Picton Street\nBristol BS6 5QA",
                    'phone' => '+44 117 496 0321',
                    'email' => 'studio@kilnstreet.example',
                    'hours' => 'Sat–Sun, 10:00–16:00',
                ],
            ]), scheme: KilnStreetSite::SCHEME),
        ];
    }
}
