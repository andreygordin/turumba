<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Command\Create\Command;

use App\Module\Page\Validator\NotBlankIfVisible\NotBlankIfVisible;
use App\Module\Page\Validator\PageExists;
use App\Module\Page\Validator\PathIsNotTaken;
use App\Module\Page\Validator\PathIsValid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Assert\GroupSequence({"Page", "Path"})
 */
class Page
{
    /**
     * @Assert\Sequentially({
     *  @Assert\Uuid(),
     *  @PageExists()
     * })
     */
    public ?string $parentId = null;

    /**
     * @Assert\Length(max=255, allowEmptyString=true)
     * @NotBlankIfVisible(visibilityPath="isVisible")
     */
    public ?string $shortName = null;

    /**
     * @Assert\Length(max=255, allowEmptyString=true)
     * @NotBlankIfVisible(visibilityPath="isVisible")
     */
    public ?string $fullName = null;

    /**
     * @Assert\Length(max=255, allowEmptyString=true)
     * @NotBlankIfVisible(visibilityPath="isVisible")
     */
    public ?string $title = null;

    /**
     * @Assert\Valid
     * @PathIsValid(slug1Path="path.slug1", slug2Path="path.slug2", slug3Path="path.slug3")
     * @PathIsNotTaken(slug1Path="path.slug1", slug2Path="path.slug2", slug3Path="path.slug3", groups={"Path"})
     */
    public ?Path $path = null;

    public ?bool $isVisible = null;

    /**
     * @Assert\Length(max=65535, allowEmptyString=true)
     */
    public ?string $note = null;
}
