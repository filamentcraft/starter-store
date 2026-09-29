<?php

declare(strict_types=1);

namespace App\Blueprints\Pages;

use FilamentCraft\Sections\Builtin\FaqSection;
use FilamentCraft\Sections\Builtin\RichTextSection;

final class CarePage extends Page
{
    public function name(): string
    {
        return 'Care & delivery';
    }

    public function slug(): string
    {
        return 'care';
    }

    public function description(): string
    {
        return 'Delivery, returns, breakages and how to look after handmade stoneware.';
    }

    public function sections(): array
    {
        return [
            $this->section(RichTextSection::class, [
                'eyebrow' => '',
                'title' => 'Care & delivery',
                'body' => '<p>Everything is packed by hand in recycled paper and posted from Bristol on Tuesdays and Fridays. Delivery is £5 in the UK, free over £60, and you pay the courier when your parcel arrives.</p>',
                'layout' => 'article',
                'width' => 'md',
                'align' => 'start',
                'surface' => false,
            ]),

            $this->section(FaqSection::class, [
                'eyebrow' => '',
                'title' => '',
                'intro' => '',
                'align' => 'start',
            ], $this->blocks('item', [
                ['question' => 'Can it go in the dishwasher?', 'answer' => 'Yes, and in the microwave. The vases are the exception: they are glazed inside, but not meant to hold hot liquids.'],
                ['question' => 'What if something arrives broken?', 'answer' => 'Send a photo within 7 days and we will post a replacement, or refund you if that shape has sold out. You do not need to send the pieces back.'],
                ['question' => 'Can I return something?', 'answer' => 'Within 30 days, unused. Email us first and we will send a returns label.'],
                ['question' => 'Do you ship outside the UK?', 'answer' => 'Not through the shop yet. Email us and we will quote postage to Europe.'],
                ['question' => 'Why are there little dark specks?', 'answer' => 'That is iron in the clay coming through the glaze in the kiln. It is part of the clay body, not a flaw.'],
                ['question' => 'Do you take commissions?', 'answer' => 'For cafés and restaurants, yes, from 24 pieces. We are booked about four months ahead.'],
            ])),
        ];
    }
}
