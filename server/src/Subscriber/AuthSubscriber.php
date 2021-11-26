<?php

namespace App\Subscriber;

use App\Controller\Api\ProtectedRoute;
use App\Service\JwtService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class AuthSubscriber implements EventSubscriberInterface
{
    private JwtService $jwtService;
    private bool $dev;

    public function __construct(
        JwtService $jwtService,
        string $env
    ) {
        $this->dev = $env === 'dev';
        $this->jwtService = $jwtService;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();
        $request = $event->getRequest();
        $controller = is_array($controller) ? $controller[0] : $controller;

        if ($controller instanceof ProtectedRoute) {
            $authorization = $request->headers->all('authorization');

            if (
                !isset($authorization[0]) ||
                (bool) !($client = $this->jwtService->validateToken($authorization[0], true))
            ) {
                throw new AccessDeniedHttpException(jwtService::INVALID_TOKEN);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $code = $response->getStatusCode();

        if (!$response) {
            return;
        }

        $content = json_decode($response->getContent(), true);
        if (!$content) {
            return;
        }

        if ($code >= 400) {
            return;
        }

        $authorization = $event->getRequest()->headers->all('authorization');
        if (
            !isset($authorization[0]) ||
            !($payload = $this->jwtService->validateToken($authorization[0], false))
        ) {
            return;
        }
        $jwt = $this->jwtService->generateJWTFromPayload($payload);
        if (!$jwt) {
            return;
        }

        $content += ['token' => $jwt];
        $response->setContent(json_encode($content));

        return $response;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
            KernelEvents::RESPONSE => 'onKernelResponse'
        ];
    }
}
