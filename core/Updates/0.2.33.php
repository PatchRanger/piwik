<?php
/**
 * Piwik - Open source web analytics
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 * @category Piwik
 * @package Updates
 */
use Piwik\Core\Piwik;
use Piwik\Core\Piwik_Common;

/**
 * @package Updates
 */
class Piwik_Updates_0_2_33 extends Piwik_Updates
{
    static function getSql($schema = 'Myisam')
    {
        $sqlarray = array(
            // 0.2.33 [1020]
            'ALTER TABLE `' . Piwik_Common::prefixTable('user_dashboard') . '`
				CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci '                                                                            => '1146',
            'ALTER TABLE `' . Piwik_Common::prefixTable('user_language') . '`
				CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci ' => '1146',
        );

        // alter table to set the utf8 collation
        $tablesToAlter = Piwik::getTablesInstalled(true);
        foreach ($tablesToAlter as $table) {
            $sqlarray['ALTER TABLE `' . $table . '`
				CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci '] = false;
        }

        return $sqlarray;
    }

    static function update()
    {
        Piwik_Updater::updateDatabase(__FILE__, self::getSql());
    }
}
