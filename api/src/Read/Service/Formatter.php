<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

namespace App\Read\Service;

use EMT\EMTypograph;
use Throwable;
use Twig\Environment;
use Webmozart\Assert\Assert;

class Formatter
{
    private EMTypograph $typograph;
    private Environment $twig;
    private ?string $value = null;

    public function __construct(EMTypograph $typograph, Environment $twig)
    {
        $this->typograph = $typograph;
        $this->twig = $twig;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function withValue(string $value): self
    {
        $formatter = clone $this;
        $formatter->value = $value;
        return $formatter;
    }

    public function getValue(): string
    {
        $this->assertValueNotNull();
        return $this->value;
    }

    public function encode(): self
    {
        $this->assertValueNotNull();

        $this->value = htmlspecialchars($this->value, ENT_QUOTES | ENT_SUBSTITUTE);

        return $this;
    }

    public function formatWithTypograph(): self
    {
        $this->assertValueNotNull();

        $this->typograph->set_text($this->value);
        /** @var string $value */
        $value = $this->typograph->apply();
        $this->value = $value;

        return $this;
    }

    public function formatWithTemplateEngine(): self
    {
        $this->assertValueNotNull();

        try {
            $this->value = $this->twig->createTemplate($this->value)->render();
        } catch (Throwable $e) {
        }

        return $this;
    }

    /**
     * @psalm-assert !null $this->value
     */
    private function assertValueNotNull(): void
    {
        Assert::notNull($this->value, 'You need to set value first');
    }
}
