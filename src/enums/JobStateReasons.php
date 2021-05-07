<?php
namespace obray\ipp\enums;

class JobStateReasons extends \obray\ipp\types\Enum
{
    const none = 'none';
    const jobIncoming = 'job-incoming';
    const jobDataInsufficient = 'job-data-insufficient';
    const documentAccessError = 'document-access-error';
    const submissionInterrupted = 'submission-interrupted';
    const jobOutgoing = 'job-outgoing';
    const jobHoldUntilSpecified = 'job-hold-until-specified';
    const resourcesAreNotReady = 'resources-are-not-ready';
    const printerStoppedPartly = 'printer-stopped-partly';
    const printerStopped = 'printer-stopped';
    const jobInterpreting = 'job-interpreting';
    const jobQueued = 'job-queued';
    const jobTransforming = 'job-transforming';
    const jobQueuedForMarker = 'job-queued-for-marker';
    const jobPrinting = 'job-printing';
    const jobCanceledByUser = 'job-canceled-by-user';
    const jobCanceledByOperator = 'job-canceled-by-operator';
    const jobCanceledAtDevice = 'job-canceled-at-device';
    const abortedBySystem = 'aborted-by-system';
    const unsupportedCompression = 'unsupported-compression';
    const compressionError = 'compression-error';
    const unsupportedDocumentFormat = 'unsupported-document-format';
    const documentFormatError = 'document-format-error';
    const processingToStopPoint = 'processing-to-stop-point';
    const serviceOffLine = 'service-off-line';
    const jobCompletedSuccessfully = 'job-completed-successfully';
    const jobCompletedWithWarnings = 'job-completed-with-warnings';
    const jobCompletedWithErrors = 'job-completed-with-errors';
    const jobRestartable = 'job-restartable';
    const queuedInDevice = 'queued-in-device';
}