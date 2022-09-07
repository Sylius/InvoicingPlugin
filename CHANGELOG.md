# CHANGELOG

### v0.23.0 (2022-09-07)

- [#282](https://github.com/Sylius/InvoicingPlugin/issues/282) Fix translation on pdf template ([@Orkin](https://github.com/Orkin))
- [#284](https://github.com/Sylius/InvoicingPlugin/issues/284) Add directory purger before fixtures run ([@AdamKasp](https://github.com/AdamKasp))
- [#285](https://github.com/Sylius/InvoicingPlugin/issues/285) [Maintenance] Allow flex plugin during plugin installation ([@lchrusciel](https://github.com/lchrusciel))
- [#286](https://github.com/Sylius/InvoicingPlugin/issues/286) [Maintenance] Fix multi-environment .env files in test application ([@NoResponseMate](https://github.com/NoResponseMate))
- [#287](https://github.com/Sylius/InvoicingPlugin/issues/287) Unit net price and discounted unit net price on the invoice ([@Rafikooo](https://github.com/Rafikooo), [@Zales0123](https://github.com/Zales0123))
- [#207](https://github.com/Sylius/InvoicingPlugin/issues/207) Update console command definition to SF 4.4 standards ([@GSadee](https://github.com/GSadee))
- [#289](https://github.com/Sylius/InvoicingPlugin/issues/289) Fix down method of migration that changes indexes ([@GSadee](https://github.com/GSadee))

### v0.22.0 (2022-04-27)

- [#274](https://github.com/Sylius/InvoicingPlugin/issues/274) [Maintenance] Support PHP 8.1 ([@GSadee](https://github.com/GSadee))
- [#275](https://github.com/Sylius/InvoicingPlugin/issues/275) [Maintenance] Add TwigToPdfGenerator ([@coldic3](https://github.com/coldic3))
- [#277](https://github.com/Sylius/InvoicingPlugin/issues/277) [DependencyInjection] Move registering resources to prepend method ([@GSadee](https://github.com/GSadee))
- [#278](https://github.com/Sylius/InvoicingPlugin/issues/278) Optional parameter to turn off the PDF generation ([@GSadee](https://github.com/GSadee))
- [#279](https://github.com/Sylius/InvoicingPlugin/issues/279) [GitHub Actions] Include build with PDF generation disabled to the main workflow ([@GSadee](https://github.com/GSadee))

### v0.21.0 (2022-03-31)

- [#271](https://github.com/Sylius/InvoicingPlugin/issues/271) [GitHub Actions] Configure workflow to run on the workflow_dispatch event ([@GSadee](https://github.com/GSadee))
- [#272](https://github.com/Sylius/InvoicingPlugin/issues/272) Enable local file access in InvoicePdfFileGenerator ([@coldic3](https://github.com/coldic3))

### v0.20.0 (2022-02-17)

- [#270](https://github.com/Sylius/InvoicingPlugin/issues/270) [Maintenance] Support stable Sylius v1.11.0 ([@GSadee](https://github.com/GSadee))

### v0.19.0 (2022-01-25)

- [#249](https://github.com/Sylius/InvoicingPlugin/issues/249) Use PHP 7.4 syntax ([@GSadee](https://github.com/GSadee))
- [#251](https://github.com/Sylius/InvoicingPlugin/issues/251) New PDF layout ([@kulczy](https://github.com/kulczy))
- [#252](https://github.com/Sylius/InvoicingPlugin/issues/252) [Behat] Fix type of show page ([@GSadee](https://github.com/GSadee))
- [#253](https://github.com/Sylius/InvoicingPlugin/issues/253) [Maintenance] Minor installation guide improvements ([@GSadee](https://github.com/GSadee))
- [#255](https://github.com/Sylius/InvoicingPlugin/issues/255) Added missing french translations ([@Snowbaha](https://github.com/Snowbaha))
- [#261](https://github.com/Sylius/InvoicingPlugin/issues/261) Drop support for Sylius 1.9 & Symfony 5.2 ([@GSadee](https://github.com/GSadee))
- [#262](https://github.com/Sylius/InvoicingPlugin/issues/262) [PDF] Fix minor locale code typo ([@GSadee](https://github.com/GSadee))
- [#256](https://github.com/Sylius/InvoicingPlugin/issues/256) Fix invoice payment state not updating when given order is paid ()
- [#210](https://github.com/Sylius/InvoicingPlugin/issues/210) Minor admin ui redesign ([@dmitri](https://github.com/dmitri).[@perunov](https://github.com/perunov)@[@gmail](https://github.com/gmail).[@com](https://github.com/com))
- [#258](https://github.com/Sylius/InvoicingPlugin/issues/258) fix: compare when name is the same but not variant ([@Snowbaha](https://github.com/Snowbaha))
- [#266](https://github.com/Sylius/InvoicingPlugin/issues/266) Remove duplicated config for wkhtmltopdf ([@Zales0123](https://github.com/Zales0123))
- [#263](https://github.com/Sylius/InvoicingPlugin/issues/263) resolve SYLIUS_INVOICING_LOGO_FILE ()
- [#267](https://github.com/Sylius/InvoicingPlugin/issues/267) Drop support for PHP 7.4 ([@GSadee](https://github.com/GSadee))
- [#269](https://github.com/Sylius/InvoicingPlugin/issues/269) Upgrade node in GitHub Actions builds ([@GSadee](https://github.com/GSadee))
- [#268](https://github.com/Sylius/InvoicingPlugin/issues/268) Drop support for Symfony 5.3 ([@GSadee](https://github.com/GSadee))

### v0.18.1 (2021-09-15)

- [#250](https://github.com/Sylius/InvoicingPlugin/issues/250) [Maintenance] Remove wrong return types ([@lchrusciel](https://github.com/lchrusciel))

### v0.18.0 (2021-08-27)

- [#246](https://github.com/Sylius/InvoicingPlugin/issues/246) [Invoice] Add payment status ([@AdamKasp](https://github.com/AdamKasp), [@Tomanhez](https://github.com/Tomanhez), [@arti0090](https://github.com/arti0090))
- [#247](https://github.com/Sylius/InvoicingPlugin/issues/247) Update knp snappy file ([@arti0090](https://github.com/arti0090))
- [#201](https://github.com/Sylius/InvoicingPlugin/issues/201) Fix extended resource entities ([@Prometee](https://github.com/Prometee), [@lchrusciel](https://github.com/lchrusciel))

### v0.17.0 (2021-08-06)

- [#233](https://github.com/Sylius/InvoicingPlugin/issues/233) Upgrade to stable Sylius 1.10 ([@GSadee](https://github.com/GSadee))
- [#231](https://github.com/Sylius/InvoicingPlugin/issues/231) Save invoices on server during generation ([@Zales0123](https://github.com/Zales0123))
- [#232](https://github.com/Sylius/InvoicingPlugin/issues/232) Do not require visibility in PHPSpec functions ([@Zales0123](https://github.com/Zales0123))
- [#230](https://github.com/Sylius/InvoicingPlugin/issues/230) change information on WKHTMLTOPDF_PATH in README ([@DennisdeBest](https://github.com/DennisdeBest))
- [#238](https://github.com/Sylius/InvoicingPlugin/issues/238) Extract wkhtmltopdf file path to env variable ([@GSadee](https://github.com/GSadee))
- [#234](https://github.com/Sylius/InvoicingPlugin/issues/234) Rework invoice template on pdf and admin show page ([@Tomanhez](https://github.com/Tomanhez), [@GSadee](https://github.com/GSadee))
- [#240](https://github.com/Sylius/InvoicingPlugin/issues/240) Remove unneeded TemporaryFilesystem service ([@GSadee](https://github.com/GSadee))
- [#241](https://github.com/Sylius/InvoicingPlugin/issues/241) Add note to UPGRADE filem about removing TemporaryFilesystem ([@GSadee](https://github.com/GSadee))

### v0.16.1 (2021-06-23)

- [#228](https://github.com/Sylius/InvoicingPlugin/issues/228) Fix sorting and filtering invoices by order in the grid ([@hurricane-voronin](https://github.com/hurricane-voronin))

### v0.16.0 (2021-06-18)

- [#226](https://github.com/Sylius/InvoicingPlugin/issues/226) Relate Invoice with Order ([@Zales0123](https://github.com/Zales0123))
- [#220](https://github.com/Sylius/InvoicingPlugin/issues/220) Polish translations & invoice download template update ([@mysiar](https://github.com/mysiar), [@GSadee](https://github.com/GSadee))

### v0.15.0 (2021-06-02)

- [#221](https://github.com/Sylius/InvoicingPlugin/issues/221) unify usage of buses ([@SirDomin](https://github.com/SirDomin))
- [#223](https://github.com/Sylius/InvoicingPlugin/issues/223) Drop support for Sylius 1.8 ([@GSadee](https://github.com/GSadee))
- [#224](https://github.com/Sylius/InvoicingPlugin/issues/224) Add support for Sylius 1.10 + require PHP ^7.4 || ^8.0 ([@GSadee](https://github.com/GSadee))
- [#225](https://github.com/Sylius/InvoicingPlugin/issues/225) Bring back easy-coding-standard + apply CS fixes ([@GSadee](https://github.com/GSadee))

### v0.14.0 (2021-03-18)

- [#216](https://github.com/Sylius/InvoicingPlugin/issues/216) toggle doctrine migration ([@arti0090](https://github.com/arti0090))
- [#217](https://github.com/Sylius/InvoicingPlugin/issues/217) Add gitkeep file to src/Migrations directory stays in code ([@arti0090](https://github.com/arti0090))
- [#168](https://github.com/Sylius/InvoicingPlugin/issues/168) Define logo-file as parameter ([@BigBadBassMan](https://github.com/BigBadBassMan))
- [#218](https://github.com/Sylius/InvoicingPlugin/issues/218) Remove twig errors configuration from test application ([@GSadee](https://github.com/GSadee))

### v0.13.0 (2021-03-04)

- [#199](https://github.com/Sylius/InvoicingPlugin/issues/199) Bugfix | Update README file to use new route format ([@stloyd](https://github.com/stloyd))
- [#202](https://github.com/Sylius/InvoicingPlugin/issues/202) Add translation in invoice PDF file ([@webdudi](https://github.com/webdudi), )
- [#212](https://github.com/Sylius/InvoicingPlugin/issues/212) Upgrade to Sylius 1.9  ([@Tomanhez](https://github.com/Tomanhez))
- [#213](https://github.com/Sylius/InvoicingPlugin/issues/213) Minor fix sylius version in composer 1.9RC to 1.9 ([@Tomanhez](https://github.com/Tomanhez))

### v0.12.0 (2020-11-16)

- [#190](https://github.com/Sylius/InvoicingPlugin/issues/190) Add conflict to ^8.3.25 of symplify/package-builder to fix the build ([@Prometee](https://github.com/Prometee))
- [#187](https://github.com/Sylius/InvoicingPlugin/issues/187) Use Sylius UI instead of Sonata event ([@Prometee](https://github.com/Prometee))
- [#186](https://github.com/Sylius/InvoicingPlugin/issues/186) [Enhancement] Refactor and deprecate Invoice repository class ([@Prometee](https://github.com/Prometee))
- [#191](https://github.com/Sylius/InvoicingPlugin/issues/191) Update upgrade to v0.12.0 ([@GSadee](https://github.com/GSadee))
- [#193](https://github.com/Sylius/InvoicingPlugin/issues/193) Initialize GitHub Actions ([@pamil](https://github.com/pamil))
- [#194](https://github.com/Sylius/InvoicingPlugin/issues/194) Test InvoicingPlugin via GitHub Actions ([@pamil](https://github.com/pamil))
- [#195](https://github.com/Sylius/InvoicingPlugin/issues/195) Fix Doctrine/Migrations configuration ([@Zales0123](https://github.com/Zales0123))
- [#188](https://github.com/Sylius/InvoicingPlugin/issues/188) allow override invoice shop billing data ([@jbcr](https://github.com/jbcr))
- [#196](https://github.com/Sylius/InvoicingPlugin/issues/196) Bugfix | Update channel filter template to new Symfony format ([@stloyd](https://github.com/stloyd))
- [#197](https://github.com/Sylius/InvoicingPlugin/issues/197) Bugfix | Update `admin_invoicing` routes to support new Symfony format ([@stloyd](https://github.com/stloyd))

### v0.11.0 (2020-09-22)

- [#178](https://github.com/Sylius/InvoicingPlugin/issues/178) Fix failing InvoicingPlugin build ([@GSadee](https://github.com/GSadee), )
- [#183](https://github.com/Sylius/InvoicingPlugin/issues/183) Allow for PHP 7.4 & upgrade static analysis ([@pamil](https://github.com/pamil))
- [#175](https://github.com/Sylius/InvoicingPlugin/issues/175) Switch to Doctrine/Migrations 3.0-alpha ([@pamil](https://github.com/pamil))
- [#184](https://github.com/Sylius/InvoicingPlugin/issues/184) Remove old migrations + upgrade Sylius to 1.8 ([@GSadee](https://github.com/GSadee))
- [#185](https://github.com/Sylius/InvoicingPlugin/issues/185) [Docs] Update of UPGRADE file and installation instruction ([@lchrusciel](https://github.com/lchrusciel))

### v0.10.1 (2020-01-10)

- [#167](https://github.com/Sylius/InvoicingPlugin/issues/167) Add missing migration ([@AdamKasp](https://github.com/AdamKasp))

### v0.10.0 (2019-12-06)

- [#156](https://github.com/Sylius/InvoicingPlugin/issues/156) Change test server to Symfony server ([@AdamKasp](https://github.com/AdamKasp))
- [#161](https://github.com/Sylius/InvoicingPlugin/issues/161) Remove embeddable and replace InvoicingChannel to Channel form Core ([@Tomanhez](https://github.com/Tomanhez))
- [#165](https://github.com/Sylius/InvoicingPlugin/issues/165) fix(deps): loosen constraint for symfony/messenger ([@Gounlaf](https://github.com/Gounlaf), [@pamil](https://github.com/pamil))

### v0.9.0 (2019-10-17)

- [#97](https://github.com/Sylius/InvoicingPlugin/issues/97) Make entities extendable ([@tautelis](https://github.com/tautelis), [@pamil](https://github.com/pamil))
- [#124](https://github.com/Sylius/InvoicingPlugin/issues/124) Make invoices and credit memo grid consistent ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#125](https://github.com/Sylius/InvoicingPlugin/issues/125) Passing invoice entity to invoice pdf file generator ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#126](https://github.com/Sylius/InvoicingPlugin/issues/126) added invoice screenshot ([@doctorx32](https://github.com/doctorx32))
- [#132](https://github.com/Sylius/InvoicingPlugin/issues/132) Customizations friendly improvements ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#133](https://github.com/Sylius/InvoicingPlugin/issues/133) Enhanced conflict section ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#134](https://github.com/Sylius/InvoicingPlugin/issues/134) Representative added to invoice shop billing data ([@bartoszpietrzak1994](https://github.com/bartoszpietrzak1994))
- [#135](https://github.com/Sylius/InvoicingPlugin/issues/135) Fixed typo in service definition ([@kortwotze](https://github.com/kortwotze), [@GSadee](https://github.com/GSadee))
- [#136](https://github.com/Sylius/InvoicingPlugin/issues/136) Minimise conflicts and require Messenger 4.3 ([@pamil](https://github.com/pamil))
- [#137](https://github.com/Sylius/InvoicingPlugin/issues/137) Require Symfony ^4.2 and improve autoloading ([@pamil](https://github.com/pamil))
- [#138](https://github.com/Sylius/InvoicingPlugin/issues/138) Allow to run PHPUnit without arguments and run Behat with strict mode on Travis CI ([@pamil](https://github.com/pamil))
- [#139](https://github.com/Sylius/InvoicingPlugin/issues/139) Add missing migration ([@pamil](https://github.com/pamil), [@GSadee](https://github.com/GSadee))
- [#140](https://github.com/Sylius/InvoicingPlugin/issues/140) Hotfix for TestOrderPlacedProducer ([@pamil](https://github.com/pamil))
- [#141](https://github.com/Sylius/InvoicingPlugin/issues/141) A new way to test generating previous invoices ([@pamil](https://github.com/pamil))
- [#146](https://github.com/Sylius/InvoicingPlugin/issues/146) [Admin] Unify order link in InvocingPlugin ([@Tomanhez](https://github.com/Tomanhez))
- [#148](https://github.com/Sylius/InvoicingPlugin/issues/148) Fix GenerateInvoicesCommand selects order without number ([@tom10271](https://github.com/tom10271))
- [#158](https://github.com/Sylius/InvoicingPlugin/issues/158) Update dependencies ([@pamil](https://github.com/pamil))
- [#160](https://github.com/Sylius/InvoicingPlugin/issues/160) Add migrations for tests app ([@Tomanhez](https://github.com/Tomanhez))
- [#162](https://github.com/Sylius/InvoicingPlugin/issues/162) Require GridBundle v1.7+ with embeddables support ([@pamil](https://github.com/pamil))
