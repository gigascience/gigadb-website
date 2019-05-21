<?php

class FtpHelper
{
    const HOST = 'parrot.genomics.cn';

    //'user99'
    //'WhiteLabel'

    public static function getListOfFilesWithSizes($user, $password)
    {
        $ftpConn = ftp_connect(self::HOST);

        if (!$ftpConn) {
            throw new Exception('Failed to connect to ftp.');
        }

        $ftpLogin = ftp_login($ftpConn, $user, $password);
        if (!$ftpLogin) {
            throw new Exception('Username or password is invalid.');
        }

        ftp_pasv($ftpConn, true);
        $files = ftp_nlist($ftpConn, ".");

        $sizes = array();
        foreach ($files as $file) {
            $size = ftp_size($ftpConn, $file);

            $sizes[$file] = $size;
        }

        ftp_close($ftpConn);

        return $sizes;
    }
}