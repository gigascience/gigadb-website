<?php

declare(strict_types=1);

/** format the type field as raw instead of the default text if the xml needs to be displayed */
class CTypeFormatter extends CFormatter
{
    public function format($value, $type): string
    {
        if (is_string($value) && false !== strpos($value, '<pre>&lt;?xml')) {
            $type = "raw";
        }

        return parent::format($value, $type);
    }
}
