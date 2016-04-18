<?php
/*
 * This file is part of the elesia package.
 *
 * (c) Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GM\CookiePolicy;

/**
 * @author  Giuseppe Mazzapica <giuseppe.mazzapica@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @package elesia
 */
final class SimpleRenderer implements RendererInterface
{
    /**
     * Very simple render engine. Template files can access variables using `$this->variableName`.
     * No other variable than the ones passed in `$data` param are available in templates.
     *
     * @param string $template Full path to template file to render
     * @param array $data
     * @return string
     */
    public function render($template, array $data = [])
    {
        $context = (object) $data;

        $renderer = \Closure::bind(function ($template) {
            ob_start();
            /** @noinspection PhpIncludeInspection */
            @include $template;

            return trim(ob_get_clean());
        }, $context, 'stdClass');

        return $renderer($template);
    }
}
