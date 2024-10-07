<?php

declare(strict_types=1);

class LogCurationFormatter extends \yii\base\BaseObject
{

    public static function getDisplayAttr(int $id, string $xmlData)
    {
        $short = substr($xmlData, 0, 60) . '...</resource>';
        $short = htmlspecialchars($short, ENT_QUOTES, 'UTF-8');
        $display = "<span class=\"js-short-$id\"><pre>$short</pre></span><span class=\"js-long-$id\" style=\"display: none;\">$xmlData</span>";
        $display .= "<button type='button' class='js-desc btn btn-subtle' data='$id' aria-label='show more' aria-expanded='false' aria-controls='js-long-$id'>+</button>";

        return $display;
    }
}
