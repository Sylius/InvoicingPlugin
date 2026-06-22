# UPGRADE FROM 2.1 TO 2.2

1. Support for the `SyliusPdfGenerationBundle` has been added as an alternative to the legacy PDF generation
   which was using `KnpSnappyBundle` with a hardcoded `wkhtmltopdf` binary.
   To use it, set the `legacy` option to `false` in your configuration:

    ```yaml
    sylius_invoicing:
        pdf_generator:
            legacy: false
    ```

   The bundle is preconfigured with `knp_snappy` adapter and `gaufrette` storage by default, with a `sylius_invoicing` context making it a drop-in replacement.

   The `sylius_invoicing.pdf_generator.legacy` option itself is deprecated and will be removed in 3.0,
   together with the entire legacy PDF generation path. The `SyliusPdfGenerationBundle` integration will become
   the only supported mode.

1. The following services now accept new argument types from the `SyliusPdfGenerationBundle`. Passing the old types is deprecated and will be removed in 3.0:

   - `Sylius\InvoicingPlugin\Generator\InvoicePdfFileGenerator`:

     ```diff
     public function __construct(
     -   private readonly TwigToPdfGeneratorInterface $twigToPdfGenerator,
     +   private readonly TwigToPdfGeneratorInterface|TwigToPdfRendererInterface $twigToPdfRenderer,
         private readonly FileLocatorInterface $fileLocator,
         private readonly InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
         private readonly string $template,
         private readonly string $invoiceLogoPath,
     )
     ```

   - `Sylius\InvoicingPlugin\Creator\InvoiceCreator`:

     ```diff
     public function __construct(
         private readonly InvoiceRepositoryInterface $invoiceRepository,
         private readonly OrderRepositoryInterface $orderRepository,
         private readonly InvoiceGeneratorInterface $invoiceGenerator,
         private readonly InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
     -   private readonly InvoiceFileManagerInterface $invoiceFileManager,
     +   private readonly InvoiceFileManagerInterface|PdfFileManagerInterface $invoiceFileManager,
         private readonly bool $hasEnabledPdfFileGenerator = true,
     )
     ```

   - `Sylius\InvoicingPlugin\Provider\InvoiceFileProvider` — the `$invoiceFileManager` and `$invoicesDirectory` arguments are also deprecated and will be removed in 3.0:

     ```diff
     public function __construct(
         private readonly InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
     -   private readonly FilesystemInterface $filesystem,
     +   private readonly FilesystemInterface|PdfFileManagerInterface $filesystem,
         private readonly InvoicePdfFileGeneratorInterface $invoicePdfFileGenerator,
     -   private readonly InvoiceFileManagerInterface $invoiceFileManager,
     -   private readonly string $invoicesDirectory,
     +   private readonly ?InvoiceFileManagerInterface $invoiceFileManager = null,
     +   private readonly ?string $invoicesDirectory = null,
     )
     ```

1. The following classes, interfaces, and services have been deprecated and will be removed in 3.0:

   | Deprecated                                                      | Replacement                                                                  |
   |-----------------------------------------------------------------|------------------------------------------------------------------------------|
   | `Sylius\InvoicingPlugin\Generator\TwigToPdfGeneratorInterface`  | `Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface`        |
   | `Sylius\InvoicingPlugin\Generator\TwigToPdfGenerator`           | `Sylius\PdfGenerationBundle\Core\Renderer\TwigToPdfRendererInterface`        |
   | `Sylius\InvoicingPlugin\Generator\PdfOptionsGeneratorInterface` | `sylius/pdf-generation-bundle` option processors                             |
   | `Sylius\InvoicingPlugin\Generator\PdfOptionsGenerator`          | `sylius/pdf-generation-bundle` option processors                             |
   | `Sylius\InvoicingPlugin\Manager\InvoiceFileManagerInterface`    | `Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManagerInterface` |
   | `Sylius\InvoicingPlugin\Manager\InvoiceFileManager`             | `Sylius\PdfGenerationBundle\Core\Filesystem\Manager\PdfFileManager`          |

   The corresponding services (`sylius_invoicing.generator.twig_to_pdf` and `sylius_invoicing.generator.pdf_options`) are also deprecated.

1. `Sylius\InvoicingPlugin\Provider\UnitNetPriceProvider` now accepts a `CalculatorInterface` argument used to compute the
   tax included in the unit price. Not passing it is deprecated and it will be required in 3.0:

    ```diff
    public function __construct(
    +   private ?CalculatorInterface $taxCalculator = null,
    )
    ```

   When no calculator is passed, the provider falls back to recalculating the included tax from the full unit price.
   This replaces the previous behaviour of subtracting the neutral tax adjustment amount directly, which was computed on
   the discounted (post-promotion) price and therefore produced a net price that diverged from the actual one.
