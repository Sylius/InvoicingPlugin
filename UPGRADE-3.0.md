# UPGRADE FROM 2.1 TO 3.0

## Changes

1. Introduced plugin-specific Messenger buses (`sylius_invoicing.command_bus` and `sylius_invoicing.event_bus`):

- The Invoicing Plugin no longer uses the global `sylius.command_bus` or `sylius.event_bus`.
- If you have custom message handlers, middleware, or routing related to the plugin, update them to use the new plugin-specific buses.
- Example configuration:

```yaml
framework:
    messenger:
        buses:
            sylius_invoicing.command_bus:
                middleware:
                    - 'validation'
                    - 'doctrine_transaction'
            sylius_invoicing.event_bus:
                default_middleware: allow_no_handlers
```
