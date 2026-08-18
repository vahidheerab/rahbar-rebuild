## Purpose

این قابلیت تضمین می‌کند بازسازی و انتقال Rahbar در چند نشست کاری قابل پیگیری، قابل تکرار و مبتنی بر شواهد باشد و هیچ مرحله حساس بدون معیار پذیرش و rollback عبور نکند.

## ADDED Requirements

### Requirement: Resumable execution record
The rebuild process SHALL maintain a single granular checklist and a session log containing the current phase, last safe checkpoint, blockers, evidence, and next action.

#### Scenario: Work resumes after an interruption
- **WHEN** a new work session starts after any interruption
- **THEN** the operator can identify the last completed test, outstanding blocker, and exact next task without relying on memory

### Requirement: Isolated environment verification
The verification process SHALL test Legacy and Rebuild independently and SHALL record their Compose project, network, volume, database, URL, and runtime health.

#### Scenario: One environment is restarted
- **WHEN** either Legacy or Rebuild is restarted or changed
- **THEN** its own services remain healthy and the other environment's containers, data, ports, and network remain unaffected

### Requirement: Evidence-backed test outcomes
Every mandatory test SHALL have a stable identifier, an expected result, a recorded outcome, and a reference to reproducible evidence before it is marked complete.

#### Scenario: A test is marked complete
- **WHEN** an operator changes a mandatory test to completed
- **THEN** the recorded evidence demonstrates the expected result and identifies the tested environment and date

### Requirement: Migration reconciliation
Every data migration rehearsal SHALL compare source, destination, and exception counts for all critical entities and SHALL preserve a verified pre-migration recovery point.

#### Scenario: Migrated data differs from source
- **WHEN** source and destination counts or sampled records do not reconcile
- **THEN** the relevant migration gate remains blocked and the discrepancy is recorded for resolution

### Requirement: Critical business-path parity
The Rebuild SHALL not pass its release gate until critical public, account, commerce, payment, entitlement, SEO, security, accessibility, and operational paths match the approved Legacy behavior or an explicitly approved replacement behavior.

#### Scenario: A critical path fails
- **WHEN** any critical-path test fails or lacks an approved disposition
- **THEN** cutover remains blocked

### Requirement: Tested rollback
The cutover workflow SHALL include a time-bounded rollback procedure that has been rehearsed against a non-production recovery point.

#### Scenario: Cutover acceptance fails
- **WHEN** a rollback trigger occurs during the release window
- **THEN** the operator can restore the approved Legacy service and data state using the documented procedure
