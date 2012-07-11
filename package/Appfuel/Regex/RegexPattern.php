<?php
/**
 * Appfuel
 * Copyright (c) Robert Scott-Buccleuch <rsb.appfuel@gmail.com>
 * See LICENSE file at project root for details.
 */
namespace Appfuel\Regex;

use InvalidArgumentException;

/**
 * Taken from the book Mastering Regular Expressions 3rd Edition. The
 * pattern will take any raw regex and pattern suitable for use with the 
 * preg_* family of functions.
 * 
 * Note: currently this only supports patterns written for the forward slash
 *       delimiter
 */ 
class RegexPattern implements RegexPatternInterface
{
    /**
     * Error message when filter or validation fails
     * @var string
     */
    protected $error = null;

    /**
     * @param   string  $raw
     * @param   string  $modifiers
     * @return  string | false on failure
     */
    public function filter($raw, $modifiers = "")
    {
        $pattern = $this->convert($raw, $modifiers);
        if (false === $this->validate($pattern)) {
            return false;
        }

        return $pattern;
    }

    /**
     * @param   string  $raw        the raw regex 
     * @param   string  $modifiers  chars used to modify the regex
     * @return  string  
     */  
    public function convert($raw, $modifiers = "")
    {
        if (! is_string($raw)) {
            $this->setError("raw regex must be a non empty string");
            return false;
        }

        if (! is_string($modifiers)) {
            $this->setError("regex modifiers must be a string");
            return false;
        }

        /*
         * To convert to a safe pattern, we must wrap the pattern in delimiters
         * (we'll use a pair of forward slashes) and append the modifiers.
         * We must also be sure to escape any unescaped occurences of the 
         * delimiter within the regex, and to escape a regex-ending escape
         * (which, if left alone, would end up escaping the delimiter we append)
         *
         * We can't just blindly escape embedded delimiters, because it would 
         * break a regex containing an already escaped delimiter. For example,
         * if the regex is '\/', a blind escape results in '\\/' which would 
         * not work when eventually wrapped with '/\\//'.
         * 
         * Rather, we'll break down the regex into sections: escaped chars, 
         * unescaped forward slashes (which we'll need to escape), and 
         * everything else. As a special case, we also look out for, and escape
         * a regex-ending escape
         * 
         * this comment was taken more or less directly from the book --rsb
         */
        if (! preg_match('{\\\\(?:/|$)}', $raw)) {
            /*
             * There are no already-escaped forward slashes, and none escaped
             * at the end, so it's safe to blindly escape forward slashes
             */
            $clean = preg_replace('!/!', '\/', $raw);
        }
        else {
           /*
            * This is the pattern we'll use to parse $raw
            * The two parts whose matches we'll need to escape are within
            * capturing parens
            */
            $pattern = '{  [^\\\\/]+  |  \\\\\. |  ( /   |  \\\\$  )  }sx';

            $clean = preg_replace_callback($pattern, function ($matches) {
                if (empty($matches[1])) {
                    return $matches[0];
                }

                return '\\\\' . $matches[1];
            }, $raw);
        }

        return "/$clean/$modifiers";
    }

    /**
     * @param   string  $regex
     * @return  string | false
     */
    public function validate($regex)
    {
        /*
         * To tell if the pattern has errors, we try to use it
         */
        if ($oldTrack = ini_get("track_errors")) {
           $oldMsg = isset($php_errormsg) ? $php_errormsg : false; 
        }
        else {
            ini_set('track_errors', 1);
        }

        /* no that we backup the old message and ensured track_errors is 
         * enabled we are ready to try out the regex
         */
        unset($php_errormsg);

        @preg_match($regex, "");

        $result = isset($php_errormsg) ? $php_errormsg : false;

        /*
         * restore the global state now that we have what we are after
         */
        if ($oldTrack) {
            $php_errormsg = isset($oldMsg) ? $oldMsg : false;
        }
        else {
            ini_set('track_errors', 0);
        }

        if (false !== $result) {
            $this->setError($result);
            return false;
        }

        return true;
    }

    /**
     * @return  string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return  bool
     */
    public function isError()
    {
        return is_string($this->error) && ! empty($this->error);
    }

    /**
     * @return  RegexFilter
     */
    public function clearError()
    {
        $this->error = null;
        return $this;
    }

    /**
     * @param   string  $err
     * @return  null
     */
    protected function setError($err)
    {
        $this->error = $err;
    }
}
