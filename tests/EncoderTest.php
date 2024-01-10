<?php

declare(strict_types=1);

namespace Yethee\Tiktoken\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Yethee\Tiktoken\Encoder;
use Yethee\Tiktoken\EncoderProvider;
use Yethee\Tiktoken\Vocab\Vocab;

final class EncoderTest extends TestCase
{
    /** @param list<int> $tokens */
    #[DataProvider('provideDataForFlatTokenization')]
    public function testEncode(string $text, array $tokens): void
    {
        $encoder = self::getEncoder('cl100k_base');

        self::assertSame($tokens, $encoder->encode($text));
    }

    /** @param list<int> $tokens */
    #[DataProvider('provideDataForFlatTokenization')]
    public function testDecode(string $text, array $tokens): void
    {
        $encoder = self::getEncoder('cl100k_base');

        self::assertSame($text, $encoder->decode($tokens));
    }

    /**
     * @param positive-int    $maxTokensPerChunk
     * @param list<list<int>> $expected
     */
    #[DataProvider('provideDataForChunkBasedTokenization')]
    public function testEncodeInChunks(Encoder $encoder, string $text, int $maxTokensPerChunk, array $expected): void
    {
        self::assertSame($expected, $encoder->encodeInChunks($text, $maxTokensPerChunk));
    }

    /**
     * @return iterable<array{string, list<int>}>
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function provideDataForFlatTokenization(): iterable
    {
        yield 'hello world' => ['hello world', [15339, 1917]];

        yield 'привет мир' => ['привет мир', [8164, 2233, 28089, 8341, 11562, 78746]];

        yield 'emoji' => ['🌶', [9468, 234, 114]];

        yield 'new line character' => [".\n", [627]];
    }

    /**
     * @return iterable<array{
     *     Encoder,
     *     string,
     *     positive-int,
     *     list<list<int>>
     * }>
     *
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function provideDataForChunkBasedTokenization(): iterable
    {
        yield 'p50k_base' => [
            self::getEncoder('p50k_base'),
            '1 2 hello，world 3 4',
            3,
            [
                [16, 362, 23748],
                [171, 120, 234],
                [6894, 513, 604],
            ],
        ];

        yield 'cl100k_base' => [
            self::getEncoder('cl100k_base'),
            '1 2 hello，world 3 4',
            5,
            [
                [16, 220, 17, 24748],
                [3922, 14957, 220, 18, 220],
                [19],
            ],
        ];
    }

    /** @param non-empty-string $encoding */
    private static function getEncoder(string $encoding): Encoder
    {
        return new Encoder(
            $encoding,
            Vocab::fromFile(__DIR__ . '/Fixtures/' . $encoding . '.tiktoken'),
            EncoderProvider::ENCODINGS[$encoding]['pat'],
        );
    }
}
