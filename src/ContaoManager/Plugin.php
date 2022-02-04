<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\EasyThemesBundle\Terminal42EasyThemesBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            (new BundleConfig(Terminal42EasyThemesBundle::class))
                ->setLoadAfter([ContaoCoreBundle::class])
                ->setReplace(['easy_themes']),
        ];
    }
}
