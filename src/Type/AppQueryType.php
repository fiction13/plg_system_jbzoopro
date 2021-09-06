<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

namespace YOOtheme\Builder\Joomla\JBZoo\Type;

use YOOtheme\Builder\Source;
use YOOtheme\Str;

class AppQueryType
{
    /**
     * @param Source         $source
     * @param \Application   $application
     * @param \Application[] $applications
     *
     * @return array
     */
    public static function config(Source $source, $application, $applications)
    {
        $field = Str::camelCase(['zoo', $application->application_group]);
        $query = Str::camelCase([$field, 'Query'], true);

        foreach ($application->getTypes() as $type) {
            $source->objectType($name = Str::camelCase([$field, $type->id], true), ItemType::config($source, $name, $type));
        }

        return [
            'fields' => [
                $field => [
                    'type' => $query,
                    'extensions' => [
                        'call' => __CLASS__ . '::resolve',
                    ],
                ],
            ],
        ];
    }

    public static function resolve($root)
    {
        return $root;
    }
}
