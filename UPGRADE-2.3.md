# UPGRADE FROM 2.2 TO 2.3

1. Invoice number sequences can now be scoped. The scope is configurable and defaults to `global`,
   which keeps the exact numbering format and counter continuity of previous versions:

    ```yaml
    sylius_invoicing:
        sequence:
            scope: global # one of "global", "monthly", "annually"
    ```

   Custom scopes can be added by registering a service implementing
   `Sylius\InvoicingPlugin\Resolver\SequenceScopeResolverInterface`, tagged with
   `sylius_invoicing.sequence_scope_resolver`, and setting its name as the `scope` option.
   Keep in mind that the number prefix returned by `prefix()` must make invoice numbers unique
   across all periods of the scope, as invoice files are stored under names derived from invoice numbers.

1. Run doctrine migrations when upgrading — the `sylius_invoicing_plugin_sequence` table gains
   `type`, `year` and `month` columns, and unique indexes are created on the sequence scope and on
   the invoice number. In the unlikely case your database contains duplicated invoice numbers,
   the migration will fail and the duplicates have to be resolved manually first. You can check
   for duplicates upfront with:

    ```sql
    SELECT number, COUNT(*) FROM sylius_invoicing_plugin_invoice GROUP BY number HAVING COUNT(*) > 1;
    ```

1. The following interfaces and classes have changed as part of the sequence scoping feature:

   - `Sylius\InvoicingPlugin\Entity\InvoiceSequenceInterface` and `Sylius\InvoicingPlugin\Entity\InvoiceSequence`
     gained the `getType(): string`, `setType(string $type): void`, `getYear(): int`, `setYear(int $year): void`,
     `getMonth(): int` and `setMonth(int $month): void` methods, together with the `SCOPE_GLOBAL`, `SCOPE_MONTHLY`
     and `SCOPE_ANNUALLY` constants. Custom implementations must be updated accordingly.

   - `Sylius\InvoicingPlugin\Generator\SequentialInvoiceNumberGenerator` — not passing a value
     for the `$scopeResolvers` argument is deprecated and the argument will be required in 3.0;
     until then, omitting it falls back to the built-in `global`, `monthly` and `annually` resolvers:

     ```diff
     public function __construct(
         private readonly RepositoryInterface $sequenceRepository,
         private readonly FactoryInterface $sequenceFactory,
         private readonly EntityManagerInterface $sequenceManager,
         private readonly ClockInterface $clock,
         private readonly int $startNumber = 1,
         private readonly int $numberLength = 9,
     +   ?iterable $scopeResolvers = null,
     +   private readonly string $scope = InvoiceSequenceInterface::SCOPE_GLOBAL,
     )
     ```

   - `Sylius\InvoicingPlugin\Creator\InvoiceCreator` now persists the invoice before writing its PDF file,
     so that a duplicated invoice number fails on the database unique constraint without overwriting
     a file belonging to another invoice. Database errors are no longer swallowed and propagate to the caller.
