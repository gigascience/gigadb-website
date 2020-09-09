<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\BaseMessage instance of newly created mail message */

?>

<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" id="bodyTable">
    <tr>
        <td valign="top">
            <table border="0" cellpadding="0" cellspacing="0" width="600" id="emailContainer">
                <tr>
                    <td valign="top">
                        <img src="<?= $message->embed($top_img); ?>">
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <?php echo $content ?>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <img src="<?= $message->embed($bottom_img); ?>">
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <img src="<?= $message->embed($logo_img); ?>">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
    