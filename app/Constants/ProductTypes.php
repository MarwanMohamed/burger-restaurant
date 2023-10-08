<?php

namespace App\Constants;

use ArchTech\Enums\Names;
use ArchTech\Enums\Values;
use ArchTech\Enums\InvokableCases;

enum ProductTypes: string
{
    use Names;
    use Values;
    use InvokableCases;

    case NORMAL = 'NORMAL';
    case COMBINED = 'COMBINED';
    case WEIGHT = 'WEIGHT';
}
