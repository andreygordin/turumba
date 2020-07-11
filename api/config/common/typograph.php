<?php

/**
 * @link https://turumba.ru
 * @copyright Copyright (c) 2019 Turumba
 * @author Andrey Gordin <andrey@gordin.su>
 */

declare(strict_types=1);

use EMT\EMTypograph;

return [
    EMTypograph::class => function (): EMTypograph {
        $typograph = new EMTypograph();
        $typograph->setup(
            [
                'Text.paragraphs' => 'off',
                'Text.breakline' => 'off',
                'OptAlign.oa_oquote' => 'off',
                'OptAlign.oa_obracket_coma' => 'off',
                'Space.clear_percent' => 'off',
            ]
        );
        return $typograph;
    },
];
