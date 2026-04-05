# IPP/1.1 Compatibility Checklist

This checklist tracks what remains to reach full IPP/1.1 compatibility per RFC2910 (encoding/transport) and RFC2911 (job & printer operations). The RFCs define the required operations, attributes, and behaviors; use this as the working map for implementation and tests.

## 1) Required Operations (RFC2911)

- [x] `Print-Job` (`Printer::printJob`)
- [x] `Validate-Job` (`Printer::validateJob`)
- [x] `Create-Job` (`Printer::createJob`)
- [x] `Send-Document` (`Job::sendDocument`)
- [x] `Send-URI` (`Job::sendURI`) – optional in RFC2911, but implemented
- [x] `Cancel-Job` (`Job::cancelJob`)
- [x] `Get-Job-Attributes` (`Job::getJobAttributes`)
- [x] `Get-Jobs` (`Printer::getJobs`)
- [x] `Get-Printer-Attributes` (`Printer::getPrinterAttributes`)

**Remaining work**
- [x] Add integration tests that validate request/response expectations for each required operation.
- [x] Validate required operation attributes and reject invalid requests per RFC2911.

## 2) RFC2910 Encoding/Transport

**Implemented**
- [x] IPP binary encoding/decoding (types, groups, values)
- [x] HTTP transport for `ipp://` and `ipps://`
- [x] Multi-value (1setOf) attribute encoding

**Remaining work**
- [x] Enforce required operation attribute order (attributes-charset, attributes-natural-language, then target attributes).
- [x] Validate required charset/natural-language handling on all operations.
- [x] Add tests for response decoding edge-cases (truncated payloads, unknown tags).

## 3) Operation Attributes (RFC2911)

**Supported in OperationAttributes**
- `attributes-charset`
- `attributes-natural-language`
- `printer-uri`
- `job-uri`
- `job-id`
- `requesting-user-name`
- `document-uri`
- `document-name`
- `document-format`
- `document-natural-language`
- `compression`
- `job-name`
- `ipp-attribute-fidelity`
- `which-jobs`
- `limit`
- `my-jobs`
- `last-document`

**Remaining work**
- [x] Allow callers to identify jobs using `job-uri` (not just `printer-uri` + `job-id`).
- [x] Validate required operation attributes per operation.

## 4) Job Template Attributes (RFC2911 §4.2)

**Implemented in `JobAttributes`**
- `job-priority`
- `job-hold-until`
- `job-sheets`
- `multiple-document-handling`
- `copies`
- `finishings`
- `page-ranges`
- `sides`
- `number-up`
- `orientation-requested`
- `media`
- `printer-resolution`
- `print-quality`
- `print-scaling`
- `media-col`

**Remaining work**
- [x] Verify required/optional list against RFC2911 §4.2 tables and add any missing template attributes.
- [x] Add tests for each required template attribute type and encoding.

## 5) Job Description Attributes (RFC2911 §4.3)

**Implemented in `JobAttributes`**
- `job-uri`
- `job-id`
- `job-state`
- `job-state-reasons`
- `job-state-message`
- `job-printer-uri`
- `job-name`
- `job-originating-user-name`
- `time-at-creation`
- `time-at-processing`
- `time-at-completed`
- `job-k-octets`
- `job-impressions`
- `job-media-sheets`
- `job-k-octets-processed`
- `job-impressions-completed`
- `job-media-sheets-completed`
- `number-of-intervening-jobs`

**Remaining work**
- [x] Confirm required attributes and implement missing ones in `JobAttributes`.
- [x] Add decode/encode tests for all required job description attributes.

## 6) Printer Description Attributes (RFC2911 §4.4)

**Implemented in `PrinterAttributes`**
- `device-uri`
- `port-monitor`
- `ppd-name`
- `printer-is-accepting-jobs`
- `printer-info`
- `printer-location`
- `printer-more-info`
- `printer-uri-supported`
- `uri-authentication-supported`
- `uri-security-supported`
- `printer-name`
- `printer-state`
- `printer-state-reasons`
- `printer-state-message`
- `operations-supported`
- `charset-supported`
- `natural-language-supported`
- `generated-natural-language-supported`
- `printer-up-time`
- `printer-current-time`

**Remaining work**
- [x] Confirm required attributes and implement missing ones in `PrinterAttributes`.
- [x] Add decode/encode tests for all required printer description attributes.

---

## Current Status

RFC2910 and RFC2911 core compatibility work is now implemented and covered by unit, fixture replay, and local integration tests.

## Follow-On Hardening

1. Expand fixture coverage for malformed and out-of-band attribute values as the corpus evolves.
2. Record an explicit real-printer `job-uri` lifecycle fixture in addition to the existing `printer-uri` + `job-id` path.
3. Keep the compliance matrix current as additional IPP/2.x and PWG extension work lands.
