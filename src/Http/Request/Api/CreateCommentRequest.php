<?php

declare(strict_types=1);

namespace ComposerPackage\Http\Request;

use Traversable;

/**
 * Класс объекта запроса создания комментария.
 */
final class CreateCommentRequest extends AbstractRequest
{
    /**
     * @param string $name Имя автора комментария.
     * @param string $text Текст комментария
     */
    public function __construct(
        private readonly string $name, 
        private readonly string $text
    )
    {
    }

    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return 'POST';
    }

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    public function getUri(): string
    {
        return '/comment';
    }

    /**
     * Тело запроса.
     *
     * @return array|string|resource|Traversable|Closure
     */
    public function getBody(): mixed
    {
        return [
            'name' => $this->name,
            'text' => $this->text,
        ];
    }
}
