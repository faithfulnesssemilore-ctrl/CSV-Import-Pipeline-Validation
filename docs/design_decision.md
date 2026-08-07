# Design Decision 

# Overview
The doecument explains the key archetectural decision made while implemtenting the CSV import pipeline validation system. The gal was to build a solution that is modular, extensible,testable and capable of handling large CSV files efficiently.

---
# Problem Statement 
 - The operations team uploads messy CSV files of customer records. Files come from different vendors with different  column orders, 
 inconsistent date formats, 
  and occasional garbage rows.
 We need a pipeline that ingests a file, validates and normalizes each row, and reports exactly what passed and what failed — without aborting the whole import on the first bad row.
--- 
# Design Goal
THe Solution was designed to meet the following goals:
- **Extensibility**: The system should be easy to extend with new validation rules, sanitization logic, and vendor-specific mappings with out changing the core pipeline.
- **Low Memory Usage**: The system should minimize memory consumption, especially when processing large CSV files.
- **Testability**: Each component of the pipeline should be independently testable, allowing for unit tests to verify correctness.
- **Clear Error Reporting**: The system should provide clear and actionable error messages for each row that fails validation, including the specific reason for failure.
-**Modularity**: The system should be composed of independent stages, each with a single responsibility, to improve maintainability and readability.

---
## Acheitectural Overview 
 Configuration
       |
       V
   ConfigurationValidator → validates the import setup before runtime process begins 
     |
       V
CSV Reader(streaming) → Reads the file as a generator 1 row at a time 
|
  V
HeaderMapper → runs only on the first role of the csv file  
|
V
Sanitizer-> in importpipeline::RowContex() each fields value is sanitized using the configured sanitizer for the canonical field 
|
       V
Parsing/canonical - IN buildRowContext(), the sanitize value is parsed i into a single standard accepted  internal name or format 
|
       V
Field/ Row Context– After sanitized + parse, the row is assembled into context  object: FieldContext- holds raw test,Sanitized string,typed value and parse sucess and errors
RowContext– holds all fields for the row and can answer when the row is valid 
|
       v
Validation  → validates each fields in a row
|
       V
Invalid→Reject writer  ->if the row has any field errors the pipeline will reject it 
Valid
|
       v
DuplicateChecker→ the pipeline checkers for duplicate using one canonical fields
|
       V
Duplicate →Reject writer → if the duplicate check fails : the row is rejected, and Rejected writer aduit log it 

Unique → if row is valid with no duplication files can proceed to the outpu
|
       V
ImportPipeling report.


- The architecture of the CSV import pipeline validation system is designed to be modular and extensible. The system is composed of multiple stages, each responsible for a specific function in the import process. The stages are designed to be easily extended with new validation rules, allowing the system to adapt to different types of CSV data and validation requirements.

## 1. Pipeline-Based Architecture
  **Decision**
- The import process was implemteed as a sequence of independent pipeline stages rather than one processing class,
**Why**
- Each stage has a single responsibility:
    - **Configuration Validation**
    - **CSV Reading**
    - **Header Mapping**
    - **Sanitization**
    - **Parsing**
    - **Field/Row Context Creation**
    - **Validation**
    - **Duplicate Checking**
    - **Writing accepted or rejected rows**
- Separating these responsibilities makes the pipeline easier to understand, test, and extend.

## 2. Streaming CSV Processing
  **Decision**
  -Csv files are pocesssed using a genetor-based streaming approach one row at a time instead of loading the entire file into memory.
  -This alows the system to handle large CSV files without running into memory issues.
  **Why**
  - Streaming processing is more memory efficient and allows for better performance when dealing with large datasets.

## 3. Header Mapping before Processing 
   **Decision**
    Vendor-specific column names are mapped to canonical internal field names before any sanitization or validation occurs.
    **Why**

Different vendors may use different headers for the same field.

For example:

Email
Customer Email
Email Address

All are mapped to a single canonical field name.

The rest of the pipeline only works with canonical names, making every downstream component independent of vendor-specific formats.

## 4.Seperate Sanitization and Parsing Stages 
**Decision**
Sanitization and parsing are implemented as separate stages in the pipeline.
**Why**
- Sanitization is responsible for cleaning and normalizing the data (e.g., trimming whitespace, Normalizing email casing).
Parsing is responsible for converting cleaned string into domain types (e.g., converting a string to a date or number, String → Integer,String → DateTimeImmutable).
- Keeping these stages separate allows for better testability and maintainability.
- Keeping these responsibilities separate improves readability, testing, and extensibility.

## 5. Validation After Parsing
**Decision**

Validation occurs only after values have been sanitized and parsed.

**Why**

Validation should operate on canonical, correctly typed data rather than raw CSV strings.

For example, validating an integer is more reliable than validating a numeric string.

This also prevents validation rules from needing to perform their own parsing.
---
# 6. Field /Row Context Objects 
**Decision**
- FieldContext and RowContext objects are used to encapsulate the state of each field and row during processing.
- FieldContext holds the raw text,sanitized string, typed value, and any parsing or validation errors for a single field.
- RowContext holds all FieldContext objects for a single row and can determine if the row is valid based on the state of its fields.
**Why**
- Using context objects allows for better encapsulation of state and makes it easier to pass information between pipeline stages.
- It also simplifies error reporting, as each context object can provide detailed information about what went wrong during processing.

