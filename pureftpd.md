   ------------------------ AFTER AN UPLOAD ------------------------


After an upload, any external program or shell script can be spawned with the
name of the newly uploaded file as an argument. You can use that feature to
automatically send a mail when a new file arrives. Or you can pass it to a
moderation system, an anti-virus, a MD5 signature generator or whatever you
decide can be done with a file.

To support this, the server has to be configured --with-uploadscript at
compilation time. Upload scripts won't be spawned on unreadable directories.
So it's highly recommended to use upload scripts with the --customerproof
run-time option and without unreadable parent directories.
To tell the FTP server to use upload scripts, it has to be launched with the
'-o' option. Finally, you have to run another daemon called 'pure-uploadscript'
provided by this package.

IMPORTANT:

YOU MUST START PURE-FTPD _FIRST_ and _THEN_ START PURE-UPLOADSCRIPT.
THE REVERSE ORDER WON'T WORK.

For security purposes, the server never launches any external program. It's
why there is a separate daemon, that reads new uploads pushed into a named
pipe by the server. Uploads are processed synchronously and sequencially.
It's why on loaded or untrusted servers, it might be a bad idea to use
pure-uploadscript with lengthy or cpu-intensive scripts.

The easiest way to run pure-uploadscript is 'pure-uploadscript -r <script>':

/usr/local/sbin/pure-uploadscript -r /bin/antivirus.sh

The absolute path of the newly uploaded file is passed as a first argument.
Some environment variables are also filled with interesting values:

- UPLOAD_SIZE  : the size of the file, in bytes.
- UPLOAD_PERMS : the permissions, as an octal value.
- UPLOAD_UID   : the uid of the owner.
- UPLOAD_GID   : the group the file belongs to.
- UPLOAD_USER  : the name of the owner.
- UPLOAD_GROUP : the group name the file belongs to.
- UPLOAD_VUSER : the full user name, or the virtual user name. (127 chars max)

There are also some options to "pure-uploadscript":

- '-u <uid>' and '-g <gid>' to switch the account pure-uploadscript will run
as. The script will be spawned with the same identity.

- '-B' to fork in background.

Please have a look at the man page ('man pure-uploadscript') for additional
info.

