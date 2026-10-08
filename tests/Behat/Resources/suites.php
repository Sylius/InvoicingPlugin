<?php

declare(strict_types=1);

use Behat\Config\Config;

return (new Config())
    ->import([
        'suites/admin/managing_invoices.php',
        'suites/customer.php',
    ])
;
