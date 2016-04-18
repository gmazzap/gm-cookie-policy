<?php
/*
 * This file is part of the gm-cookie-policy package.
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
 * @package gm-cookie-policy
 */
interface RendererInterface
{
    /**
     * Render a template with given context.
     *
     * @param string $template
     * @param array  $data
     * @return string
     */
    public function render($template, array $data = []);
}
