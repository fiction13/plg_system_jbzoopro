<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0.1
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class plgSystemJBZooProInstallerScript
{	
	// Post Install Function

	function postflight( $type, $parent )
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery( true );
		$query->update('#__extensions')->set('enabled=1')->set('ordering=1000')->where('type='.$db->q('plugin'))->where('element='.$db->q('JBZooPro'));
		$db->setQuery($query)->execute();
	}
}
