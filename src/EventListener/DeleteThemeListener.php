<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

/**
 * @Callback(table="tl_theme", target="config.ondelete")
 */
class DeleteThemeListener
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function __invoke(DataContainer $dc): void
    {
        $users = $this->connection->fetchAllAssociative('SELECT id,et_enable,et_activeModules FROM tl_user');

        foreach ($users as $user) {
            // if the user doesn't use easy_themes, we skip
            if (!$user['et_enable']) {
                continue;
            }

            $arrModulesOld = StringUtil::deserialize($user['et_activeModules']);

            // if there's no data we skip
            if (!\is_array($arrModulesOld) || !\count($arrModulesOld)) {
                continue;
            }

            $arrModulesNew = [];

            foreach ($arrModulesOld as $strConfig) {
                $arrChunks = explode('::', $strConfig);
                $intThemeID = (int) $arrChunks[0];

                // we only add it to the new array if it's NOT the one being deleted
                if ($intThemeID !== (int) $dc->id) {
                    $arrModulesNew[] = $strConfig;
                }
            }

            $this->connection->update(
                'tl_user',
                ['et_activeModules' => serialize($arrModulesNew)],
                ['id' => $user['id']]
            );
        }
    }
}
