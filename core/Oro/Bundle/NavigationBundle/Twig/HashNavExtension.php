<?php

namespace Oro\Bundle\NavigationBundle\Twig;

use Oro\Bundle\NavigationBundle\Event\ResponseHashnavListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Provides Twig functions to support hash navigation:
 *   - oro_is_hash_navigation
 *   - oro_hash_navigation_header
 */
class HashNavExtension extends AbstractExtension
{
    /** @var Request */
    protected $request;

    /**
     * Listen to the 'kernel.request' event to get the main request.
     * The request can not be injected directly into a Twig extension,
     * this causes a ScopeWideningInjectionException
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if ($event->getRequestType() === HttpKernel::MASTER_REQUEST) {
            $this->request = $event->getRequest();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'oro_is_hash_navigation',
                [$this, 'checkIsHashNavigation']
            ),
            new TwigFunction(
                'oro_hash_navigation_header',
                [$this, 'getHashNavigationHeaderConst']
            ),
        ];
    }

    /**
     * Check for hash navigation
     *
     * @return bool
     */
    public function checkIsHashNavigation()
    {
        return
            is_object($this->request)
            && (
                $this->request->headers->get(ResponseHashnavListener::HASH_NAVIGATION_HEADER)
                || $this->request->get(ResponseHashnavListener::HASH_NAVIGATION_HEADER)
            );
    }

    /**
     * Get hash navigation header string
     *
     * @return string
     */
    public function getHashNavigationHeaderConst()
    {
        return ResponseHashnavListener::HASH_NAVIGATION_HEADER;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'oro_hash_nav';
    }
}
