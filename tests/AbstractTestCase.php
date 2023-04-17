<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class AbstractTestCase extends TestCase
{
    protected function setEntityId(object $entity, int $value, $idField = 'id')
    {
        $class = new \ReflectionClass($entity);
        $property = $class->getProperty($idField);
        $property->setAccessible(true);
        $property->setValue($entity, $value);
        $property->setAccessible(false);
    }

    protected function assertResponse(int $expectedStatusCode, string $expectedBody, Response $response): void
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonStringEqualsJsonString($expectedBody, $response->getContent());
    }

    protected function createExceptionEvent(\Throwable $throwable): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $throwable
        );
    }

    private function createTestKernel(): HttpKernelInterface
    {
        return new class() implements HttpKernelInterface {
            /**
             * Handles a Request to convert it to a Response.
             *
             * When $catch is true, the implementation must catch all exceptions
             * and do its best to convert them to a Response instance.
             *
             * @param int  $type  The type of the request
             *                    (one of HttpKernelInterface::MAIN_REQUEST or HttpKernelInterface::SUB_REQUEST)
             * @param bool $catch Whether to catch exceptions or not
             *
             * @return Response
             *
             * @throws \Exception When an Exception occurs during processing
             */
            public function handle(Request $request, int $type = self::MAIN_REQUEST, bool $catch = true)
            {
                return new Response('test');
            }
        };
    }
}
