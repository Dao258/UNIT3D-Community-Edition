<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Middleware;

class MinifyJavascript extends Minifier
{
    protected static $allowInsertSemicolon;

    protected function apply()
    {
        static::$minifyJavascriptHasBeenUsed = true;

        $obfuscate = (bool) config("html-minifier.obfuscate_javascript", false);

        static::$allowInsertSemicolon = (bool) config("html-minifier.js_automatic_insert_semicolon", true);

        foreach ($this->getByTag("script") as $el)
        {
            $value = $this->replace($el->nodeValue);

            // Is the obfuscate function enabled?
            if ($obfuscate)
            {
                $value = $this->obfuscate($value);
            }
            $el->nodeValue = "";
            $el->appendChild(static::$dom->createTextNode($value));
        }

        return static::$dom->saveHtml();
    }

    protected function insertSemicolon($value)
    {
        // delete all comments
        $value = \preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:|\\\|\'|\")\/\/.*))/', '', $value);

        $value = \preg_replace_callback('/(`[\S\s]*?[^\\\`]`)/', function($m) {
            return \preg_replace('/\n+/', '', $m[1]);
        }, $value);

        $result = [];
        $code = \explode("\n", \trim($value));

        $patternRegex = [
            // find string ending with {, [, (, ,, ;, =>, :, ?, .remove all comments
            '#(?:({|\[|\(|,|;|=>|\:|\?|\.))$#',
            // find blank spaces
            '#^\s*$#',
            // find first and last string do, else
            '#^(do|else)$#'
        ];

        $loop = 0;

        foreach ($code as $line)
        {
            $loop++;
            $insert = false;
            $shouldInsert = true;

            foreach ($patternRegex as $pattern)
            {
                // if the pattern doesn't match it means you can add a semicolon
                $match = \preg_match($pattern, trim($line));
                $shouldInsert = $shouldInsert && (bool) !$match;
            }

            if ($shouldInsert)
            {
                $i = $loop;

                while (true)
                {
                    if ($i >= count($code))
                    {
                        $insert = true;
                        break;
                    }

                    $c = trim($code[$i]);
                    $i++;

                    if (!$c)
                    {
                        continue;
                    }

                    $insert = true;
                    $regex = ['#^(\?|\:|,|\.|{|}|\)|\])#'];

                    foreach ($regex as $r)
                    {
                        $insert = $insert && (bool) !preg_match($r, $c);
                    }

                    if ($insert && \preg_match("#(?:\\})$#", trim($line)) && \preg_match("#^(else|elseif|else\s*if|catch)#", $c)) {
                        $insert = false;
                    }

                    break;
                }
            }

            if ($insert)
            {
                $result[] = sprintf("%s;", $line);
            }
            else
            {
                $result[] = $line;
            }

        }

        return \implode("\n", $result);
    }

    protected function replace($value)
    {
        if (static::$allowInsertSemicolon)
        {
            $value = $this->insertSemicolon($value);
        }

        return trim(preg_replace([
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ],[
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ], $value));
    }

    protected function obfuscate($value)
    {
        $ords = [];

        for ($i = 0, $iMax = strlen($value); $i < $iMax; $i++)
        {
            $ords[] = ord($value[$i]);
        }

        $template = sprintf("
        eval(((_, __, ___, ____, _____, ______, _______) => {
            ______[___](x => _______[__](String[____](x)));
            return _______[_](_____)
        })('join', 'push', 'forEach', 'fromCharCode', '', %s, []))

        ", json_encode($ords, JSON_THROW_ON_ERROR));

        return $this->replace($template);
    }
}