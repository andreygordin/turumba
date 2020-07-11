<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Module\Page\Entity\Page;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

/**
 * @ORM\Entity
 * @ORM\Table(name="page",
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(columns={"slug1", "slug2", "slug3"})
 *    }
 * )
 */
class Page
{
    /**
     * @ORM\Column(type="page_page_id")
     * @ORM\Id
     */
    private Id $id;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private ?self $parent;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="parent")
     */
    private Collection $children;

    /**
     * @ORM\Column(type="page_page_short_name", nullable=true)
     */
    private ShortName $shortName;

    /**
     * @ORM\Column(type="page_page_full_name", nullable=true)
     */
    private FullName $fullName;

    /**
     * @ORM\Column(type="page_page_title", nullable=true)
     */
    private Title $title;

    /**
     * @ORM\Embedded(class="Path", columnPrefix=false)
     */
    private Path $path;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isVisible;

    /**
     * @ORM\Column(type="page_page_note", nullable=true)
     */
    private Note $note;

    public function __construct(Id $id)
    {
        $this->id = $id;
        $this->parent = null;
        $this->children = new ArrayCollection();
        $this->shortName = new ShortName('');
        $this->fullName = new FullName('');
        $this->title = new Title('');
        $this->path = new Path('', '', '');
        $this->isVisible = false;
        $this->note = new Note('');
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $page): void
    {
        if ($this->parent === $page) {
            return;
        }
        if ($this->parent) {
            $this->parent->removeChild($this);
        }
        $this->parent = $page;
        if ($page) {
            $page->addChild($this);
        }
    }

    public function withParent(?Page $parent): self
    {
        $page = clone $this;
        $page->setParent($parent);
        return $page;
    }

    /**
     * @return self[]
     */
    public function getChildren(): array
    {
        /** @var self[] */
        return $this->children->toArray();
    }

    public function hasChildren(): bool
    {
        return !$this->children->isEmpty();
    }

    public function addChild(self $page): void
    {
        if ($this->children->contains($page)) {
            return;
        }
        $this->children->add($page);
        $page->setParent($this);
    }

    public function removeChild(self $page): void
    {
        if (!$this->children->contains($page)) {
            return;
        }
        $this->children->removeElement($page);
        $page->setParent(null);
    }

    public function getShortName(): ShortName
    {
        return $this->shortName;
    }

    public function setShortName(ShortName $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function withShortName(ShortName $shortName): self
    {
        $page = clone $this;
        $page->setShortName($shortName);
        return $page;
    }

    public function getFullName(): FullName
    {
        return $this->fullName;
    }

    public function setFullName(FullName $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function withFullName(FullName $fullName): self
    {
        $page = clone $this;
        $page->setFullName($fullName);
        return $page;
    }

    public function getTitle(): Title
    {
        return $this->title;
    }

    public function setTitle(Title $title): void
    {
        $this->title = $title;
    }

    public function withTitle(Title $title): self
    {
        $page = clone $this;
        $page->setTitle($title);
        return $page;
    }

    public function getPath(): Path
    {
        return $this->path;
    }

    public function setPath(Path $path): void
    {
        $this->path = $path;
    }

    public function withPath(Path $path): self
    {
        $page = clone $this;
        $page->setPath($path);
        return $page;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setVisibility(bool $isVisible): void
    {
        if ($isVisible && !$this->isValid()) {
            throw new DomainException('Cannot make the page visible until validation errors are fixed.');
        }
        $this->isVisible = $isVisible;
    }

    public function withVisibility(bool $isVisible): self
    {
        $page = clone $this;
        $page->setVisibility($isVisible);
        return $page;
    }

    public function getNote(): Note
    {
        return $this->note;
    }

    public function setNote(Note $note): void
    {
        $this->note = $note;
    }

    public function withNote(Note $note): self
    {
        $page = clone $this;
        $page->setNote($note);
        return $page;
    }

    public function isValid(): bool
    {
        return !$this->getShortName()->isEmpty()
            && !$this->getFullName()->isEmpty()
            && !$this->getTitle()->isEmpty();
    }

    public function restoreDefaults(): void
    {
        $this->setParent(null);
        $this->setShortName(new ShortName(''));
        $this->setFullName(new FullName(''));
        $this->setTitle(new Title(''));
        $this->setPath(new Path('', '', ''));
        $this->setVisibility(false);
        $this->setNote(new Note(''));
    }
}
