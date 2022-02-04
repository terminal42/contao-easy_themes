<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\EventListener;

use Contao\BackendTemplate;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Terminal42\EasyThemesBundle\Helper;

/**
 * @Hook("parseBackendTemplate")
 */
class BackendTemplateListener
{
    private Helper $easyThemes;

    public function __construct(Helper $easyThemes)
    {
        $this->easyThemes = $easyThemes;
    }

    public function __invoke($strContent, string $template)
    {
        if ('be_main' !== $template || !$this->easyThemes->isEnabled()) {
            return $strContent;
        }

        return preg_replace(
            '/(<div id="container"[^>]*>)/',
            '$1'."\n".$this->generateContainerContent(),
            $strContent,
            1
        );
    }

    protected function generateContainerContent()
    {
        // we disable easy_themes if:
        // - it has been disabled (what a luminary)
        // - the mode is "be_mod"
        // - there is no theme at all
        // - the user has no module activated at all

        if ('be_mod' === $this->easyThemes->getCurrentMode() || empty($this->easyThemes->getAllThemes())) {
            return '';
        }

        $arrNavArray = $this->easyThemes->prepareBackendNavigationArray();

        if (empty($arrNavArray)) {
            return '';
        }

        $classes = [];
        $classes[] = 'easythemes_'.$this->easyThemes->getCurrentMode();
        $classes[] = 'easythemes_long';

        $objTemplate = new BackendTemplate('be_easythemes');
        $objTemplate->mode = $this->easyThemes->getCurrentMode();
        $objTemplate->class = implode(' ', $classes);
        $objTemplate->themes = $arrNavArray;

        return $objTemplate->parse();
    }
}
