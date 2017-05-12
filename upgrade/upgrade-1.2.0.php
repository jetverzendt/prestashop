<?php
/**
 * Created by PhpStorm.
 * User: Algemeen
 * Date: 5/10/2017
 * Time: 12:26 PM
 */

if (!defined('_PS_VERSION_'))
    exit;



function upgrade_module_1_2_0()
{
    $sql[] = '
	  DROP TABLE IF EXISTS `'._DB_PREFIX_.'keendelivery`;
    ';

    foreach ($sql as $query)
        if (Db::getInstance()->execute($query) == false)
            return false;
    return true;
}