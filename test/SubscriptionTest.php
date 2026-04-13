<?php
$loader = require_once 'vendor/autoload.php';

use obray\ipp\test\FakeRequest;
use PHPUnit\Framework\TestCase;

class SubscriptionTest extends TestCase
{
    protected \obray\ipp\Subscription $subscription;

    protected function setUp(): void
    {
        FakeRequest::reset();

        $this->subscription = (new \obray\ipp\Subscription(
            'ipp://localhost/printers/CUPS_PDF',
            7,
            'demo-user',
            'secret'
        ))->setRequestClass(FakeRequest::class);
    }

    public function testGetSubscriptionAttributesBuildsExpectedPayload(): void
    {
        $this->subscription->getSubscriptionAttributes(200);

        $this->assertSame(\obray\ipp\types\Operation::GET_SUBSCRIPTION_ATTRIBUTES, FakeRequest::$lastCall['operation']);
        $this->assertSame(200, FakeRequest::$lastCall['requestId']);
        $this->assertSame('1.1', FakeRequest::$lastCall['version']);
        $this->assertSame('ipp://localhost/printers/CUPS_PDF', (string) FakeRequest::$lastCall['operationAttributes']->{'printer-uri'});
        $this->assertSame('7', (string) FakeRequest::$lastCall['operationAttributes']->{'notify-subscription-id'});
    }

    public function testRenewSubscriptionBuildsExpectedPayload(): void
    {
        $this->subscription->renewSubscription(201);

        $this->assertSame(\obray\ipp\types\Operation::RENEW_SUBSCRIPTION, FakeRequest::$lastCall['operation']);
        $this->assertSame(201, FakeRequest::$lastCall['requestId']);
        $this->assertFalse(FakeRequest::$lastCall['operationAttributes']->has('notify-lease-duration'));
    }

    public function testRenewSubscriptionWithLeaseDurationBuildsExpectedPayload(): void
    {
        $this->subscription->renewSubscription(202, 3600);

        $this->assertSame(\obray\ipp\types\Operation::RENEW_SUBSCRIPTION, FakeRequest::$lastCall['operation']);
        $this->assertSame('3600', (string) FakeRequest::$lastCall['operationAttributes']->{'notify-lease-duration'});
    }

    public function testCancelSubscriptionBuildsExpectedPayload(): void
    {
        $this->subscription->cancelSubscription(203);

        $this->assertSame(\obray\ipp\types\Operation::CANCEL_SUBSCRIPTION, FakeRequest::$lastCall['operation']);
        $this->assertSame(203, FakeRequest::$lastCall['requestId']);
        $this->assertSame('7', (string) FakeRequest::$lastCall['operationAttributes']->{'notify-subscription-id'});
    }
}
