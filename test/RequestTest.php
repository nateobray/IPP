<?php
declare(strict_types=1);

final class RequestTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider buildPostUrlProvider
     */
    public function testBuildPostUrlTranslatesIppSchemes(string $printerUri, string $expected): void
    {
        $method = new \ReflectionMethod(\obray\ipp\Request::class, 'buildPostUrl');
        $method->setAccessible(true);

        $this->assertSame($expected, $method->invoke(null, $printerUri));
    }

    public function buildPostUrlProvider(): array
    {
        return [
            'ipp default port' => [
                'ipp://printer.example/ipp/print',
                'http://printer.example:631/ipp/print',
            ],
            'ipp explicit port' => [
                'ipp://printer.example:8631/ipp/print',
                'http://printer.example:8631/ipp/print',
            ],
            'ipps default port' => [
                'ipps://printer.example/ipp/print',
                'https://printer.example:443/ipp/print',
            ],
            'ipps explicit port' => [
                'ipps://printer.example:8443/ipp/print',
                'https://printer.example:8443/ipp/print',
            ],
            'http unchanged' => [
                'https://printer.example/ipp/print',
                'https://printer.example/ipp/print',
            ],
        ];
    }
}
