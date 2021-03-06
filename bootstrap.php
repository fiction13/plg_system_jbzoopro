<?php
/*
 * @package   System - JBZoo YOOtheme Pro
 * @version   1.0.2
 * @author    Dmitriy Vasyukov - https://fictionlabs.ru
 * @copyright Copyright (c) 2021 Fictionlabs. All rights reserved.
 * @license   GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 * @link      https://fictionlabs.ru/
 */

namespace YOOtheme;

use YOOtheme\Builder\Joomla\JBZoo\SourceListener;

return [

    'routes' => [
        // noop
    ],

    'events' => [

        'source.init' => [
            SourceListener::class => ['initSource', -20],
        ],

        'customizer.init' => [
            // noop
        ],

        'builder.template' => [
            // noop
        ],

    ],

    'extend' => [
        Builder::class => function (Builder $builder) {
            $builder->addTypePath(Path::get('./elements/*/element.json'));
        },
    ],

];
