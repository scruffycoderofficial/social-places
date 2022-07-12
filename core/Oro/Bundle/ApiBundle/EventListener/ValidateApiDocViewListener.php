<?php

namespace Oro\Bundle\ApiBundle\EventListener;

use Nelmio\ApiDocBundle\Controller\ApiDocController;
use Oro\Bundle\ApiBundle\Controller\RestApiDocController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Checks whether the requested API view is valid for REST API sandbox.
 * This implemented as a listener to allow introduce new controllers for REST API sandbox with different views.
 * Also makes sure that "view" request attribute contains the correct default API view
 * if a view was not requested explicitly.
 */
class ValidateApiDocViewListener
{
    /** @var string */
    private $basePath;

    /** @var string[] */
    private $views;

    /** @var string|null */
    private $defaultView;

    /**
     * @param string      $basePath
     * @param string[]    $views
     * @param string|null $defaultView
     */
    public function __construct(string $basePath, array $views, ?string $defaultView)
    {
        $this->basePath = $basePath;
        $this->views = $views;
        $this->defaultView = $defaultView;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        if (\is_array($controller) && $this->isApiDocController($controller)) {
            $request = $event->getRequest();
            if (!$this->isValidView($request)) {
                throw new NotFoundHttpException(\sprintf('Invalid API view "%s".', $this->getView($request)));
            }

            $defaultView = $this->getDefaultView($request);
            if ($defaultView
                && $request->attributes->get('view') !== $defaultView
                && $this->isDefaultViewRequested($request)
            ) {
                $request->attributes->set('view', $defaultView);
            }
        }
    }

    /**
     * @param array $controller
     *
     * @return bool
     */
    protected function isApiDocController(array $controller): bool
    {
        $controllerClass = $controller[0];
        if ($controllerClass instanceof RestApiDocController) {
            return true;
        }
        if ($controllerClass instanceof ApiDocController) {
            return 'indexAction' === $controller[1];
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isValidView(Request $request): bool
    {
        $view = $this->getView($request);

        return !$view || \in_array($view, $this->views, true);
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    protected function getView(Request $request): ?string
    {
        return $request->attributes->get('view');
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    protected function getDefaultView(Request $request): ?string
    {
        return $this->defaultView;
    }

    /**
     * @param Request $request
     *
     * @return bool
     */
    protected function isDefaultViewRequested(Request $request): bool
    {
        $pathInfo = $request->getPathInfo();
        $pos = \strpos($pathInfo, $this->basePath);

        return false === $pos || !\substr($pathInfo, $pos + 9);
    }
}
