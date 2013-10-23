<?php
/**
 * Webbuilder useful tools
 *
 * @author Eugene I. Nezhuta <eugene@seotoaster.com>
 * User: iamne
 * Date: 5/8/13
 * Time: 1:29 PM
 */

class Webbuilder_Tools_Misc {

    const HASH_LENGTH = 24;

    const HASH_PREFIX = 'wb_';

    public static function toHash($string) {
        return self::HASH_PREFIX . substr(md5($string), 0, self::HASH_LENGTH);
    }

}