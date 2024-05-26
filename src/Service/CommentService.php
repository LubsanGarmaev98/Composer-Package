<?php

declare(strict_types=1);

namespace lubsangarmaev98\ComposerPackage\Service;

use lubsangarmaev98\ComposerPackage\Exception\CommentHttpException;
use lubsangarmaev98\ComposerPackage\Http\Client\CommentHttpClient;
use lubsangarmaev98\ComposerPackage\Http\Request\Api\GetCommentsRequest;
use lubsangarmaev98\ComposerPackage\Http\Request\Api\CreateCommentRequest;
use lubsangarmaev98\ComposerPackage\Http\Request\Api\UpdateCommentRequest;
use lubsangarmaev98\ComposerPackage\Model\Comment;
use lubsangarmaev98\ComposerPackage\Model\CommentBody;
use lubsangarmaev98\ComposerPackage\Service\CommentBuilder;
use Generator;
use JsonMachine\Items;
use lubsangarmaev98\ComposerPackage\Http\Request\AbstractRequest;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;
use Throwable;

/**
 * Класс http сервиса комментариев.
 */
final class CommentService implements CommentServiceInterface
{
    private CONST SUCCESS = [200, 201] ;

    /**
     * @param CommentHttpClient $commentHttpClient http клиент.
     * @param LoggerInterface   $logger            логгер.
     */
    public function __construct(
        private CommentHttpClient $commentHttpClient, 
        private LoggerInterface $logger)
    {
    }

    /**
     * Возвращает комментарии.
     *
     * @return iterable
     */
    public function getComments(): iterable
    {
        $request = new GetCommentsRequest();

        try {
            $response = $this->commentHttpClient->execute($request);
            
            $this->checkStatusCode($response);

            $jsonChunks = $this->httpClientChunks($this->commentHttpClient->stream($response));

            foreach (Items::fromIterable($jsonChunks) as $item) {
                yield CommentBuilder::buildFromStd($item);
            }
        } catch (Throwable $exception) {
            $this->createLoggerMessage($exception, $request);
        }

        return [];
    }

    /**
     * Генерирует чанки.
     *
     * @param ResponseStreamInterface $responseStream фрагменты ответа.
     *
     * @return Generator
     */
    private function httpClientChunks(ResponseStreamInterface $responseStream): Generator
    {
        foreach ($responseStream as $chunk) {
            yield $chunk->getContent();
        }
    }

    /**
     * Создает комментарий.
     *
     * @param CommentBody $commentBody Тело комментария (автор и текст).
     *
     * @return Comment|null
     */
    public function createComment(CommentBody $commentBody): ?Comment
    {
        $request = new CreateCommentRequest($commentBody->getName(), $commentBody->getText());

        try {
            $response = $this->commentHttpClient->execute($request);

            $this->checkStatusCode($response);

            $comment = CommentBuilder::buildFromJson($response->getContent());
        } catch (Throwable $exception) {
            $this->createLoggerMessage($exception, $request);

            return null;
        }

        return $comment;
    }

    /**
     * Редактирует комментарий.
     *
     * @param Comment $comment Объект редактируемого комментария.
     * @param array   $data    Редактируемые данные.
     *
     * @return Comment|null
     */
    public function updateComment(Comment $comment, array $data): ?Comment
    {
        $request = $this->getUpdateCommentRequest($comment, $data);
        if (empty($request->getBody())) {
            return $comment;
        }

        try {
            $response = $this->commentHttpClient->execute($request);

            $this->checkStatusCode($response);

            $comment = CommentBuilder::buildFromJson($response->getContent());
        } catch (Throwable $exception) {
            $this->createLoggerMessage($exception, $request, $comment);

            return null;
        }

        return $comment;
    }

    /**
     * Формирует запрос для редактирования комментария.
     *
     * @param Comment $comment комментарий.
     * @param array   $data    данные.
     *
     * @return UpdateCommentRequest
     */
    private function getUpdateCommentRequest(Comment $comment, array $data): UpdateCommentRequest
    {
        $request = new UpdateCommentRequest($comment->getId());

        if (isset($data['name']) && $data['name'] !== $comment->getName()) {
            $request->setName($data['name']);
        }

        if (isset($data['text']) && $data['text'] !== $comment->getText()) {
            $request->setText($data['text']);
        }

        return $request;
    }

    /**
     * Проверка статус кода 200 или 201
     * 
     * @param ResponseInterface $responseInterface
     * @return void
     * 
     * @exception CommentHttpException
     */
    private function checkStatusCode(ResponseInterface $responseInterface): void 
    {
        if (!in_array($responseInterface->getStatusCode(), self::SUCCESS)) {
            throw CommentHttpException::failResponse(
                $responseInterface->getStatusCode(), 
                $responseInterface->getContent()
            );
        }
    }

    private function createLoggerMessage(Throwable $exception, AbstractRequest $request, ?Comment $comment = null)
    {
        $this->logger->error(
            $request->getErrorMessage(),
            match(true) {
                !is_null($comment)   => compact('request', 'exception', 'comment'),
                default             => compact('request', 'exception')
            }
        );
    }
}
