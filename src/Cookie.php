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
class Cookie
{
    const COOKIE         = 'gm-cookie-policy';
    const ACCEPTED_QUERY = 'cookie-policy-accepted';

    /**
     * @return mixed
     */
    public static function expiration()
    {
        return apply_filters('cookie-policy.cookie-expiration', 3 * MONTH_IN_SECONDS);
    }

    /**
     * @return bool
     */
    public function exists()
    {
        return filter_input(INPUT_COOKIE, self::COOKIE, FILTER_SANITIZE_NUMBER_INT) > 0;
    }

    /**
     * @return bool
     */
    public function save()
    {
        if (headers_sent()) {
            return false;
        }

        $now = time();
        $url = parse_url(home_url());
        $host = isset($url['host']) ? $url['host'] : null;

        return setcookie(
            self::COOKIE,
            $now,
            $now + self::expiration(),
            '/',
            $host,
            is_ssl(),
            true
        );
    }
}
