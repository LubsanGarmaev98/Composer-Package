<?php

declare(strict_types=1);

namespace lubsangarmaev98\ComposerPackage\Http\Request;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Traversable;

/**
 * Абстрактный класс запроса.
 */
abstract class AbstractRequest
{
    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    abstract public function getMethod(): string;

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    abstract public function getUri(): string;

    /**
     * Возвращает сообщение ошибки.
     *
     * @return string
     */
    abstract public function getErrorMessage(): string;

    /**
     * Тело запроса.
     *
     * @return array|string|resource|Traversable|Closure
     */
    public function getBody()
    {
        return HttpClientInterface::OPTIONS_DEFAULTS['body'] ?? '';
    }
}
