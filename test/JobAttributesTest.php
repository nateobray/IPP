<?php
$loader = require_once 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

class JobAttributesTest extends TestCase
{
    public function testAttributeGroupSupportsCommonIpp11JobTemplateAttributes()
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('job-uri', 'ipp://printer.example/jobs/42');
        $jobAttributes->set('job-printer-uri', 'ipp://printer.example/printers/main');
        $jobAttributes->set('job-name', 'Quarterly report');
        $jobAttributes->set('job-originating-user-name', 'alice');
        $jobAttributes->set('document-format', 'application/pdf');
        $jobAttributes->set('attributes-charset', 'utf-8');
        $jobAttributes->set('attributes-natural-language', 'en');
        $jobAttributes->set('job-state', \obray\ipp\enums\JobState::PENDING);
        $jobAttributes->set('job-state-reasons', ['job-incoming', 'job-data-insufficient']);
        $jobAttributes->set('job-state-message', 'Waiting for resources');
        $jobAttributes->set('job-detailed-status-messages', ['detail one', 'detail two']);
        $jobAttributes->set('job-document-access-errors', ['fetch failed']);
        $jobAttributes->set('job-hold-until', 'indefinite');
        $jobAttributes->set('job-message-from-operator', 'Load tray 2');
        $jobAttributes->set('number-of-documents', 2);
        $jobAttributes->set('output-device-assigned', 'printer-output-bin-1');
        $jobAttributes->set('job-k-octets', 512);
        $jobAttributes->set('job-impressions', 16);
        $jobAttributes->set('job-media-sheets', 8);
        $jobAttributes->set('job-k-octets-processed', 128);
        $jobAttributes->set('job-impressions-completed', 4);
        $jobAttributes->set('job-media-sheets-completed', 2);
        $jobAttributes->set('time-at-creation', 100);
        $jobAttributes->set('time-at-processing', 110);
        $jobAttributes->set('time-at-completed', 120);

