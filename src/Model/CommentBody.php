<?php

declare(strict_types=1);

namespace lubsangarmaev98\ComposerPackage\Model;

/**
 * Контейнер тела комментария.
 */
class CommentBody
{
    /**
     * @param string $name имя.
     * @param string $text текст.
     */
    public function __construct(
        private readonly string $name, 
        private readonly string $text
    )
    {
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
