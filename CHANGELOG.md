# Changelog

All notable changes to this project will be documented in this file.

The format is based on *Keep a Changelog* and this project adheres to *Semantic Versioning*.

## [Unreleased]

## [0.1.0] - 2026-01-20

### Added
- Global Filament progress bar overlay UI.
- Progress polling with configurable idle/active intervals.
- Progress API via `ProgressManager`: `init()`, `update()`, `complete()`.
- Determinate progress when `totalRecords` is provided.
- Indeterminate progress when total is unknown.
- Optional auto-complete when `current >= total`.
- Cache-backed persistence with configurable cache store.
- Internal JSON endpoint for polling.
- Test suite for progress behaviour and anti-zombie updates.

