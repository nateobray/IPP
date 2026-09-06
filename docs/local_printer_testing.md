# Local Printer Testing

Use the real-printer suite when you want to exercise the library against a CUPS queue on the current machine instead of the request stubs.

## Commands

```bash
composer test:unit
composer test:integration
composer record:fixtures
composer test:fixtures
```

## Discovery

`test:integration` discovers a target printer in this order:

1. `IPP_TEST_URI` if provided.
2. `IPP_TEST_QUEUE` on the local CUPS instance as `ipp://localhost/printers/{queue}`.
3. The first local CUPS queue returned by `lpstat -e` that answers `Get-Printer-Attributes`.

The suite uses `IPP_TEST_USER` when provided, otherwise the current shell user, so multi-step job operations can work against a local CUPS queue without embedding a password in the repo. Set `IPP_TEST_PASSWORD` only if your CUPS server requires HTTP auth.

## Notes

- The integration suite creates held test jobs so documents do not print while the test is running.
- The multi-step job test finishes by cancelling the held job.
- The held-job test reads back state 4 (`pending-held`) before and after document submission, then state 7 (`canceled`) after cleanup. A cleanup failure fails the test.
- If a queue does not allow unauthenticated job management, the read-only tests still run and the mutating test is skipped with a message describing which credential override to set.
- `composer record:fixtures` records raw IPP request/response binaries plus JSON metadata for safe live operations under `test/fixtures/real/`.
- By default the recorder captures local CUPS queues plus direct `_ipp._tcp` printers discovered via `ippfind`; set `IPP_RECORD_INCLUDE_IPPS=1` to include `_ipps._tcp`, and `IPP_RECORD_INSECURE_TLS=1` if those devices use self-signed certificates.
- The recorded fixtures are replayed by the normal PHPUnit suite and now drive RFC-oriented compliance assertions for core IPP/1.1 request structure, required printer attributes, and required operations.
- The recorder also captures safe negative paths: unsupported document formats via `Validate-Job`, plus one local CUPS `Cancel-Job` authentication challenge when a queue supports creating a held probe job.
- When a local CUPS queue supports it, the recorder also captures a safe held-job lifecycle: `Print-Job`, `Get-Job-Attributes`, and authenticated `Cancel-Job`, with cleanup performed as part of the recording pass.
- When a discovered target advertises PWG5100.5 Document Object support, the recorder also captures `Get-Documents` and `Get-Document-Attributes` against a held probe job. On machines without a PWG5100.5-capable printer, those paths skip explicitly and do not fail the suite.
- The replay suite now also enforces a per-operation required-attribute matrix over the recorded request binaries, including the RFC 2911 rule that target attributes immediately follow `attributes-charset` and `attributes-natural-language`.

## USB Zebra GK420t verification

To target this queue explicitly, without discovering other printers:

```bash
IPP_TEST_QUEUE=Zebra_Technologies_ZTC_GK420t composer test:integration
```

This path uses the library to speak IPP to macOS CUPS, which sends output to the Zebra over USB. The tested queue uses the Zebra ZPL driver, 203 dpi, and `na_index-4x6_4x6in` media.

The live checks exercised capability discovery, Validate-Job, Get-Jobs, held Create-Job/Send-Document/cancellation, and subscription creation, inspection, renewal, cancellation, and event polling. A separate Print-Job probe (job 377) was confirmed held and then canceled. Document Object operations were skipped because the queue does not advertise them.

For the physical check, job 378 contained one 4×6 ZPL label submitted as `application/vnd.cups-raw`. The library created the job held, sent the document, verified state 4, and released it once. CUPS reported state 9 (`completed`), and the operator confirmed exactly one clear label with readable text and barcode. Barcode scanning was not tested.

This verifies the IPP → CUPS → USB path. It does not establish direct printer IPP/TLS support, HTTP password authentication, or PDF-to-ZPL conversion. The normal integration suite continues to use held jobs and does not reproduce the physical print automatically.
