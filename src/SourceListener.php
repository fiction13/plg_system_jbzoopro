<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

namespace YOOtheme\Builder\Joomla\JBZoo;

class SourceListener
{
    /**
     * @param Source $source
     */
    public static function initSource($source)
    {
        $source->objectType('JBImage', Type\JBImageType::config());
        $source->objectType('JBGalleryImage', Type\JBGalleryImageType::config());
        $source->objectType('JBGallery', Type\JBGalleryType::config());

        foreach (ApplicationHelper::getApplications() as $applications) {
            $source->queryType(Type\AppQueryType::config($source, $applications[0], $applications));
        }
    }
}
