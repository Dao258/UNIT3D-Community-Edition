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

return [
    /*
    |--------------------------------------------------------------------------
    | Enable Minifier
    |--------------------------------------------------------------------------
    |
    | Use this to turn on and off the minifier middleware
    |
    */
    "enable" => env("HTML_MINIFIER_ENABLE", true),

    /*
    |--------------------------------------------------------------------------
    | Automatically add semicolon at the end of code for CSS
    |--------------------------------------------------------------------------
    |
    | Please set this field to false if your CSS code is buggy or not working after using MinifyCss Middleware
    |
    */
    "css_automatic_insert_semicolon" => env("HTML_MINIFIER_CSS_AUTOMATIC_INSERT_SEMICOLON", false),

    /*
    |--------------------------------------------------------------------------
    | Automatically add semicolon at the end of code for Javascript
    |--------------------------------------------------------------------------
    |
    | Please set this field to false if your Javascript code has a bug or doesn't work after using MinifyJavascript Middleware
    |
    | And don't forget to always end with a semicolon if this field is set to false.
    |
    */
    "js_automatic_insert_semicolon" => env("HTML_MINIFIER_JS_AUTOMATIC_INSERT_SEMICOLON", false),

    /*
    |--------------------------------------------------------------------------
    | Enable uncommenting in HTML
    |--------------------------------------------------------------------------
    |
    | Set this section to false if HTML comments don't want to be removed
    |
    | Note: This setting will apply if using MinifyHtml Middleware
    |
    */
    "remove_comments" => env("HTML_MINIFIER_REMOVE_COMMENTS", false),

    /*
    |--------------------------------------------------------------------------
    | Obfuscate Javascript
    |--------------------------------------------------------------------------
    |
    | This setting will apply if using MinifyJavascript Middleware
    |
    | If set to true then javascript code will be obfuscated, Will translate js code to ord() php function
    | And will be decoded with String.fromCharCode() javascript function
    |
    | Note: Maybe this function will make your code long.
    |
    */
    "obfuscate_javascript" => env("HTML_MINIFIER_OBFUSCATE_JS", false),

    /*
    |--------------------------------------------------------------------------
    | Ignore Routes
    |--------------------------------------------------------------------------
    |
    | Checking will use the request()->is() function,
    | And if the route matches it will be ignored / Not minified.
    | You can use * as a wildcard.
    |
    */

    "ignore" => [],
];