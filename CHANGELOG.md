# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added

-

### Changed

-

### Deprecated

-

### Removed

-

### Fixed

-

### Security

-

## [1.3.0] - 2026-03-29

### Added

- Full support for Laravel 13 with comprehensive CI testing matrix.
- Added Laravel version compatibility matrix CI workflow for testing Laravel 11, 12, and 13.

### Changed

- Updated minimum Laravel version from 10.0 to 11.0.
- Updated CI matrix to test Laravel 11, 12, and 13.
- Updated Testbench constraint to include v11.0 for Laravel 13.
- Updated project documentation to reflect Laravel 13 support.

### Removed

- Dropped support for Laravel 10. Package now requires Laravel 11 or higher.

## [1.2.0] - 2026-03-29

### Added

- Added IDE helper generation command `helpers:ide` for dynamic helper autocompletion.
- Added automatic IDE helper regeneration after `make:helper`.

## [1.1.0] - 2025-11-26

### Added

- Added dynamic global helper function auto-creation from files in `app/Helpers`.
- Added `HelperProxy` for dynamic helper method forwarding.
- Added comprehensive project documentation in `README.md`.
- Added composer scripts for testing and formatting.
- Added package metadata improvements including keywords and MIT license.

### Changed

- Improved `make:helper` command output messages for clearer generated helper usage.
- Updated package metadata by removing a pinned package version.

### Fixed

- Corrected README badge formatting and visibility.

## [1.0.0] - 2025-11-23

### Added

- Initial package release.

[Unreleased]: https://github.com/l0n3ly/laravel-dynamic-helpers/compare/v1.3.0...HEAD
[1.3.0]: https://github.com/l0n3ly/laravel-dynamic-helpers/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/l0n3ly/laravel-dynamic-helpers/compare/v1.1.0...v1.2.0
[1.1.0]: https://github.com/l0n3ly/laravel-dynamic-helpers/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/l0n3ly/laravel-dynamic-helpers/releases/tag/v1.0.0
