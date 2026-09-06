<?php
declare(strict_types=1);

use obray\ipp\enums\JobState;
use PHPUnit\Framework\TestCase;

final class JobStateTest extends TestCase
{
    /** @dataProvider states */
    public function testJobStateNamesMatchRfc8011(int $code, string $name): void
    {
        $constructed = new JobState($code);
        $decoded = (new JobState())->decode(pack('N', $code), 0, 4);

        foreach ([$constructed, $decoded] as $state) {
            $this->assertSame($code, $state->getValue());
            $this->assertSame($name, (string) $state);
            $this->assertSame('"' . $name . '"', json_encode($state));
        }
    }

    public function states(): array
    {
        // RFC 8011 §5.3.7; the last case preserves vendor-value fallback.
        return [
            [3, 'pending'],
            [4, 'pending-held'],
            [5, 'processing'],
            [6, 'processing-stopped'],
            [7, 'canceled'],
            [8, 'aborted'],
            [9, 'completed'],
            [0x1000, '0x1000'],
        ];
    }

    public function testExistingConstantNamesRemainCompatible(): void
    {
        $this->assertSame(4, JobState::PENDINGHELD);
        $this->assertSame(6, JobState::PROCESSINGSTOPPED);
    }
}
