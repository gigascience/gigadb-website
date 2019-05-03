<?php

class MailHelper
{
    public static function sendUploadedDatasetToAdmin(User $sender, $fileSource, $fileName, $adminEmail = '')
    {
        $emailName = Yii::app()->params['app_email_name'];
        $email = Yii::app()->params['app_email'];
        $adminEmail = $adminEmail ?: Yii::app()->params['adminEmail'];

        // email fields: to, from, subject, and so on
        $from = $emailName." <".$email.">";
        $to = $adminEmail;
        $subject = "New dataset uploaded by user ".$sender->id." - ".$sender->first_name.' '.$sender->last_name;
        $receiveNewsletter = $sender->newsletter ? 'Yes' : 'No';
        $message = <<<EO_MAIL

New dataset is uploaded by:
<br/>
<br/>
Id:  <b>{$sender->id}</b>
<br/>
Email: <b>{$sender->email}</b>
<br/>
First Name:  <b>{$sender->first_name}</b>
<br/>
Last Name:  <b>{$sender->last_name}</b>
<br/>
Affiliation:  <b>{$sender->affiliation}</b>
<br/>
Receiving Newsletter:  <b>{$receiveNewsletter}</b>
<br/><br/>
EO_MAIL;

        $headers = "From: $from";

        /* prepare attachments */

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
        // multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" ."Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";
        $fp =    @fopen($fileSource, "rb");
        $data =    @fread($fp, filesize($fileSource));
        @fclose($fp);
        $data = chunk_split(base64_encode($data));

        $message .= "Content-Type: application/octet-stream; name=\"".$fileName."\"\n" .
            "Content-Description: ".$fileName."\n" ."Content-Disposition: attachment;\n" . " filename=\"".$fileName."\"; size=".filesize($fileSource).";\n" ."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        return @mail($to, $subject, $message, $headers, $returnpath);
    }
}