# 7. Duplicate Detection After Validation
**Decision**
Duplicate checking occurs only after a row has passed validation.

**Why**

There is no value in checking duplicates for data that is already invalid.

Performing validation first avoids unnecessary work and ensures duplicate detection operates on valid, canonical values.

---
## 8. Accepted and Rejected Row Writers
**Decision**
- The pipeline has separate writers for accepted and rejected rows.
- Accepted rows are written to the output destination, while rejected rows are logged with their errors for auditing purposes.
**Why** 
- Separetes invalid rows from good output cleanlyand allow for good auditing and reporting of errors.
- Keeps a record of why a row failed validation, which can be useful for debugging and improving data quality.
- Prevents invaoid data from entering the final output.

---

# 9. Clear Error Reporting
**Decision**
- The system provides detailed error messages for each row that fails validation, including the specific reason for failure and the field(s) involved.
- Rejected rows are logged with their errors for auditing purposes
**Why** 
- Keeos diplicte filtering in the pipeline and allows for better debugging and data quality improvement.

# Design Pattern Used 
- **Strategy Pattern** -- the core mechanism, used three separete times: SanitizesField,ParsesField,ValidatesField.Each defines one contract; small,interchangeable concrete classes implement it; the algorithm used is chosen externally via configuration/constructor injection.

- **Named Constructor pattern** - ParseResult::success($value)/ParseResult::failure($error), using a private contructor with a static factory methods,so invalid combinations of state(e.g, success: true  with a non-null error)are structurally impossible to construct.

- **Value Object pattern** - ParseResult and FieldContext are both value objects: representing "a fact" rather than "a thing with a lifecycle."

- **Pipeline pattern** - 
---

# Principles 
- **SOLID**
   - **SRP**-- every class has exactly one reason to change(Sanitizer only changes for cleaning logic, Parser only changes for parsing logic, Validator only changes for validation logic)
    - **OCP**-- new rule/fields/sanitisers/parsers are added via new files + configuration, not by changing existing code.
    - **LSP**--every ValidatesField/SanitisesField/ParsesField implementation honors its interface's exact contract(eg. ?string return:null=pass,string=fail),making any  implementation safely substitutable without breaking the caller.
    - **ISP**-- every interface is single-method and narrowly scoped(sanitize(),parse(),validate()), so that implementers are not forced to implement methods they don't need.
    - **DIP**-- partially applied: the sanitizers/parsers/validators are injected as interfaces:CsvReader is injected as a concrete class deliberately, since only one implementation is needed (YAGNI), but the rest of the pipeline is injected as interfaces, so that the caller can choose which implementation to use.
