<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0.1
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

namespace YOOtheme\Builder\Joomla\JBZoo;

use function YOOtheme\app;
use YOOtheme\JBZoo;

class ApplicationHelper
{
    public static function getApplications()
    {
        static $groups;

        if ($groups) {
            return $groups;
        }

        /**
         * @var $app Zoo
         */
        $app = app(JBZoo::class);

        $groups = [];
        foreach ($app->application->getApplications() as $application) {

            // App is not installed
            if (!$application->getMetaXMLFile()) {
                continue;
            }

            $groups[$application->application_group][] = $application;
        }

        return $groups;
    }

    public static function getApplication($id)
    {
        foreach (static::getApplications() as $group) {
            foreach ($group as $application) {
                if ($id === $application->id) {
                    return $application;
                }
            }
        }
    }
}
