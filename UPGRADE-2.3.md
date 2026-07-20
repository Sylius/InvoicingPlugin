# UPGRADE FROM 2.2 TO 2.3

1. Invoice number sequences can now be scoped. The scope is configurable and defaults to `global`,
   which keeps the exact numbering format and counter continuity of previous versions:

    ```yaml
    sylius_invoicing:
        sequence:
            scope: global # one of "global", "monthly", "annually"
    ```

   - `global` — a single, ever-increasing counter for the whole store, never reset.
   - `monthly` — a separate counter per year and month, reset on the 1st of every month.
   - `annually` — a separate counter per year, reset on the 1st of January.

   All three produce the same `Y/m/index` number format; only the counter's reset behavior differs.

   Custom scopes can be added by registering a service implementing
   `Sylius\InvoicingPlugin\Resolver\SequenceScopeResolverInterface`, tagged with
   `sylius_invoicing.sequence_scope_resolver`, and setting its name as the `scope` option.
   Keep in mind that the number prefix returned by `prefix()` must make invoice numbers unique
   across all periods of the scope, as invoice files are stored under names derived from invoice numbers.

   Changing the `scope` on a store that already has invoices is not risk-free: each scope keeps
   its own counter, so switching scopes does not carry over or reset any existing counter — a fresh
   one is started instead. If the new scope's counter produces a number that was already issued
   under the previous scope for the current period, invoice generation will fail on the database's
   unique constraint. To avoid this, only change the `scope` at the very start of a new period (e.g.
   right after midnight on the 1st of a month), before any invoice has been issued in it.

1. Run doctrine migrations when upgrading — the `sylius_invoicing_plugin_sequence` table gains
   `type`, `year` and `month` columns, and unique indexes are created on the sequence scope and on
   the invoice number. Migrations are provided for both MySQL and PostgreSQL. In the unlikely case
   your database contains duplicated invoice numbers, the migration will fail and the duplicates
   have to be resolved manually first. You can check for duplicates upfront with:

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
