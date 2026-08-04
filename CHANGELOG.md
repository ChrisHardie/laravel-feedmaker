# Changelog

All notable changes to `laravel-feedmaker` will be documented in this file.

## 1.0.7 - 2026-08-04

- enhancement: Add comprehensive Pest test suite
- enhancement: Add DatabaseQueryHelper for cross-database compatibility
- fix: Resolve ViewException in RSS feed when home_url is missing
- fix: Improve exception reporting and recovery logic

## 1.0.6 - 2024-01-03

- fix: update model attribute casting for modern Laravel

## 1.0.5 - 2021-10-06

- fix: encode HTML entities in RSS item guid field

## 1.0.4 - 2021-10-05

- fix: encode HTML entities in RSS item links

## 1.0.3 - 2021-10-01

- fix: update last checked when update command runs, not within base class functions

## 1.0.2 - 2021-10-01

- fix: empty check in blade template for guid

## 1.0.1 - 2021-10-01

- enhancement: support guids different from url in RSS construction
- fix: don't encode URLs in RSS XML

## 1.0.0 - 2021-09-24

- initial release