-**DRY**(Don't Repeat Yourself)-- e.g, the RequiredRule is one reusable class applied to name and email via configuration, rather than having separate RequiredNameRule and RequiredEmailRule classes.
- **YAGNI** (You Aren't Gonna Need It) -- only implement features that are actually needed, avoiding unnecessary complexity that why we explicitly avoided: a registry/auto-discovery mechanism for rules, a PipelineContext object to hold all the pipeline state, a generic "field type" system, and a generic "rule" system. Instead, we implemented only what was needed for the current requirements, keeping the design simple and focused.
- **KISS**(Keep It Simple, Stupid) -- the design is straightforward and easy to understand, with clear separation of concerns and minimal complexity like using PHP's built-in filter_var(..., FILTER_VALIDATE_EMAIL) for email validation instead of implementing a custom regex-based validator.
- **Composition over inheritance** -- the pipeline is composed of small, independent classes that can be combined in different ways, rather than relying on a complex inheritance hierarchy,and zero use of extends anywher in the source files(src/);every relationship is "has-a"
(importPipeline has a CsvReader)rather than "is-a" (CsvReader is not a subclass of importPipeline).

- **Fail Fast** -- the system is designed to fail fast, rejecting invalid rows as soon as they are detected,so like  CsvReader(missing file),HeaderMapper (missing required header field), and ConfigurationValidator (invalid configuration) all throw exceptions immediately, rather than trying to continue processing and potentially causing more errors down the line.

- **Separation of Concerns** -- each stage of the pipeline has a single responsibility, making it easier to understand, test, and maintain. For example, the Sanitizer is only responsible for cleaning data, while the Parser is only responsible for converting data into the correct type.

- **Dependency Injection** -- (, the Dependency Inversion Principle (DIP) and Dependency Injection (DI) are not the same thing; DIP is a high-level design rule, while Dependency Injection is a specific coding technique used to follow that rule.)every configurable dependency (saniyizers, parsers, rules,field labels,dateformats, key fields, etc) is injected into the pipeline via constructor injection, allowing for easy configuration and testing, not a hard-coded dependency on a specific implementation. This makes it easy to swap out different implementations of sanitizers, parsers, and validators without changing the core pipeline code.

- **Directly turned down implementation for this Level**
- Registry / self-registering rules -- The assignment required configurable header-to-field mapping. Aliases solve that requirement directly by mapping multiple vendor-specific header names to a single canonical field name. A registry would be more appropriate for managing reusable objects such as sanitizers or parsers, not simple string mappings. Using a registry here would add unnecessary complexity without providing additional value.
-**Abstract classes** -- never used;no two implementations shared actual reusable code, so there was no need for an abstract base class. Instead, each implementation is a small, independent class that implements a single interface. This keeps the design simple and focused, and avoids the complexity of an inheritance hierarchy.
-**Generic "field type" system** -- never used; the assignment required a fixed set of fields with specific validation rules. A generic field type system would add unnecessary complexity without providing additional value. Instead, each field has its own specific validation rules, which are implemented as small, independent classes that can be easily tested and extended.
-**Pipeline Context**- rejected as an wrong way pattern that becomes a deumping ground over time;RowContext stays narrowly scoped instead 

---
# Edge Cases

- A row with a comma inside a quoted field (e.g., "Smith, John") is correctly
  preserved as one value by CsvReader and OutputWriter, both using PHP's
  native CSV parsing/writing functions rather than manual string splitting.
- A field that fails to parse (e.g., "not-a-number") never reaches
  validation rules that depend on a typed value — NonNegativeRule and
  SignupDateNotInFutureRule both check parseSuccess first and skip silently,
  avoiding a confusing second, contradictory error.
- A vendor's CSV missing an entire required column (not just one bad row,
  the whole column) is caught by HeaderMapper before any row is processed,
  since no per-row check could sensibly catch a structural, file-wide problem.
- A configuration referencing a field with no sanitizer or parser is caught
  by ConfigurationValidator before the file is even opened, rather than
  crashing on the first row that touches that field.
- A file that produces an unusually high proportion of rejected rows (over
  50%) triggers a logged warning, since this is more likely a configuration
  mistake than organically bad vendor data.

# Trade-offs

| Decision | Cost | Benefit |
|---|---|---|
| Streaming via generators | No random access; can't sort or know row count upfront | Flat memory usage regardless of file size |
| In-memory duplicate detection | No memory across separate runs; memory scales with unique key count | Simple, fast, zero external dependencies |
| Exceptions for structural failures only | Requires correctly classifying every new failure type | One bad row never aborts the whole import |
| Plain array configuration | No compile-time consistency checking | Fewer classes, faster to extend per field |
| ConfigurationValidator fails on first error | Multiple config mistakes require fix-and-rerun repeatedly | Simpler implementation; matches how a compiler-style check is normally used |
| No database persistence | No transactional rollback safety | No unnecessary complexity for an assignment scoped around file I/O |

# Production Improvements

If this were deployed as a real, ongoing production system rather than an
assignment submission, the following would be necessary before handling
real data, particularly for something as sensitive as financial or customer
records:

- Persist accepted/rejected rows to a transactional database rather than
  flat CSV files, so partial failures can be rolled back cleanly.
- Move duplicate detection to a persistent store (a database unique
  constraint or key-value store) so it works across separate import runs,
  not just within a single file.
- Encrypt sensitive fields (email, balance) at rest, and audit who ran each
  import and when, for compliance in regulated environments.
- Add idempotency so re-running the same file after a partial failure does
  not double-import already-successful rows.
- Expand logging into structured, leveled logs (info/warning/error) with
  metrics (rows/sec, duration) for real operational monitoring.
- Make the rejection-rate threshold configurable rather than a fixed 50%.

# Testing Strategy

The suite is split into two layers, matching the difference between
verifying one class in isolation versus verifying the whole system
cooperates correctly:

-- **Unit tests** (tests/Unit/) — one file per class, mirroring src/'s
  structure. Each test verifies one class's behavior with no real
  dependencies on other classes or the filesystem, except where a class's
  entire purpose is file I/O (CsvReader, OutputWriter, RejectWriter,
  FileLogger), which use real temporary files created and cleaned up
  within the test itself.
- **Feature tests** (tests/Feature/) — a small number of tests that run
  the entire pipeline end-to-end against realistic CSV data, including the
  assignment's own sample rows, verifying the final ImportReport rather
  than any single class's internal behavior.
- A dedicated streaming smoke test generates 100,000 rows and asserts
  memory growth stays under a fixed threshold after processing, directly
  demonstrating (not just claiming) that memory usage stays flat regardless
  of row count.
- Tests favor the AAA pattern (Arrange, Act, Assert) and assert exact,
  specific outcomes (e.g., exact file contents, exact error message text)
  rather than loose checks, so a regression is caught precisely rather than
  vaguely.

# Conclusion

The goal of this design was not simply to satisfy the assignment's stated
requirements, but to produce an architecture that remains simple to reason
about while genuinely supporting extension — new fields, new rules, new
vendors — without modifying existing, already-tested code. Every major
decision in this document was made by weighing a concrete trade-off against
the actual scope of this assignment, and several deliberately powerful-
sounding patterns (self-registering rules, a shared pipeline context object,
abstract base classes) were considered and rejected because they solved
problems this assignment does not actually have.