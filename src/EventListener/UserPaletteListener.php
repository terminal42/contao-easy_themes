<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\EventListener;

use Contao\BackendUser;
use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Symfony\Component\Security\Core\Security;

/**
 * @Callback(table="tl_user", target="config.onload")
 */
class UserPaletteListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(DataContainer $dc): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof BackendUser || !$user->hasAccess('themes', 'modules')) {
            return;
        }

        $pm = PaletteManipulator::create()
            ->addLegend('et_legend', 'theme_legend')
            ->addField('et_enable', 'et_legend', PaletteManipulator::POSITION_APPEND)
        ;

        foreach (array_keys($GLOBALS['TL_DCA']['tl_user']['palettes']) as $palette) {
            if ('__selector__' === $palette) {
                continue;
            }

            $pm->applyToPalette($palette, 'tl_user');
        }

        // extend selector
        $GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'et_enable';

        // extend subpalettes
        $strSubpalette = 'et_activeModules,et_mode';

        if ('be_mod' === $user->et_mode) {
            $strSubpalette .= ',et_bemodRef';
        }

        $GLOBALS['TL_DCA']['tl_user']['subpalettes']['et_enable'] = $strSubpalette;
    }
}
