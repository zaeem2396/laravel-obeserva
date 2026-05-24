<?php

declare(strict_types=1);

if (extension_loaded('pcov') || extension_loaded('xdebug')) {
    exit(0);
}

fwrite(STDERR, "No code coverage driver found. Install php-pcov or enable php-xdebug.\n");

exit(1);
