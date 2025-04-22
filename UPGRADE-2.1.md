# UPGRADE FROM 2.0 TO 2.1

### Twig hooks
- `'sylius_admin.invoice.show.content.header.title_block.title.subtitle'`- hook has been deprecated and disabled. Content of this hook has been moved to `'sylius_admin.invoice.show.content.sections.details'`section
- `'sylius_admin.invoice.show.content.sections.card'`- hook has been deprecated and disabled. Content of this hook has been moved to `'sylius_admin.invoice.show.content.sections.details'`section
