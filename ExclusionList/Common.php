<?php

namespace OuterEdge\OpenTelemetry\ExclusionList;

class Common implements ExclusionListInterface
{
    public static function getPatterns(): array
    {
        return [
            'Can not resolve reCAPTCHA parameter',
            'maintenance mode is enabled',
            'No such file or directory',
            'does not exists',
            'Front controller reached 100 router match iterations',
            'Environment emulation nesting is not allowed',
            'No such entity with cartId = ',
            'Unable to resolve the source file',
            'Your card has insufficient funds',
            'Unsupported image format',
            'Order doesn\'t have a paypal_order_id',
            'Compilation from source: LESS file is empty',
            'Requested path \'_cache/merged/'
        ];
    }
}