        $this->assertSame('ipp://printer.example/jobs/42', (string) $jobAttributes->{'job-uri'});
        $this->assertSame('ipp://printer.example/printers/main', (string) $jobAttributes->{'job-printer-uri'});
        $this->assertSame('Quarterly report', (string) $jobAttributes->{'job-name'});
        $this->assertSame('alice', (string) $jobAttributes->{'job-originating-user-name'});
        $this->assertSame('application/pdf', (string) $jobAttributes->{'document-format'});
        $this->assertSame('utf-8', (string) $jobAttributes->{'attributes-charset'});
        $this->assertSame('en', (string) $jobAttributes->{'attributes-natural-language'});
        $this->assertSame('pending', (string) $jobAttributes->{'job-state'});
        $this->assertIsArray($jobAttributes->{'job-state-reasons'});
        $this->assertSame('job-incoming', (string) $jobAttributes->{'job-state-reasons'}[0]);
        $this->assertSame('job-data-insufficient', (string) $jobAttributes->{'job-state-reasons'}[1]);
        $this->assertSame('Waiting for resources', (string) $jobAttributes->{'job-state-message'});
        $this->assertIsArray($jobAttributes->{'job-detailed-status-messages'});
        $this->assertSame('detail one', (string) $jobAttributes->{'job-detailed-status-messages'}[0]);
        $this->assertSame('detail two', (string) $jobAttributes->{'job-detailed-status-messages'}[1]);
        $this->assertIsArray($jobAttributes->{'job-document-access-errors'});
        $this->assertSame('fetch failed', (string) $jobAttributes->{'job-document-access-errors'}[0]);
        $this->assertSame('indefinite', (string) $jobAttributes->{'job-hold-until'});
        $this->assertSame('Load tray 2', (string) $jobAttributes->{'job-message-from-operator'});
        $this->assertSame('2', (string) $jobAttributes->{'number-of-documents'});
        $this->assertSame('printer-output-bin-1', (string) $jobAttributes->{'output-device-assigned'});
        $this->assertSame('512', (string) $jobAttributes->{'job-k-octets'});
        $this->assertSame('16', (string) $jobAttributes->{'job-impressions'});
        $this->assertSame('8', (string) $jobAttributes->{'job-media-sheets'});
        $this->assertSame('128', (string) $jobAttributes->{'job-k-octets-processed'});
        $this->assertSame('4', (string) $jobAttributes->{'job-impressions-completed'});
        $this->assertSame('2', (string) $jobAttributes->{'job-media-sheets-completed'});
    }


    public function testInstantiationWithoutArrayOfAttributes() {

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('page-ranges','1-2');

        $this->assertEquals(new \obray\ipp\types\RangeOfInteger(1,2),$jobAttributes->{'page-ranges'}->getAttributeValueClass());

    }

    public function testInstantiationWithArrayOfAttributes() {

        $attributes = [
            'job-uri' => 'ipps://www.cups.org/ipp/2234451',
            'page-ranges' => '3-4'
        ];

        $jobAttributes = new \obray\ipp\JobAttributes($attributes);

        $this->assertEquals(new \obray\ipp\types\URI('ipps://www.cups.org/ipp/2234451'),$jobAttributes->{'job-uri'}->getAttributeValueClass());
        $this->assertEquals(new \obray\ipp\types\RangeOfInteger(3,4),$jobAttributes->{'page-ranges'}->getAttributeValueClass());

    }

    public function testJobStateReasonsCanBeSet() {

        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('job-state-reasons',new \obray\ipp\enums\JobStateReasons(\obray\ipp\enums\JobStateReasons::jobIncoming));

        $this->assertEquals('jobincoming',(string) $jobAttributes->{'job-state-reasons'});

    }

    public function testDateTimeAttributesCanBeSet(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('date-time-at-creation', '2019-05-18 23:45:32.4-0700');

        $this->assertSame(
            '2019-05-18 23:45:32.400-0700',
            (string) $jobAttributes->{'date-time-at-creation'}
        );
    }

    public function testMultiValueJobTextAttributesDecodeToArrays(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('job-detailed-status-messages', ['detail one', 'detail two']);
        $jobAttributes->set('job-document-access-errors', ['fetch failed', 'timeout']);

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'job-detailed-status-messages'});
        $this->assertSame('detail one', (string) $decoded->{'job-detailed-status-messages'}[0]);
        $this->assertSame('detail two', (string) $decoded->{'job-detailed-status-messages'}[1]);
        $this->assertIsArray($decoded->{'job-document-access-errors'});
        $this->assertSame('fetch failed', (string) $decoded->{'job-document-access-errors'}[0]);
        $this->assertSame('timeout', (string) $decoded->{'job-document-access-errors'}[1]);
    }

    public function testCopiesUsesIntegerSyntax(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('copies', 2);

        $this->assertInstanceOf(
            \obray\ipp\types\Integer::class,
            $jobAttributes->{'copies'}->getAttributeValueClass()
        );
        $this->assertSame('2', (string) $jobAttributes->{'copies'});
    }

    public function testFinishingsSupportsOneSetOfEnumValues(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('finishings', [
            \obray\ipp\enums\Finishings::staple,
            \obray\ipp\enums\Finishings::staple_top_left,
        ]);

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'finishings'});
        $this->assertSame('staple', (string) $decoded->{'finishings'}[0]);
        $this->assertSame('staple-top-left', (string) $decoded->{'finishings'}[1]);
    }

    public function testPageRangesSupportsOneSetOfRangeOfIntegerValues(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('page-ranges', ['1-2', '5-6']);

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'page-ranges'});
        $this->assertSame('1-2', (string) $decoded->{'page-ranges'}[0]);
        $this->assertSame('5-6', (string) $decoded->{'page-ranges'}[1]);
    }

    public function testKeywordOrNameTemplateAttributesCanUseExplicitNameSyntax(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('job-hold-until', new \obray\ipp\types\NameWithoutLanguage('custom-window'));
        $jobAttributes->set('job-sheets', new \obray\ipp\types\NameWithoutLanguage('banner-sheet'));
        $jobAttributes->set('media', new \obray\ipp\types\NameWithoutLanguage('custom-tray'));

        $this->assertInstanceOf(
            \obray\ipp\types\NameWithoutLanguage::class,
            $jobAttributes->{'job-hold-until'}->getAttributeValueClass()
        );
        $this->assertInstanceOf(
            \obray\ipp\types\NameWithoutLanguage::class,
            $jobAttributes->{'job-sheets'}->getAttributeValueClass()
        );
        $this->assertInstanceOf(
            \obray\ipp\types\NameWithoutLanguage::class,
            $jobAttributes->{'media'}->getAttributeValueClass()
        );
    }

    // PWG5100.2 — output-bin

    public function testOutputBinEncodesAsKeyword(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('output-bin', 'face-down');

        $this->assertSame('face-down', (string) $jobAttributes->{'output-bin'});
        $this->assertInstanceOf(
            \obray\ipp\types\Keyword::class,
            $jobAttributes->{'output-bin'}->getAttributeValueClass()
        );
    }

    public function testOutputBinRoundTripsViaEncodeDecode(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('output-bin', 'top');

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        $this->assertSame('top', (string) $decoded->{'output-bin'});
    }

    // PWG5100.1 — extended finishings enum values

    public function testFinishingsPwg5100Point1ValuesAreRecognised(): void
    {
        $cases = [
            \obray\ipp\enums\Finishings::fold          => 'fold',
            \obray\ipp\enums\Finishings::trim          => 'trim',
            \obray\ipp\enums\Finishings::bale          => 'bale',
            \obray\ipp\enums\Finishings::booklet_maker => 'booklet-maker',
            \obray\ipp\enums\Finishings::jog_offset    => 'jog-offset',
            \obray\ipp\enums\Finishings::coat          => 'coat',
            \obray\ipp\enums\Finishings::laminate      => 'laminate',
            \obray\ipp\enums\Finishings::staple_triple_left  => 'staple-triple-left',
            \obray\ipp\enums\Finishings::bind_left     => 'bind-left',
            \obray\ipp\enums\Finishings::trim_after_pages    => 'trim-after-pages',
            \obray\ipp\enums\Finishings::punch_top_left      => 'punch-top-left',
            \obray\ipp\enums\Finishings::punch_quad_bottom   => 'punch-quad-bottom',
            \obray\ipp\enums\Finishings::fold_accordion      => 'fold-accordion',
            \obray\ipp\enums\Finishings::fold_half     => 'fold-half',
            \obray\ipp\enums\Finishings::fold_letter   => 'fold-letter',
            \obray\ipp\enums\Finishings::fold_z        => 'fold-z',
        ];

        foreach ($cases as $enumValue => $expectedName) {
            $jobAttributes = new \obray\ipp\JobAttributes();
            $jobAttributes->set('finishings', $enumValue);
            $this->assertSame($expectedName, (string) $jobAttributes->{'finishings'}, "finishings enum value $enumValue should render as '$expectedName'");
        }
    }

    public function testFinishingsPwg5100Point1ValuesRoundTrip(): void
    {
        $jobAttributes = new \obray\ipp\JobAttributes();
        $jobAttributes->set('finishings', [
            \obray\ipp\enums\Finishings::fold_half,
            \obray\ipp\enums\Finishings::bind_left,
            \obray\ipp\enums\Finishings::punch_dual_top,
        ]);

        $decoded = new \obray\ipp\JobAttributes();
        $offset = 0;
        $decoded->decode($jobAttributes->encode(), $offset);

        $this->assertIsArray($decoded->{'finishings'});
        $this->assertSame('fold-half', (string) $decoded->{'finishings'}[0]);
        $this->assertSame('bind-left', (string) $decoded->{'finishings'}[1]);
        $this->assertSame('punch-dual-top', (string) $decoded->{'finishings'}[2]);
    }


}
