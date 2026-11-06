<?php

fwrite(STDERR, "This invoice-specific script has been replaced.\n\n");
fwrite(STDERR, "Preview:\n");
fwrite(STDERR, "  php scripts/apply_service_discount.php BSA853 --service=\"BOTOX FULL HAIR\" --amount=2000\n\n");
fwrite(STDERR, "Apply:\n");
fwrite(STDERR, "  php scripts/apply_service_discount.php BSA853 --service=\"BOTOX FULL HAIR\" --amount=2000 --execute\n\n");
exit(1);
