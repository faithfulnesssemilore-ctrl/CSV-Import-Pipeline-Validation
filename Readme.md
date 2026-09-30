# CSV Import Pipeline Validation 

---

### Project Overview
This project contain my solution to the CSV import pipeline validation problem. 
The goal was to design a **multi-stage streaming pipeline** that use a **validation strategy** to validate the CSV data before importing it into the database.

### Features
- **Extensible Design**: The pipeline is designed to be easily extended with new validation rules.
- **Modular Stages**: Each stage of the pipeline performs a specific function, making the system more maintainable.
- **Flexible Validation**: The validation strategy can be easily adapted to different types of CSV data and validation requirements.
- **Error Handling**: The pipeline includes robust error handling to ensure that invalid data is properly reported and handled.
- **Logging**: The pipeline includes logging functionality to track the progress of the import process and any errors that occur.
-   **Composition**: The pipeline can be composed of multiple stages, allowing for complex validation and processing workflows.


## Tech Stack

| Technology | Purpose |
|---|---|
| PHP 8+ | Core language |
| `declare(strict_types=1)` | Type safety |
| Composer | Dependency management, autoloading |
| Pest | Testing framework |
----
# Running the project
To run the project, follow these steps:
## 1. Clone the repository to your local machine.
```bash 
git clone https://github.com/faithfulnesssemilore-ctrl/CSV-Import-Pipeline-Validation.git
cd CSV-Import-Pipeline-Validation 
```
## 2. Install the dependencies using Composer.
```bash
composer install
```
## 3. Run the test suite using Pest.
```bash
vendor/bin/pest
```

---
## Architecture Overview
   Configuration
|
   ConfigurationValidator → validates the import setup before runtime process begins 
|
CSV Reader(streaming) → Reads the file as a generator 1 row at a time 
|   
HeaderMapper → runs only on the first row of the csv file  
|
Sanitizer-> in importpipeline::buildRowContext() each fields value is sanitized using the configured sanitizer for the canonical field 
|
Parsing/canonical - IN buildRowContext(), the sanitize value is parsed i into a single standard accepted  internal name or format 
|
Field/ Row Context– After sanitized + parse, the row is assembled into context  object: FieldContext- holds raw test,Sanitized string,typed value and parse sucess and errors
RowContext– holds all fields for the row and can answer when the row is valid 
|
Validation  → validates each fields in a row
|
Invalid→Reject writer  ->if the row has any field errors the pipeline will reject it 
Valid
|
DuplicateChecker→ the pipeline checkers for duplicate using one canonical fields
|
Duplicate →Reject writer → if the duplicate check fails : the row is rejected, and Rejected writer aduit log it 

Unique → if row is valid with no duplication files can proceed to the outpu
|
ImportPipeling report.


- The architecture of the CSV import pipeline validation system is designed to be modular and extensible. The system is composed of multiple stages, each responsible for a specific function in the import process. The stages are designed to be easily extended with new validation rules, allowing the system to adapt to different types of CSV data and validation requirements.

---
# Project Structure
The project is organized into the following directories and files:
- `src/`: Contains the source code for the CSV import pipeline validation system.
- `tests/`: Contains the test cases for the system, written using Pest.
---
# Testing
The project includes a comprehensive  automated testing suite that covers all aspects of the CSV import pipeline validation system. The tests are written using Pest.The tests covers the following areas:
- CSV file reading and parsing
- Header mapping and validation
- Field sanitization and parsing
- Row validation and error handling
- Duplicate checking and rejection
- Overall pipeline functionality and performance

---
# Design Decisions
- ** For full details of the design decisions, please refer to the `docs/design_decisions.md` file in the project repository. This document provides a detailed explanation of the architectural choices made during the development of the CSV import pipeline validation system, including the rationale behind each decision and any trade-offs considered.

---
## Running an Import

See `tests/Feature/CsvImportPipelineTest.php` for a complete, working
example of constructing and running the pipeline end-to-end, including
configuration for sanitizers, parsers, validation rules, and writers.

---
#  Feedback

Thank you for taking the time to review this submission.

If you have any questions, suggestions, or feedback regarding the architecture or implementation, feel free to open an issue or reach out during the code review process.

