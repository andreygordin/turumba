<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\PageList\Service;

use App\Read\PageList\Result\Page;
use App\Read\PageList\Result\PageCollection;
use App\Read\PageList\Result\Path;
use App\Read\Service\Formatter;
use App\Read\Service\FormatterTrait;
use Webmozart\Assert\Assert;

class PageDenormalizer
{
    use FormatterTrait;

    private Formatter $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
    }

    public function denormalize(array $data): Page
    {
        /**
         * @psalm-var array{
         *     id: string,
         *     full_name: ?string,
         *     is_visible: bool,
         *     note: ?string,
         *     parent_id: ?string,
         *     short_name: ?string,
         *     slug1: ?string,
         *     slug2: ?string,
         *     slug3: ?string,
         *     title: ?string,
         * } $data
         */

        $keys = [
            'id',
            'full_name',
            'is_visible',
            'note',
            'parent_id',
            'short_name',
            'slug1',
            'slug2',
            'slug3',
            'title',
        ];
        foreach ($keys as $key) {
            Assert::keyExists($data, $key);
        }

        $page = new Page();

        $page->id = $data['id'];

        $page->children = new PageCollection();

        $page->fullName = $this->useFormatter && $data['full_name'] !== null
            ? $this->formatter->withValue($data['full_name'])->encode()->formatWithTypograph()->getValue()
            : $data['full_name'];

        $page->isVisible = $data['is_visible'];

        $page->note = $this->useFormatter && $data['note'] !== null
            ? $this->formatter->withValue($data['note'])->encode()->getValue()
            : $data['note'];

        $page->parentId = $data['parent_id'];

        $page->path = new Path();
        $page->path->slug1 = $data['slug1'];
        $page->path->slug2 = $data['slug2'];
        $page->path->slug3 = $data['slug3'];

        $page->shortName = $this->useFormatter && $data['short_name'] !== null
            ? $this->formatter->withValue($data['short_name'])->encode()->formatWithTypograph()->getValue()
            : $data['short_name'];

        $page->title = $this->useFormatter && $data['title'] !== null
            ? $this->formatter->withValue($data['title'])
                ->formatWithTemplateEngine()
                ->encode()
                ->formatWithTypograph()
                ->getValue()
            : $data['title'];

        return $page;
    }
}
