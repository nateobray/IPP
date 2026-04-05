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
- If a queue does not allow unauthenticated job management, the read-only tests still run and the mutating test is skipped with a message describing which credential override to set.
- `composer record:fixtures` records raw IPP request/response binaries plus JSON metadata for safe live operations under `test/fixtures/real/`.
- By default the recorder captures local CUPS queues plus direct `_ipp._tcp` printers discovered via `ippfind`; set `IPP_RECORD_INCLUDE_IPPS=1` to include `_ipps._tcp`, and `IPP_RECORD_INSECURE_TLS=1` if those devices use self-signed certificates.
- The recorded fixtures are replayed by the normal PHPUnit suite and now drive RFC-oriented compliance assertions for core IPP/1.1 request structure, required printer attributes, and required operations.
- The recorder also captures safe negative paths: unsupported document formats via `Validate-Job`, plus one local CUPS `Cancel-Job` authentication challenge when a queue supports creating a held probe job.
- When a local CUPS queue supports it, the recorder also captures a safe held-job lifecycle: `Print-Job`, `Get-Job-Attributes`, and authenticated `Cancel-Job`, with cleanup performed as part of the recording pass.
- The replay suite now also enforces a per-operation required-attribute matrix over the recorded request binaries, including the RFC 2911 rule that target attributes immediately follow `attributes-charset` and `attributes-natural-language`.
