<?php

namespace obray\ipp;

class Subscription
{
    private readonly string $printerURI;
    private readonly int $subscriptionId;
    private readonly ?string $user;
    private readonly ?string $password;
    private readonly array $curlOptions;
    private string $requestClass;

    public function __construct(
        string $printerURI,
        int $subscriptionId,
        ?string $user = null,
        ?string $password = null,
        array $curlOptions = [],
        ?string $requestClass = null
    ) {
        $this->printerURI = $printerURI;
        $this->subscriptionId = $subscriptionId;
        $this->user = $user;
        $this->password = $password;
        $this->curlOptions = $curlOptions;
        $this->requestClass = $requestClass ?? \obray\ipp\Request::class;
    }

    public function setRequestClass(string $requestClass): self
    {
        $this->requestClass = $requestClass;
        return $this;
    }

    private function createOperationAttributes(): \obray\ipp\OperationAttributes
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        $operationAttributes->{'notify-subscription-id'} = $this->subscriptionId;
        if (!empty($this->user)) {
            $operationAttributes->{'requesting-user-name'} = $this->user;
        }
        return $operationAttributes;
    }

    private function sendPayload(\obray\ipp\transport\IPPPayload $payload): \obray\ipp\transport\IPPPayload
    {
        $requestClass = $this->requestClass;
        return $requestClass::send(
            $this->printerURI,
            $payload->encode(),
            $this->user,
            $this->password,
            $this->curlOptions
        );
    }

    private function buildPayload(
        int $operationCode,
        int $requestId,
        \obray\ipp\OperationAttributes $operationAttributes
    ): \obray\ipp\transport\IPPPayload {
        \obray\ipp\spec\OperationRequestValidator::validate(
            $operationCode,
            $operationAttributes
        );

        return new \obray\ipp\transport\IPPPayload(
            new \obray\ipp\types\VersionNumber('1.1'),
            new \obray\ipp\types\Operation($operationCode),
            new \obray\ipp\types\Integer($requestId),
            null,
            $operationAttributes
        );
    }

    /**
     * Get Notifications
     *
     * RFC 3996 §9.1 convenience wrapper — polls for pending events on this
     * single subscription. Pass `$lastSequenceNumber` to retrieve only events
     * newer than the last one the client has already processed.
     *
     * @param int      $requestId           Client request id
     * @param int|null $lastSequenceNumber  Highest sequence number already seen (optional)
     * @param bool|null $wait               Block until an event is available (optional)
     *
     * @return \obray\ipp\transport\IPPPayload
     */
    public function getNotifications(int $requestId = 1, ?int $lastSequenceNumber = null, ?bool $wait = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = new \obray\ipp\OperationAttributes();
        $operationAttributes->{'printer-uri'} = $this->printerURI;
        if (!empty($this->user)) {
            $operationAttributes->{'requesting-user-name'} = $this->user;
        }
        $operationAttributes->{'notify-subscription-ids'} = $this->subscriptionId;
        if ($lastSequenceNumber !== null) {
            $operationAttributes->{'notify-sequence-numbers'} = $lastSequenceNumber;
        }
        if ($wait !== null) {
            $operationAttributes->{'notify-wait'} = $wait;
        }

        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_NOTIFICATION,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function getSubscriptionAttributes(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::GET_SUBSCRIPTION_ATTRIBUTES,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function renewSubscription(int $requestId = 1, ?int $leaseDuration = null): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        if ($leaseDuration !== null) {
            $operationAttributes->{'notify-lease-duration'} = $leaseDuration;
        }
        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::RENEW_SUBSCRIPTION,
                $requestId,
                $operationAttributes
            )
        );
    }

    public function cancelSubscription(int $requestId = 1): \obray\ipp\transport\IPPPayload
    {
        $operationAttributes = $this->createOperationAttributes();
        return $this->sendPayload(
            $this->buildPayload(
                \obray\ipp\types\Operation::CANCEL_SUBSCRIPTION,
                $requestId,
                $operationAttributes
            )
        );
    }
}
