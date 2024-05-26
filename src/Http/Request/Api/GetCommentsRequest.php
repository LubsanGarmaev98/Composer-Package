<?php

declare(strict_types=1);

namespace lubsangarmaev98\ComposerPackage\Http\Request\Api;

use lubsangarmaev98\ComposerPackage\Http\Request\AbstractRequest;

/**
 * Класс объекта запроса комментариев.
 */
final class GetCommentsRequest extends AbstractRequest
{
    /**
     * Возвращает HTTP метод запроса.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return 'GET';
    }

    /**
     * Возвращает URI запроса.
     *
     * @return string
     */
    public function getUri(): string
    {
        return '/comments';
    }


    /**
     * Возвращает сообщение об ошибке
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return 'Ошибка при получении комментариев';
    }
}
