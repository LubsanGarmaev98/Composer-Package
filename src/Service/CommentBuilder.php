<?php

declare(strict_types=1);

namespace lubsangarmaev98\ComposerPackage\Service;

use lubsangarmaev98\ComposerPackage\Model\Comment;
use lubsangarmaev98\ComposerPackage\Model\CommentBody;
use stdClass;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

/**
 * Класс билдер комментария.
 */
final class CommentBuilder
{
    /**
     * Инстанцирует объект комментария из json строки.
     *
     * @param string $data данные json.
     *
     * @return Comment
     *
     * @throws InvalidArgumentException
     */
    public static function buildFromJson(string $data): Comment
    {
        $data = json_decode($data, true, JSON_THROW_ON_ERROR);

        Assert::notEmpty($data['id']);
        Assert::notEmpty($data['name']);
        Assert::notEmpty($data['text']);

        return new Comment(
            id: $data['id'],
            commentBody: new CommentBody(
                name: $data['name'], 
                text: $data['text']
            )
        );
    }

    /**
     * Инстанцирует объект комментария из объекта stdClass.
     *
     * @param stdClass $data данные.
     *
     * @return Comment
     *
     * @throws InvalidArgumentException
     */
    public static function buildFromStd(stdClass $data): Comment
    {
        Assert::propertyExists($data, 'id');
        Assert::propertyExists($data, 'name');
        Assert::propertyExists($data, 'text');

        return new Comment(
            id: $data->id,
            commentBody: new CommentBody(
                name: $data->name, 
                text: $data->text
            )
        );
    }
}
