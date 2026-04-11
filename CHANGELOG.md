# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.1] — 2026-04-11

### Added
- **`Printer::setPrinterAttributes(array $attributes)`** — RFC 8011 §4.2.19 Set-Printer-Attributes operation.
- **`Job::setJobAttributes(array $attributes)`** — RFC 8011 §4.2.20 Set-Job-Attributes operation.
- **PHP 8 return type declarations** on all public methods in `Printer` and `Job`.
- **PHP 8 property type declarations** on all properties in `Printer` and `Job`.

## [1.1.0] — 2026-04-11

### Added
- **`Enum` tolerates unknown values** — constructing or decoding an `Enum`-based type (e.g. `StatusCode`, `Operation`) with an unrecognised numeric code no longer throws. The raw value is stored and rendered as a zero-padded hex string (e.g. `"0x003C"`). `StatusCode::getClass()` still works correctly based on numeric range.
- **New `StatusCode` constants** (RFC 3380 / RFC 8011): `successful_ok_ignored_subscriptions` (0x0003), `successful_ok_ignored_notifications` (0x0004), `successful_ok_too_many_events` (0x0005), `successful_ok_but_cancel_subscription` (0x0006), `successful_ok_events_complete` (0x0007), `client_error_ignored_all_subscriptions` (0x0413), `client_error_too_many_subscriptions` (0x0414), `client_error_ignored_all_notifications` (0x0415), `client_error_print_support_file_not_found` (0x0416), `server_error_printer_is_deactivated` (0x050A), `server_error_too_many_jobs` (0x050B), `server_error_too_many_documents` (0x050C).
- **RFC 3382 collection attribute syntax** marked as `DONE` in the compliance matrix.
- **CUPS server operations** on `Printer`: `getDefault()`, `getPrinters()`, `getClasses()`
- **CUPS job operations** on `Job`: `moveJob()`, `authenticateJob()`
- **`IppStatusException`** — thrown automatically when the printer returns a non-successful IPP status code (any code ≥ `0x0100`). Carries the full decoded response payload, printer URI, and status code. See [Exceptions](README.md#exceptions).
- **`NetworkError` exception** — thrown on cURL-level transport failures (connection refused, DNS failure, TLS error, timeout).
- **Generic attribute fallback** in `JobAttributes`, `PrinterAttributes`, and `OperationAttributes` — unknown attribute names are now accepted with type inference (bool → `boolean`, int → `integer`, string → `keyword`, typed `{value, type}` arrays) instead of throwing immediately.
- **Typed member construction** in `CollectionAttribute` and `Collection` — members can now be passed as `['value' => ..., 'type' => 'keyword']` to force a specific IPP type.
- **`Collection` constructor** — accepts an optional associative array with the same type-inference logic.
- **`Collection::getValueTag()`** and **`Collection::getLength()`** helper methods.
- CUPS PDF printer real-fixture recordings for integration testing.
- IPPS/TLS transport support (`ipps://` URIs mapped to HTTPS).

### Fixed
- `Collection::encode()` — fatal `len()` call replaced with `strlen()`.
- `Collection::encode()` — undefined `$this->endValueTag` replaced with `$this->endTag`.
- `Collection::decode()` — `$this->name->getValue()` (non-existent property) replaced with `$name->getValue()`.
- `Collection::decode()` — `$this->attributes` now consistently uses array notation; object-notation access removed.
- `Collection::__get()` — fixed to use array notation matching the initialized type.
- `MemberAttribute` — added `bool` and `array` type auto-detection in the constructor.
- `MemberAttribute` — `COLLECTION`-type member value length is now correctly encoded as `0` per RFC 8010 §3.1.7.
- `Operations::CUPS_MOVE_JBO` renamed to `CUPS_MOVE_JOB` (typo fix).
- `Operations::SET_PRINTER_ATTIRBUTES` renamed to `SET_PRINTER_ATTRIBUTES` (typo fix).
- `Types::getType()` parameter `$natuarlLanguage` renamed to `$naturalLanguage` (typo fix).

### Breaking Changes
- **`IppStatusException` is now thrown** by `Printer` and `Job` methods when the printer returns a client-error or server-error IPP status. Previously, all responses were returned as `IPPPayload` objects regardless of status, and callers had to check `$response->statusCode` manually. Callers that relied on the old behavior must wrap calls in a `try/catch` block. See [Exceptions](README.md#exceptions).
- `Operations::CUPS_MOVE_JBO` and `Operations::SET_PRINTER_ATTIRBUTES` have been removed — use the corrected names `CUPS_MOVE_JOB` and `SET_PRINTER_ATTRIBUTES`.

## [1.0.2] — 2024-05-01

See [GitHub releases](https://github.com/nateobray/IPP/releases) for prior release notes.
