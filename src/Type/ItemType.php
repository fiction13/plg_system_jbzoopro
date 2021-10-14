<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0.1
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

namespace YOOtheme\Builder\Joomla\JBZoo\Type;

use YOOtheme\Event;
use function YOOtheme\app;
use YOOtheme\Arr;
use YOOtheme\Builder\Source;
use YOOtheme\Path;
use YOOtheme\Str;
use YOOtheme\View;
use YOOtheme\Builder\Joomla\Zoo\StrHelper;

class ItemType
{
    protected static $access = [
        'jbimage', 'jbgallery', 'jbgalleryimage'
    ];

    /**
     * @param Source $source
     * @param string $name
     * @param \Type  $type
     *
     * @return array
     */
    public static function config(Source $source, $name, \Type $type)
    {
        $elements = $type->getElements();

        foreach ($elements as $key => $element) {

            // only access elements
            if (!in_array($element->getElementType(), self::$access)) {
                continue;
            }

            $name = $element->config->name;
            $field = StrHelper::toFieldName($name);

            if (!$field) {
                continue;
            }

            $config = [
                'type' => 'String',
                'group' => $element->getMetaData('group'),
                'args' => [],
                'metadata' => [
                    'label' => $name,
                ],
                'extensions' => [
                    'call' => [
                        'func' => __CLASS__ . '::resolve',
                        'args' => ['element' => $element->identifier],
                    ],
                ],
            ];

            $elementType = Str::camelCase($element->getElementType(), true);

            if (self::hasMultipleValue($element)) {
                $configSingle = is_callable($callback = [__CLASS__, "config{$elementType}"])
                    ? $callback($element, $config)
                    : static::configElement($element, $config);


                $configSingle = Event::emit('source.com_zoo.item.field|filter', $configSingle, $element, $source, $name);

                $configMultiple = is_callable($callback = [__CLASS__, "config{$elementType}"])
                    ? $callback($element, $config, true)
                    : static::configElement($element, $config);

                $configMultiple = Event::emit('source.com_zoo.item.field|filter', $configMultiple, $element, $source, $name);

                if ($configSingle) {
                    $fields[$field] = $configSingle;
                }

                if ($configMultiple) {
                    $fields[$field.'_multiple'] = $configMultiple;
                }
            } else {
                $config = is_callable($callback = [__CLASS__, "config{$elementType}"])
                    ? $callback($element, $config, $source, $name)
                    : static::configElement($element, $config);

                $config = Event::emit('source.com_zoo.item.field|filter', $config, $element, $source, $name);

                if ($config) {
                    $fields[$field] = $config;
                }
            }
        }

        $metadata = [
            'type' => true,
            'label' => $type->getName(),
        ];

        return compact('fields', 'metadata');
    }

    protected static function configJBImage(\Element $element, array $config, bool $isRepeatable = null)
    {   
        if ($isRepeatable) {
            return ['type' => ['listOf' => 'ZooJBImage']] + $config;
        }

        return ['type' => 'ZooJBImage'] + $config;
    }

    protected static function configJBGalleryImage(\Element $element, array $config, bool $isRepeatable = null)
    {
        if ($isRepeatable) {
            return ['type' => ['listOf' => 'ZooJBGalleryImage']] + $config;
        }

        return ['type' => 'ZooJBGalleryImage'] + $config;
    }

    protected static function configJBGallery(\Element $element, array $config, bool $isRepeatable = null)
    {
        if ($isRepeatable) {
            return ['type' => ['listOf' => 'ZooJBGallery']] + $config;
        }

        return ['type' => 'ZooJBGallery'] + $config;
    }

    public static function resolve($item, $args, $ctx, $info)
    {
        $element = $item->getElement($args['element']);

        if (!$element) {
            return;
        }

        $isMultiple = strpos($info->fieldName, '_multiple') !== FALSE;

        if ($isMultiple) {
            $info->fieldName = str_replace('_multiple', '', $info->fieldName);
        }

        $elementType = Str::camelCase($element->getElementType(), true);

        $args = array(
            'isMultiple' => $isMultiple
        );

        if (is_callable($callback = [__CLASS__, "resolve{$elementType}"])) {
            return $callback($element, $args);
        }

        return static::resolveElement($element);
    }

    public static function resolveJBGalleryImage($element, $args)
    {
        $value      = $element->getValue();
        $isMultiple = $args['isMultiple'];

        if (!self::hasMultipleValue($element)) {
            return $value;
        }

        $value = (array) $value;

        return $isMultiple
            ? array_map(function ($value) {
                return is_scalar($value)
                    ? compact('value')
                    : $value;
            }, $value)
            : array_shift($value);
    }

    public static function resolveJBImage($element, $args)
    {
        $value      = $element->getValue();
        $isMultiple = $args['isMultiple'];

        if (!self::hasMultipleValue($element)) {
            return $value;
        }

        $value = (array) $value;

        return $isMultiple
            ? array_map(function ($value) {
                return is_scalar($value)
                    ? compact('value')
                    : $value;
            }, $value)
            : array_shift($value);
    }

    public static function resolveJBGallery($element, $args)
    {
        $value      = $element->getValue();
        $isMultiple = $args['isMultiple'];

        if (!self::hasMultipleValue($element)) {
            return $value;
        }

        $value = (array) $value;

        return $isMultiple
            ? array_map(function ($value) {
                return is_scalar($value)
                    ? compact('value')
                    : $value;
            }, $value)
            : array_shift($value);
    }

    public static function resolveElement($element)
    {
        $value = $element->getValue();

        if (!self::hasMultipleValue($element)) {
            return $value;
        }

        $value = (array) $value;

        return static::isMultiple($element)
            ? array_map(function ($value) {
                return is_scalar($value)
                    ? compact('value')
                    : $value;
            }, $value)
            : array_shift($value);
    }

    protected static function hasMultipleValue(\Element $element)
    {
        return $element instanceof \ElementRepeatable
            || $element instanceof \ElementJBGalleryImage
            || $element instanceof \ElementJBImage
            || $element instanceof \ElementJBGallery;
    }

    protected static function isMultiple(\Element $element)
    {
        return $element instanceof \ElementRepeatable
            && $element->config->get('repeatable');
    }

    protected static function getCategoryType(\Element $element)
    {
        return Str::camelCase(['Zoo', $element->getType()->getApplication()->application_group, 'Category'], true);
    }
}