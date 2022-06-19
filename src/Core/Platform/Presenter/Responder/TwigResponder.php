<?php

declare(strict_types=1);

namespace BeyondCapable\Core\Platform\Presenter\Responder
{
    use BeyondCapable\Core\Platform\Presenter\ViewModel\ViewModelInterface;

    use Twig\Environment;
    use Twig\Error\LoaderError;
    use Twig\Error\SyntaxError;
    use Twig\Error\RuntimeError;

    use Symfony\Component\HttpFoundation\Response;

    /**
     * Class TwigResponder
     *
     * @package BeyondCapable\Core\Platform\Presenter\Responder
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
         * @throws LoaderError
         * @throws RuntimeError
         * @throws SyntaxError
         */
        public function send(string $template, ViewModelInterface $viewModel): Response
        {
            return new Response($this->twig->render($template, ['vm' => $viewModel]));
        }
    }
}