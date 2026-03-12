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
