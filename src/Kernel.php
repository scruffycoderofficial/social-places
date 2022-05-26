<?php

namespace BeyondCapable;

use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;

/**
 * Class Kernel
 *
 * @package App
 */
class Kernel extends BaseKernel
{
    use MicroKernelTrait;
}
