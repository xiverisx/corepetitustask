<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[AsEventListener(event: 'kernel.exception')]
class ValidationExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof UnprocessableEntityHttpException) {
            return;
        }

        $previous = $exception->getPrevious();
        if (!$previous instanceof ValidationFailedException) {
            return;
        }

        $violations = [];
        foreach ($previous->getViolations() as $violation) {
            $violations[] = [
                'propertyPath' => $violation->getPropertyPath(),
                'title' => $violation->getMessage(),
            ];
        }

        $response = new JsonResponse([
            'success' => false,
            'violations' => $violations,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
    }
}
