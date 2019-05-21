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
            "Content-Description: ".$fileName."\n" ."Content-Disposition: attachment;\n" . " filename=\"".$fileName."\"; size=".@filesize($fileSource).";\n" ."Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        return @mail($to, $subject, $message, $headers, $returnpath);
    }

    public static function sendNewSubmittedDatasetToAdmin(User $sender, Dataset $dataset, $adminEmail = '')
    {
        //change dataset status to Request
        $samples =  DatasetSample::model()->findAllByAttributes(array('dataset_id'=>$dataset->id), array('order'=>'sample_id asc'));

        $sampleLink = "";
        if ($samples != null) {
            $sampleLink .= "Samples:<br/>";
            foreach ($samples as $sample) {
                $sampleLink = $sampleLink . Yii::app()->params['home_url'] . "/adminSample/view/id/" . $sample->sample_id . "<br/>";
            }
        }

        $link = Yii::app()->params['home_url'] . "/adminDataset/update/id/" . $dataset->id;
        $linkFolder ="Link File Folder:<br/>";
        $linkFolder .= (Yii::app()->params['home_url'] . "/adminFile/linkFolder/?id=".$dataset->id);

        $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";
        $adminEmail = $adminEmail ?: Yii::app()->params['adminEmail'];

        $subject = "New dataset " . $dataset->id . " submitted online by user " . $sender->id . " - " . $sender->first_name . ' ' . $sender->last_name;
        $receiveNewsletter = $sender->newsletter ? 'Yes' : 'No';
        $date = getdate();

        $message = <<<EO_MAIL

New dataset is submitted by:
<br/>
<br/>
User:  <b>{$sender->id}</b>
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
<br/>
Submission ID: <b>$dataset->id</b><br/>
$link
<br/>
$sampleLink
    <br/>
$linkFolder
        <br/>

EO_MAIL;
        $headers = "From: $from";

        /* prepare attachments */

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
        // multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        $ok1 = @mail($adminEmail, $subject, $message, $headers, $returnpath);

        //send email to user to

        $senderEmail = $sender->email;

        $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset->id.']';
        $receiveNewsletter = $sender->newsletter ? 'Yes' : 'No';
        $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
        $message = <<<EO_MAIL
Dear $sender->first_name $sender->last_name,<br/>

Thank you for submitting your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
submission "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

        $headers = "From: $from";

        /* prepare attachments */

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
        // multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        $ok2 = @mail($senderEmail, $subject, $message, $headers, $returnpath);

        return $ok1 && $ok2;
    }

    public static function sendUpdateDatasetToAdmin(User $sender, Dataset $dataset, $adminEmail = '')
    {
        $from = Yii::app()->params['app_email_name'] . " <" . Yii::app()->params['app_email'] . ">";
        $adminEmail = $adminEmail ?: Yii::app()->params['adminEmail'];

        $link = Yii::app()->params['home_url'] . "/adminDataset/update/id/" . $dataset->id;

        $subject = "Dataset " . $dataset->id . " updated online by user " . $sender->id . " - " . $sender->first_name . ' ' . $sender->last_name;
        $receiveNewsletter = $sender->newsletter ? 'Yes' : 'No';
        $date = getdate();
        $adminFileLink = Yii::app()->params['home_url'] . "/adminFile/update1/?id=" .$dataset->id;
        $message = <<<EO_MAIL
Dataset is updated by:
<br/>
<br/>
User:  <b>{$sender->id}</b>
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
<br/>
Submission ID: <b>$dataset->id</b><br/>
$link
<br/>
$adminFileLink
    <br/>
EO_MAIL;

        $headers = "From: $from";

        /* prepare attachments */

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
        // multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        $ok1 = @mail($adminEmail, $subject, $message, $headers, $returnpath);

        //send email to user to
        $sendrerEmail = $sender->email;

        //  $subject = "GigaDB update \"" . $dataset->title . '"';
        $subject = "GigaDB submission \"" . $dataset->title . '"'.' ['.$dataset->id.']';
        $receiveNewsletter = $sender->newsletter ? 'Yes' : 'No';
        $timestamp = $date['mday'] . "-" . $date['mon'] . "-" . $date['year'];
        $message = <<<EO_MAIL
Dear $sender->first_name $sender->last_name,<br/>

Thank you for updating your dataset information to GigaDB.
Our curation team will contact you shortly regarding your
updates "$dataset->title".<br/>
<br/>
In the meantime, please contact us at <a href="mailto:database@gigasciencejournal.com">database@gigasciencejournal.com</a> with any questions.<br/>
<br/>
Best regards,<br/>
<br/>
The GigaDB team<br/>
<br/>
Submission date: $timestamp
<br/>
EO_MAIL;

        $headers = "From: $from";

        /* prepare attachments */

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";
        // multipart boundary
        $message = "--{$mime_boundary}\n" . "Content-Type: text/html; charset=\"utf-8\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";

        $message .= "--{$mime_boundary}--";
        $returnpath = "-f" . $adminEmail;

        $ok2 = @mail($sendrerEmail, $subject, $message, $headers, $returnpath);

        return $ok1 && $ok2;
    }
}