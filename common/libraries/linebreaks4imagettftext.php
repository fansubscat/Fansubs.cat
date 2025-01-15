<?php

/**
 * Linebreaks4imagettftext v1.0.3
 *
 * Copyright (c) 2018-2022 Andrew G. Johnson <andrew@andrewgjohnson.com>
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
 * Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN
 * AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * PHP version 5
 *
 * @category  Andrewgjohnson
 * @package   Linebreaks4imagettftext
 * @author    Andrew G. Johnson <andrew@andrewgjohnson.com>
 * @copyright 2018-2022 Andrew G. Johnson <andrew@andrewgjohnson.com>
 * @license   https://opensource.org/licenses/mit/ The MIT License
 * @link      https://github.com/andrewgjohnson/linebreaks4imagettftext
 */

namespace andrewgjohnson;

if (!function_exists('andrewgjohnson\\linebreaks4imagettftext')) {
    /**
     * Linebreaks4imagettftext is a function to automatically insert line breaks into
     * your text while using PHP's imagettftext function
     *
     * @param float  $size               <p>The font size. Depending on your version
     *    of GD, this should be specified as the pixel size (GD1) or point size
     *    (GD2).</p>
     * @param float  $angle              <p>The angle in degrees, with 0 degrees
     *    being left-to-right reading text. Higher values represent a counter-
     *    clockwise rotation. For example, a value of 90 would result in bottom-to-
     *    top reading text.</p>
     * @param string $fontfile           <p>The path to the TrueType font you wish
     *    to use.</p><p>Depending on which version of the GD library PHP is using,
     *    when fontfile does not begin with a leading / then .ttf will be appended
     *    to the filename and the library will attempt to search for that filename
     *    along a library-defined font path.</p><p>When using versions of the GD
     *    library lower than 2.0.18, a space character, rather than a semicolon, was
     *    used as the 'path separator' for different font files. Unintentional use
     *    of this feature will result in the warning message: Warning: Could not
     *    find/open font. For these affected versions, the only solution is moving
     *    the font to a path which does not contain spaces.</p>
     * @param string $text               <p>The text string in UTF-8 encoding.</p>
     *    <p>May include decimal numeric character references (of the form:
     *    &amp;#8364;) to access characters in a font beyond position 127. The
     *    hexadecimal format (like &amp;#xA9;) is supported.Strings in UTF-8
     *    encoding can be passed directly.</p><p>Named entities, such as &amp;copy;,
     *    are not supported. Consider using html_entity_decode to decode these named
     *    entities into UTF-8 strings.</p><p>If a character is used in the string
     *    which is not supported by the font, a hollow rectangle will replace the
     *    character.</p>
     * @param int    $maximumWidth       <p>The maximum width (in pixels) a line
     *    should be before adding a line break.</p>
     * @param int    $lineBreakCharacter <p>The character(s) to use when adding a
     *    line break (default is PHP_EOL).</p>
     *
     * @return string Returns a string that is nearly identical to $text; the only
     * difference is the newly added line breaks.
     */
    function linebreaks4imagettftext(
        $size,
        $angle,
        $fontfile,
        $text,
        $maximumWidth,
        $maxLines,
        $lineBreakCharacter = PHP_EOL
    ) {
        // create an array with all the words
        $words = explode(' ', $text);

        // process all our words to generate $textWithLineBreaks
        $textWithLineBreaks = '';
        $currentLine = '';
        $lineCount = 0;

        foreach ($words as $position => $word) {
            // handle the first word
            if ($position === 0) {
                $currentLine = $word;
            } else {
                // calculate the text's size if we were to add the word
                $textDimensions = imagettfbbox(
                    $size,
                    $angle,
                    $fontfile,
                    $currentLine . ' ' . $word
                );
                $textLeft = min($textDimensions[0], $textDimensions[6]);
                $textRight = max($textDimensions[2], $textDimensions[4]);
                $textWidth = $textRight - $textLeft;
                if ($textWidth > $maximumWidth) {
                    // the text is too wide with the added word
                    if ($lineCount + 1 < $maxLines) {
                        // add the current line and start a new one
                        $textWithLineBreaks .= $currentLine . $lineBreakCharacter;
                        $currentLine = $word;
                        $lineCount++;
                    } else {
                        // if we're at the last allowed line, truncate it
                        $ellipsis = '…';
                        $remainingWidth = $maximumWidth;

                        // calculate the width of the ellipsis
                        $ellipsisDimensions = imagettfbbox($size, $angle, $fontfile, $ellipsis);
                        $ellipsisWidth = max($ellipsisDimensions[2], $ellipsisDimensions[4]) - min($ellipsisDimensions[0], $ellipsisDimensions[6]);
                        $remainingWidth -= $ellipsisWidth;

                        // truncate the word character by character
                        $truncatedLine = '';
                        $currentLine .= ' ' . $word;
                        for ($i = 0; $i < strlen($currentLine); $i++) {
                            $char = $currentLine[$i];
                            $textDimensions = imagettfbbox(
                                $size,
                                $angle,
                                $fontfile,
                                $truncatedLine . $char
                            );
                            $textLeft = min($textDimensions[0], $textDimensions[6]);
                            $textRight = max($textDimensions[2], $textDimensions[4]);
                            $charWidth = $textRight - $textLeft;
                            if ($charWidth > $remainingWidth) {
                                break;
                            }
                            $truncatedLine .= $char;
                        }

                        // add the truncated line with ellipsis and return
                        $textWithLineBreaks .= trim($truncatedLine) . $ellipsis;
                        return $textWithLineBreaks;
                    }
                } else {
                    // we have space on the current line for the added word
                    $currentLine .= ' ' . $word;
                }
            }
        }

        // add the last line if any
        if ($lineCount < $maxLines) {
            $textWithLineBreaks .= $currentLine;
        }

        return $textWithLineBreaks;
    }
}
