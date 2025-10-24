# CHANGELOG

### v3.0.0 (2025-10-24)

- [#397](https://github.com/Sylius/InvoicingPlugin/pull/397) Isolate plugin messaging: Introduce dedicated event & command buses for InvoicingPlugin ([@tomkalon](https://github.com/tomkalon))
- [#398](https://github.com/Sylius/InvoicingPlugin/pull/398) Persisted PDF path & file flow
    - `Invoice`: added `path` field (UNIQUE).
    - `InvoiceFactory`: inject `InvoiceFileNameGeneratorInterface`; use `generateForPdf($number)` to set `path` on creation.
    - `InvoiceFileProvider`: removed dependency on file-name generator; added `%sylius_invoicing.pdf_generator.enabled%`; now relies on `Invoice::path()`.
    - `InvoiceCreator`: removed `InvoicePdfFileGeneratorInterface` and `InvoiceFileManagerInterface`; PDF is no longer generated on invoice creation—it's generated lazily on first download/provide.
    - `InvoiceFileNameGeneratorInterface::generateForPdf()` now accepts `string $invoiceNumber` instead of `InvoiceInterface`.
    - `InvoiceFileNameGeneratorInterface::generateForPdf()` can prefix filenames based on `SYLIUS_INVOICING_SEQUENCE_SCOPE` (`global` – default, `monthly`, `annually`).
    - `InvoicePdfFileGenerator`: removed `InvoiceFileNameGeneratorInterface` from constructor; filename is taken from `Invoice::path()`; update DI to drop the generator argument.
