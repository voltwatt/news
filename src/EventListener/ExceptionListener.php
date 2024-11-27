<?php declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use InvalidArgumentException;

final readonly class ExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 48],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = match (true) {
            $exception instanceof ValidationFailedException => new JsonResponse(
                [
                    'error' => 'Validation failed',
                    'violations' => $this->getValidationErrors($exception),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            ),
            $exception instanceof UnauthorizedHttpException => new JsonResponse(
                ['error' => $exception->getMessage() ?: 'Unauthorized'],
                Response::HTTP_UNAUTHORIZED
            ),
            $exception instanceof NotFoundHttpException => new JsonResponse(
                ['error' => $exception->getMessage() ?: 'Resource not found'],
                Response::HTTP_NOT_FOUND
            ),
            $exception instanceof AccessDeniedHttpException => new JsonResponse(
                ['error' => $exception->getMessage() ?: 'Access denied'],
                Response::HTTP_FORBIDDEN
            ),
            $exception instanceof BadRequestHttpException => new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            ),
            $exception instanceof HttpException => new JsonResponse(
                ['error' => $exception->getMessage()],
                $exception->getStatusCode()
            ),
            $exception instanceof InvalidArgumentException => new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_BAD_REQUEST
            ),
            default => new JsonResponse(
                ['error' => $exception->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            )
        };

        $event->setResponse($response);
    }

    private function getValidationErrors(ValidationFailedException $exception): array
    {
        $violations = $exception->getViolations();
        $errors = [];

        foreach ($violations as $violation) {
            $errors[] = [
                'field' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }
}
