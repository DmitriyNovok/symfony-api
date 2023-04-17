<?php

namespace App\Tests\Listener;

use App\Listener\ApiExceptionListener;
use App\Model\ErrorDebugDetails;
use App\Model\ErrorResponse;
use App\Service\ExceptionHandler\ExceptionMapping;
use App\Service\ExceptionHandler\ExceptionMappingResolver;
use App\Tests\AbstractTestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class ApiExceptionListenerTest extends AbstractTestCase
{
    private ExceptionMappingResolver $resolver;

    private LoggerInterface $logger;

    private SerializerInterface $serializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->resolver = $this->createMock(ExceptionMappingResolver::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->serializer = $this->createMock(SerializerInterface::class);
    }

    public function testNon500MappingWithHiddenMessage(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500MappingWithPublicMessage(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, false);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function testNon500LoggableMappingTriggersLogger(): void
    {
        $mapping = new ExceptionMapping(Response::HTTP_NOT_FOUND, false, true);
        $responseMessage = 'test';
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error');

        $event = $this->createEvent(new \InvalidArgumentException('test'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    public function test500IsLoggable(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_GATEWAY_TIMEOUT);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_GATEWAY_TIMEOUT, $responseBody, $event->getResponse());
    }

    public function test500IsDefaultWhenMappingNotFound(): void
    {
        $responseMessage = Response::$statusTexts[Response::HTTP_INTERNAL_SERVER_ERROR];
        $responseBody = json_encode(['error' => $responseMessage]);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn(null);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(new ErrorResponse($responseMessage), JsonEncoder::FORMAT)
            ->willReturn($responseBody);

        $this->logger->expects($this->once())
            ->method('error')
            ->with('error message', $this->anything());

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event);

        $this->assertResponse(Response::HTTP_INTERNAL_SERVER_ERROR, $responseBody, $event->getResponse());
    }

    public function testShowTraceWhenDebug(): void
    {
        $mapping = ExceptionMapping::fromCode(Response::HTTP_NOT_FOUND);
        $responseMessage = Response::$statusTexts[$mapping->getCode()];
        $responseBody = json_encode(['error' => $responseMessage, 'trace' => 'something']);

        $this->resolver->expects($this->once())
            ->method('resolve')
            ->with(\InvalidArgumentException::class)
            ->willReturn($mapping);

        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with(
                $this->callback(function (ErrorResponse $errorResponse) use ($responseMessage) {

                    /** @var ErrorDebugDetails[]|object $details */
                    $details = $errorResponse->getDetails();

                    return $errorResponse->getMessage() == $responseMessage
                        && $details instanceof ErrorDebugDetails
                        && !empty($details->getTrace());
                }),
                JsonEncoder::FORMAT
            )
            ->willReturn($responseBody);

        $event = $this->createEvent(new \InvalidArgumentException('error message'));

        $this->runListener($event, true);

        $this->assertResponse(Response::HTTP_NOT_FOUND, $responseBody, $event->getResponse());
    }

    private function assertResponse(int $expectedStatusCode, string $expectedBody, Response $response): void
    {
        $this->assertEquals($expectedStatusCode, $response->getStatusCode());
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonStringEqualsJsonString($expectedBody, $response->getContent());
    }

    private function runListener(ExceptionEvent $event, bool $isDebug = false): void
    {
        (new ApiExceptionListener($this->resolver, $this->logger, $this->serializer, $isDebug))($event);
    }

    private function createEvent(\InvalidArgumentException $exception): ExceptionEvent
    {
        return new ExceptionEvent(
            $this->createTestKernel(),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            $exception
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
