<?php

namespace core;

/**
 * Helper
 */
class Html
{
    /**
     * @param string $content
     * @param bool $doubleEncode
     * @return string
     */
    public static function encode($content, $doubleEncode = true)
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * @param string $email
     * @return string
     */
    public static function mailto($email)
    {
        $email = static::encode($email);
        return "<a href=\"mailto:$email\">$email</a>";
    }
}
