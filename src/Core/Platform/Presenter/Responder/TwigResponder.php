<?php

declare(strict_types=1);

namespace BeyondCapable\Platform\Presenter\Responder
{
    use Twig\Environment;
    use Symfony\Component\HttpFoundation\Response;
    use BeyondCapable\Platform\Presenter\ViewModel\ViewModelInterface;

    /**
     * Class TwigResponder
     *
     * @package BeyondCapable\Platform\Presenter\Responder
     */
    final class TwigResponder
    {
        /**
         * TwigResponder constructor.
         *
         * @param Environment $twig
         */
        public function __construct(private Environment $twig)
        {
        }

        /**
         * @param string $template
         * @param ViewModelInterface $viewModel
         * @return Response
         * @throws \Twig\Error\LoaderError
         * @throws \Twig\Error\RuntimeError
         * @throws \Twig\Error\SyntaxError
         */
        public function send(string $template, ViewModelInterface $viewModel): Response
        {
            return new Response($this->twig->render($template, ['vm' => $viewModel]));
        }
    }
}