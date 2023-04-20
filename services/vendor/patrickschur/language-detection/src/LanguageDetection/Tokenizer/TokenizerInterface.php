<?php

declare(strict_types = 1);

namespace LanguageDetection\Tokenizer;

/**
 * Interface TokenizerInterface
 *
 * @copyright Patrick Schur
 * @license https://opensource.org/licenses/mit-license.html MIT
 * @author Patrick Schur <patrick_schur@outlook.de>
 * @package LanguageDetection
 */
interface TokenizerInterface
{
    public function tokenize(string $str): array;
}