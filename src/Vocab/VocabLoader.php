<?php

declare(strict_types=1);

namespace Yethee\Tiktoken\Vocab;

use Yethee\Tiktoken\Exception\IOError;

interface VocabLoader
{
    /**
     * @param non-empty-string $uri
     *
     * @throws IOError
     * @param string|\Yethee\Tiktoken\Vocab\null $checksum
     */
    public function load(string $uri, $checksum = null): Vocab;

    /**
     * @param non-empty-string $uri
     *
     * @return non-empty-string
     *
     * @throws IOError
     * @param string|\Yethee\Tiktoken\Vocab\null $checksum
     */
    public function loadFile(string $uri, $checksum = null): string;
}
