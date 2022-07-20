<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0.2
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use YOOtheme\Application;

class plgSystemJBZooPro extends CMSPlugin
{
	public function onAfterInitialise()
	{
		if (!$zoo = $this->getZoo()) {
			return;
		}

		// check if YOOtheme Pro is loaded
        if (!class_exists(Application::class, false)) {
            return;
        }

        // register alias
		class_alias(App::class, YOOtheme\JBZoo::class);

		// register plugin
		JLoader::registerNamespace('YOOtheme\\Builder\\Joomla\\JBZoo\\', __DIR__ . '/src', false, false, 'psr4');

		// register applications
		$zoo->path->register(__DIR__ . '/applications', 'applications');

        // load a single module from the same directory
		$app = Application::getInstance();
		$app->set(YOOtheme\JBZoo::class, $zoo);
        $app->load(__DIR__ . '/bootstrap.php');
	}

	protected function getZoo()
	{
		$config = JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';
		$component = ComponentHelper::getComponent('com_zoo', true);

		if (!$component->enabled || !is_file($config)) {
			return;
		}

		require_once($config);

		if (!class_exists(App::class) or !$app = App::getInstance('zoo')) {
			return;
		}

		return $app;
	}
}
