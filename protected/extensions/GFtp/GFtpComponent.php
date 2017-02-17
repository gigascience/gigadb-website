<?php

Yii::import('ext.GFtp.*');
Yii::import('ext.GFtp.converter.*');

/**
 * FTP connection.
 * 
 * @author Herv� Guenot
 * @link http://www.guenot.info
 * @copyright Copyright &copy; 2012 Herv� Guenot
 * @license GNU LESSER GPL 3
 * @version 1.0
 */
class GFtpComponent extends CComponent {

	/**
	 * @var   mixed     FTP handle.
	 */
	private $handle;

	/**
	 * @var   string    FTP hostname.
	 */
	private $host;

	/**
	 * @var   string    FTP port.
	 */
	private $port;

	/**
	 * @var   string    FTP username.
	 */
	private $user;

	/**
	 * @var   string    FTP password.
	 */
	private $pass;


	/**
	 * @var   mixed     Used for passing data to error hanling function.
	 */
	private $param = "";


	/**
	 * @var   GFtpFileListConverter Converts string array in GFtpFile array.
	 */
	private $fileListConverter;

	/**
	 * @var   int       Connection timeout.
	 */
	public $timeout;

	/**
	 * @var   bool      Connect in passive mode
	 */
	public $passive = true;

	private $ex;

	// *************************************************************************
	// CONSTRUCTOR / DESTRUCTOR
	// *************************************************************************
	/**
	 * Build a new FTP connection.
	 *
	 * @param string    $connectString FTP connection string (like ftp://[<user>[:<pass>]@]<host>[:<port>])
	 * @param int       $timeout       Connection timeout
	 */
	public function __construct($connectString = "", $timeout = 90, $passive = true) {
		GFtpUtils::initialize();

		if (isset($connectString) && is_string($connectString) && trim($connectString) !== "")
			$this->setConnectionString($connectString);
		$this->timeout = $timeout;
		$this->passive = $passive;
		$this->setFileListConverter(new GFtpUnixFileListConverter());
	}

	/**
	 * Destructor. Try to close FTP connection.
	 */
	public function __destruct() {
		try {
			$this->close();
		} catch(Exception $ex){
			// silently close...
		}
	}

	/**
	 * Sets a new connection string. If connection is already openned, try to close it before.
	 *
	 * @param string    $connectionString FTP connection string (like ftp://[<user>[:<pass>]@]<host>[:<port>])
	 *
	 * @throws GFtpException if <i>connectionString</i> is not valid or if could not close an already openned connection.
	 */
	public function setConnectionString($connectionString) {
		if (!isset($connectionString) || !is_string($connectionString) || trim($connectionString) === "") {
			throw new GFtpException(
					Yii::t('gftp', "{connectString} is not a valid connection string", array('{connectString}' => $connectionString))
			);
		}
			
		try {
			$p = new GFtpParser();
			$parts = $p->parse($connectionString);
			Yii::trace(CVarDumper::dumpAsString($parts), 'gftp');
		} catch (Exception $e) {
			throw new GFtpException(
					Yii::t('gftp', "{connectString} is not a valid connection string: {message}", array('{connectString}' => $connectionString, '{message}' => $e->getMessage()))
			);
		}

		$this->close();

		if (isset($parts['host']) && trim($parts['host']) != "") {
			$this->host = trim($parts['host']);
		}else{
			$this->host = "";
		}
		if (isset($parts['port']) && trim($parts['port']) != "") {
			$this->port = $parts['port'];
		} else {
			$this->port = 21;
		}
		if (isset($parts['user']) && trim($parts['user']) != "") {
			$this->user = trim($parts['user']);
		}else{
			$this->user = "";
		}
		if (isset($parts['pass']) && trim($parts['pass']) != "") {
			$this->pass = trim($parts['pass']);
		}else{
			$this->pass = "";
		}
	}

	/**
	 * Returns the connection string with or without password.
	 *
	 * @param bool      $withPassword     if <strong>TRUE</strong>, include password in returned connection string.
	 *
	 * @return string Connection string.
	 */
	public function getConnectionString($withPassword=false) {
		if ($withPassword === true) {
			return 'ftp://' . $this->user . ':' . $this->pass . '@' . $this->host . ':' . $this->port;
		} else {
			return 'ftp://' . $this->user . '@' . $this->host . ':' . $this->port;
		}
	}

	/**
	 * Returns the file list converter used to convert full file list (string array) in GFtpFile array.
	 *
	 * @return GFtpFileListConverter The current file list converter
	 *
	 * @see GFtp::ls
	 */
	public function getFileListConverter() {
		return $this->fileListConverter;
	}


	/**
	 * Change the current file list converter.
	 *
	 * @param GFtpFileListConverter $fileListConverter the new file list converter.
	 *
	 * @throws CException If type of $fileListConverter is not valid.
	 */
	public function setFileListConverter($fileListConverter) {
		if (! ($fileListConverter instanceof GFtpFileListConverter)) {
			throw new CException("Result parser must implements GFtpFullFileListParser");
		}

		$this->fileListConverter = $fileListConverter;
	}

	// *************************************************************************
	// ERROR HANDLING
	// *************************************************************************
	/**
	 * Handles FTP error (ftp_** functions sometimes use PHP error instead of methofr return).
	 * It throws GFtpException when ftp_** error is found.
	 *
	 * @param string    $function         FTP function name
	 * @param string    $message          Error message
	 *
	 * @return GFtpException if PHP error on ftp_*** method is found, null otherwise.
	 */
	public function createException($function, $message) {
		if ($function == 'ftp_connect()') {
			$this->handle = false;
			return new GFtpException(
					Yii::t('gftp', 'Could not connect to FTP server "{host}" on port "{port}": {message}',
							array('{host}' => $this->host, '{port}' => $this->port, '{message}' => $message)
					)
			);
		} else if ($function == 'ftp_close()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not close connection to FTP server "{host}" on port "{port}": {message}',
							array('{host}' => $this->host, '{port}' => $this->port, '{message}' => $message)
					)
			);
		} else if ($function == 'ftp_nlist()' || $function == 'ftp_rawlist()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not read folder "{folder}" on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_mkdir()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not create folder "{folder}" on "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_rmdir()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not remove folder "{folder}" on "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_cdup()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not move to parent directory on "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_chdir()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not move to folder "{folder}" on "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_pwd()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not get current folder on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{folder}' => $this->param)
					)
			);
		} else if ($function == 'ftp_chmod()') {
			return new GFtpException(
					Yii::t('gftp', 'Could change mode (to "{mode}") of file "{file}" on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{file}' => $this->param['file'], '{mode}' => $this->param['mode'])
					)
			);
		} else if ($function == 'ftp_put()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not put file "{local_file}" on "{remote_file}" on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{remote_file}' => $this->param['remote_file'], '{local_file}' => $this->param['local_file'])
					)
			);
		} else if ($function == 'ftp_get()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not synchronously get file "{remote_file}" from server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{remote_file}' => $this->param['remote_file'])
					)
			);
		} else if ($function == 'ftp_size()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not get size of file "{file}" on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{file}' => $this->param)
					)
			);
		} else if ($function == 'ftp_nb_get()' || $function == 'ftp_nb_continue()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not asynchronously get file "{remote_file}" from server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{remote_file}' => $this->param['remote_file'])
					)
			);
		} else if ($function == 'ftp_rename()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not rename file "{oldname}" to "{newname}" on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{oldname}' => $this->param['oldname'], '{newname}' => $this->param['newname'])
					)
			);
		} else if ($function == 'ftp_delete()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not delete file "{file}" on server "{host}" : {message}',
							array('{host}' => $this->host, '{message}' => $message, '{file}' => $this->param)
					)
			);
		} else if ($function == 'ftp_pasv()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not {set} passive mode on server "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{set}' => $this->param ? "set" : "unset")
					)
			);
		} else if ($function == 'ftp_mdtm()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not get modification time of file "{file}" on server "{host}"',
							array('{host}' => $this->host, '{message}' => $message, '{file}' => $this->param)
					)
			);
		} else if ($function == 'ftp_exec()' || $function == 'ftp_raw()' || $function == 'ftp_site()') {
			return new GFtpException(
					Yii::t('gftp', 'Could not execute command "{command}" on "{host}": {message}',
							array('{host}' => $this->host, '{message}' => $message, '{command}' => $this->param)
					)
			);
		}

		return null;
	}

	// *************************************************************************
	// UTILITY METHODS
	// *************************************************************************
	/**
	 * Connects and log in to FTP server if not already login.
	 * Call to {link GFTp::connect} and {@link GTP::login} is not mandatory.
	 * Must be called in each method, before executing FTP command.
	 *
	 * @param bool      $login         Flag indicating if login will be done.
	 *
	 * @see GFTp::connect
	 * @see GFTp::login
	 *
	 * @throws GFtpException if connection of login onto FTP server failed.
	 */
	protected function connectIfNeeded($login = true) {
		if (!isset($this->handle) || $this->handle == null) {
			$this->connect();
				
			if ($login && $this->user != null && $this->user != "") {
				$this->login($this->user, $this->pass);
			}
		}
	}

	/**
	 * Checks if string starts with another string.
	 *
	 * @param string    $haystack      The string to search in.
	 * @param string    $needle        The value being searched for.
	 *
	 * @return bool   This function returns <strong>TRUE<strong> if string <strong><i>haystack</i></strong> begins with <strong><i>needle</i></strong>.
	 */
	protected function strStarts($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	// *************************************************************************
	// FTP METHOD
	// *************************************************************************
	/**
	 * Connect to FTP server.
	 *
	 * throws GFtpException If connection failed.
	 */
	public function connect() {
		if (isset($this->handle) && $this->handle != null) {
			$this->close();
		}
		$this->handle = ftp_connect($this->host, $this->port, $this->timeout);
		if ($this->handle === false) {
			$this->handle = false;
			throw new GFtpException(
					Yii::t('gftp', 'Could not connect to FTP server "{host}" on port "{port}"',
							array('{host}' => $this->host, '{port}' => $this->port)
					)
			);
		} else {
			$this->onConnectionOpen(new CEvent($this));
		}
	}


	/**
	 * Log into the FTP server. If connection is not openned, it will be openned before login.
	 *
	 * @param string    $user          Username used for log on FTP server.
	 * @param string    $password      Password used for log on FTP server.
	 *
	 * @throws GFtpException if connection failed.
	 */
	public function login ($user = null, $password = null) {
		$this->connectIfNeeded(false);
		if (!isset($user) || $user == null) $user = $this->user;
		if (!isset($password) || $password == null) $password = $this->pass;
		if (ftp_login($this->handle, $user, $password) === false) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not login to FTP server "{host}" with user "{user}"',
							array('{host}' => $this->host, '{user}' => $user)
					)
			);
		} else {
			if ($this->passive) {
				try {
					$this->pasv($this->passive);
				}catch(GFtpException $e){
						
				}
			}
			$this->onLogin(new CEvent($this, $user));
		}
	}

	/**
	 * Returns list of files in the given directory.
	 *
	 * @param string    $dir           The directory to be listed.
	 *                                 This parameter can also include arguments, eg. $ftp->ls("-la /your/dir");
	 *                                 Note that this parameter isn't escaped so there may be some issues with filenames containing spaces and other characters.
	 * @param string    $full          List full dir description.
	 * @param string    $recursive     Recursively list folder content
	 *
	 * @return GFtpFile[] Array containing list of files.
	 */
	public function ls($dir = ".", $full = false, $recursive = false) {
		$this->connectIfNeeded();
		$this->param = $dir;

		$res = array();
		if (!$full) {
			$opts = $recursive ? "-R " : "";
			$res = ftp_nlist($this->handle, $opts.$dir);
		} else {
			$res = ftp_rawlist($this->handle, $dir, $recursive);
		}

		if ($res === false) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not read folder "{folder}" on server "{host}"',
							array('{host}' => $this->host, '{folder}' => $dir)
					)
			);
		}

		if ($full) {
			$res = $this->fileListConverter->parse($res);
		} else {
			$arr = array();
				
			foreach ($res as $file) {
				$arr[] = Yii::createComponent('GFtpFile', array('filename' => $file));
			}
				
			$res = $arr;
		}

		return $res;
	}

	/**
	 * Turns on or off passive mode.
	 *
	 * @param bool      $pasv          If <strong>TRUE</strong>, the passive mode is turned on, else it's turned off.
	 */
	public function pasv($pasv) {
		$this->connectIfNeeded();
		$this->param = $pasv;

		if (!ftp_pasv($this->handle, $pasv === true)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not {set} passive mode on server "{host}": {message}',
							array('{host}' => $this->host, '{set}' => $pasv ? "set" : "unset")
					)
			);
		}
	}

	/**
	 * Close FTP connection.
	 *
	 * @throws GFtpException Raised when error occured when closing FTP connection.
	 */
	public function close() {
		if (isset($this->handle) && $this->handle != null) {
			if (!ftp_close($this->handle)) {
				throw new GFtpException(
						Yii::t('gftp', 'Could not close connection to FTP server "{host}" on port "{port}"',
								array('{host}' => $this->host, '{port}' => $this->port)
						)
				);
			} else {
				$this->handle = false;
				$this->onConnectionClose(new CEvent($this));
			}
		}
	}

	/**
	 * Create a new folder on FTP server.
	 *
	 * @param string    $dir           Folder to create on server (relative or absolute path).
	 *
	 * @throws GFtpException If folder creation failed.
	 */
	public function mkdir($dir) {
		$this->connectIfNeeded();
		$this->param = $dir;

		if (!ftp_mkdir($this->handle, $dir)) {
			throw new GFtpException(
					Yii::t('gftp', 'An error occured while creating folder "{folder}" on server "{host}"',
							array('{host}' => $this->host, '{folder}' => $dir)
					)
			);
		} else {
			$this->onFolderCreated(new CEvent($this, $dir));
		}
	}

	/**
	 * Removes a folder on FTP server.
	 *
	 * @param string    $dir           Folder to delete from server (relative or absolute path).
	 *
	 * @throws GFtpException If folder deletion failed.
	 */
	public function rmdir($dir) {
		$this->connectIfNeeded();
		$this->param = $dir;

		if (!ftp_rmdir($this->handle, $dir)) {
			throw new GFtpException(
					Yii::t('gftp', 'An error occured while removing folder "{folder}" on server "{host}"',
							array('{host}' => $this->host, '{folder}' => $dir)
					)
			);
		} else {
			$this->onFolderDeleted(new CEvent($this, $dir));
		}
	}

	/**
	 * Changes current folder.
	 *
	 * @param string    $dir           Folder to move on (relative or absolute path).
	 *
	 * @return string Current folder on FTP server.
	 *
	 * @throws GFtpException If folder deletion failed.
	 */
	public function chdir($dir) {
		$this->connectIfNeeded();
		$this->param = $dir;

		if (!ftp_chdir($this->handle, $dir)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not go to "{folder}" on server "{host}"',
							array('{host}' => $this->host, '{folder}' => $dir)
					)
			);
		} else if ($this->ex == null) {
			$this->onFolderChanged(new CEvent($this, $dir));
		}

		try {
			$dir = $this->pwd();
		} catch (GFtpException $ex) {
				
		}
		return $dir;
	}

	/**
	 * Download a file from FTP server.
	 *
	 * @param int       $mode          The transfer mode. Must be either <strong>FTP_ASCII</strong> or <strong>FTP_BINARY</strong>.
	 * @param string    $remote_file   The remote file path.
	 * @param string    $local_file    The local file path. If set to <strong>null</strong>, file will be downloaded inside current folder using remote file base name).
	 * @param bool      $asynchronous  Flag indicating if file transfert should block php application or not.
	 *
	 * @return string The full local path (absolute).
	 *
	 * @throws GFtpException If an error occcured during file transfert.
	 */
	public function get($mode, $remote_file, $local_file = null, $asynchronous = false) {
		$this->connectIfNeeded();

		if (!isset($local_file) || $local_file == null || !is_string($local_file) || trim($local_file) == "") {
			$local_file = getcwd() . DIRECTORY_SEPARATOR . basename($remote_file);
		}
		$this->param = array('remote_file' => $remote_file, 'local_file' => $local_file, 'asynchronous' => $asynchronous);

		if ($asynchronous !== true) {
			if (!ftp_get($this->handle, $local_file, $remote_file, $mode)){
				throw new GFtpException(
						Yii::t('gftp', 'Could not synchronously get file "{remote_file}" from server "{host}"',
								array('{host}' => $this->host, '{remote_file}' => $remote_file)
						)
				);
			} else{
				$this->onFileDownloaded(new CEvent($this, $this->param));
			}
		} else {
			$ret = ftp_nb_get($this->handle, $local_file, $remote_file, $mode);
				
			while ($ret == FTP_MOREDATA) {
				// continue downloading
				$ret = ftp_nb_continue($my_connection);
			}
			if ($ret == FTP_FAILED){
				throw new GFtpException(
						Yii::t('gftp', 'Could not asynchronously get file "{remote_file}" from server "{host}"',
								array('{host}' => $this->host, '{remote_file}' => $remote_file)
						)
				);
			} else{
				$this->onFileDownloaded(new CEvent($this, $this->param));
			}
		}

		return realpath($local_file);
	}

	/**
	 * Upload a file to the FTP server.
	 *
	 * @param int       $mode          The transfer mode. Must be either <strong>FTP_ASCII</strong> or <strong>FTP_BINARY</strong>.
	 * @param string    $local_file    The local file path.
	 * @param string    $remote_file   The remote file path. If set to <strong>null</strong>, file will be downloaded inside current folder using local file base name).
	 * @param bool      $asynchronous  Flag indicating if file transfert should block php application or not.
	 *
	 * @return string The full local path (absolute).
	 *
	 * @throws GFtpException If an error occcured during file transfert.
	 */
	public function put($mode, $local_file, $remote_file = null, $asynchronous = false) {
		$this->connectIfNeeded();
		$full_remote_file = $remote_file;
		if (!isset($remote_file) || $remote_file == null || !is_string($remote_file) || trim($remote_file) == "") {
			$remote_file = basename($local_file);
				
			try {
				$full_remote_file = $this->pwd() . "/" . $remote_file;
			} catch(GFtpException $e) {
			}
		}
		$this->param = array('remote_file' => $full_remote_file, 'local_file' => $local_file, 'asynchronous' => $asynchronous);

		if ($asynchronous !== true) {
			if (!ftp_put($this->handle, $remote_file, $local_file, $mode)) {
				throw new GFtpException(
						Yii::t('gftp', 'Could not put file "{local_file}" on "{remote_file}" on server "{host}"',
								array('{host}' => $this->host, '{remote_file}' => $full_remote_file, '{local_file}' => $local_file)
						)
				);
			} else{
				$this->onFileUploaded(new CEvent($this, $this->param));
			}
		} else {
			$ret = ftp_nb_put($this->handle, $remote_file, $local_file, $mode);
				
			while ($ret == FTP_MOREDATA) {
				$ret = ftp_nb_continue($this->handle);
			}
				
			if ($ret !== FTP_FINISHED) {
				throw new GFtpException(
						Yii::t('gftp', 'Could not put file "{local_file}" on "{remote_file}" on server "{host}"',
								array('{host}' => $this->host, '{remote_file}' => $full_remote_file, '{local_file}' => $local_file)
						)
				);
			} else{
				$this->onFileUploaded(new CEvent($this, $this->param));
			}
		}

		return $full_remote_file;
	}

	/**
	 * Deletes specified files from FTP server.
	 *
	 * @param string    $path          The file to delete.
	 *
	 * @throws GFtpException If file could not be deleted.
	 */
	public function delete($path) {
		$this->connectIfNeeded();
		$this->param = $path;

		if (!ftp_delete($this->handle, $path)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not delete file "{file}" on server "{host}"',
							array('{host}' => $this->host, '{file}' => $path)
					)
			);
		} else {
			$this->onFileDeleted(new CEvent($this, $path));
		}
	}

	/**
	 * Retrieves the file size in bytes.
	 *
	 * @param string    $path          The file to delete.
	 *
	 * @return int File size.
	 *
	 * @throws GFtpException If an error occured while retrieving file size.
	 */
	public function size($path) {
		$this->connectIfNeeded();
		$this->param = $path;

		$res = ftp_size($this->handle, $path);
		if ($res < 0) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not get size of file "{file}" on server "{host}"',
							array('{host}' => $this->host, '{file}' => $path)
					)
			);
		}
		
		return $res;
	}

	/**
	 * Renames a file or a directory on the FTP server.
	 *
	 * @param string    $oldname       The old file/directory name.
	 * @param string    $newname       The new name.
	 *
	 * @throws GFtpException If an error occured while renaming file or folder.
	 */
	public function rename($oldname, $newname) {
		$this->connectIfNeeded();
		$this->param = array('oldname' => $oldname, 'newname' => $newname);

		if (!ftp_rename($this->handle, $oldname, $newname)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not rename file "{oldname}" to "{newname}" on server "{host}"',
							array('{host}' => $this->host, '{oldname}' => $oldname, '{newname}' => $newname)
					)
			);
		} else {
			$this->onFileRenamed(new CEvent($this, $this->param));
		}
	}

	/**
	 * Returns the current directory name.
	 *
	 * @return The current directory name.
	 *
	 * @throws GFtpException If an error occured while getting current folder name.
	 */
	public function pwd() {
		$this->connectIfNeeded();

		$dir = ftp_pwd($this->handle);
		if ($dir === false) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not get current folder on server "{host}"',
							array('{host}' => $this->host)
					)
			);
		}

		return $dir;
	}

	/**
	 * Set permissions on a file via FTP.
	 *
	 * @param string    $mode          The new permissions, given as an <strong>octal</strong> value.
	 * @param string    $file          The remote file.
	 *
	 * @throws GFtpException If couldn't set file permission.
	 */
	public function chmod($mode, $file) {
		$this->connectIfNeeded();
		if (substr($mode, 0, 1) != '0') {
			$mode = octdec ( str_pad ( $mode, 4, '0', STR_PAD_LEFT ) );
			$mode = (int) $mode;
		}

		$this->param = array('mode' => $mode, 'file' => $file);

		if (!ftp_chmod($this->handle, $mode, $file)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could change mode (to "{mode}") of file "{file}" on server "{host}"',
							array('{host}' => $this->host, '{file}' => $file, '{mode}' => $mode)
					)
			);
		} else {
			$this->onFileModeChanged(new CEvent($this, $this->param));
		}
	}

	/**
	 * Execute any command on FTP server.
	 *
	 * @param string    $command       FTP command.
	 * @param bool      $raw           Do not parse command to determine if it is a <i>SITE</i> or <i>SITE EXEC</i> command.
	 *
	 * @returns bool|string[] Depending on command : SITE and SITE EXEC command will returns <strong>TRUE</strong>; other command will returns an array. If <strong>$raw</strong> is set to <strong>TRUE</strong>, it always return an array.
	 *
	 * @throws GFtpException If command execution fails.
	 *
	 * @see GFtp::exec Used to execute a <i>SITE EXEC</i> command
	 * @see GFtp::site Used to execute a <i>SITE</i> command
	 * @see GFtp::raw  Used to execute any other command (or if $raw is set to <strong>TRUE</strong>)
	 */
	public function execute($command, $raw = false) {
		$this->connectIfNeeded();
		$this->param = $command;

		if (!$raw && $this->stringStarts($command, "SITE EXEC")) {
			$this->exec(substr($command, strlen("SITE EXEC")));
			return true;
		} else if (!$raw && $this->stringStarts($command, "SITE")) {
			$this->site(substr($command, strlen("SITE")));
			return true;
		} else {
			return $this->raw($command);
		}
	}

	/**
	 * Sends a SITE EXEC command request to the FTP server.
	 *
	 * @param string    $command       FTP command (does not include <i>SITE EXEC</i> words).
	 *
	 * @throws GFtpException If command execution fails.
	 */
	public function exec($command) {
		$this->connectIfNeeded();
		$this->param = "SITE EXEC " . $command;
		$exec = true;

		if (!ftp_exec($this->handle, substr($command, strlen("SITE EXEC")))) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not execute command "{command}" on "{host}"',
							array('{host}' => $this->host, '{command}' => $this->param)
					)
			);
		}
	}

	/**
	 * Sends a SITE command request to the FTP server.
	 *
	 * @param string    $command       FTP command (does not include <strong>SITE</strong> word).
	 *
	 * @throws GFtpException If command execution fails.
	 */
	public function site($command) {
		$this->connectIfNeeded();
		$this->param = "SITE " . $command;

		if (!ftp_site($this->handle, $command)) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not execute command "{command}" on "{host}"',
							array('{host}' => $this->host, '{command}' => $this->param)
					)
			);
		}
	}

	/**
	 * Sends an arbitrary command to the FTP server.
	 *
	 * @param string    $command       FTP command to execute.
	 *
	 * @return string[] The server's response as an array of strings. No parsing is performed on the response string and not determine if the command succeeded.
	 *
	 * @throws GFtpException If command execution fails.
	 */
	public function raw($command) {
		$this->connectIfNeeded();
		$this->param = $command;

		$res = ftp_raw($this->handle, $command);
		return $res;
	}

	/**
	 * Gets the last modified time for a remote file.
	 *
	 * @param string    $path          The file from which to extract the last modification time.
	 *
	 * @return string The last modified time as a Unix timestamp on success.
	 *
	 * @throws GFtpException If could not retrieve the last modification time of a file.
	 */
	public function mdtm($path) {
		$this->connectIfNeeded();
		$this->param = $path;

		$res = ftp_mdtm($this->handle, $path);
		if ($res < 0) {
			throw new GFtpException(
					Yii::t('gftp', 'Could not get modification time of file "{file}" on server "{host}"',
							array('{host}' => $this->host, '{file}' => $path)
					)
			);
		}

		return $res;
	}

	public function systype() {
		$this->connectIfNeeded();
		$res = ftp_systype($this->handle);
		return $res;
	}

	/* *********************************
	 * EVENTS SECTION
	*/
	/**
	 * Raised when connection to FTP server was openned.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onConnectionOpen($event) {
		$this->raiseEvent('onConnectionOpen', $event);
	}

	/**
	 * Raised when connection to FTP server was closed.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onConnectionClose($event) {
		$this->raiseEvent('onConnectionClose', $event);
	}

	/**
	 * Raised when users has logged in on the FTP server.
	 * Username is stored in : <code>$event->params</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onLogin($event) {
		$this->raiseEvent('onLogin', $event);
	}

	/**
	 * Raised when a folder was created on FTP server.
	 * Folder name is stored in : <code>$event->params</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFolderCreated($event) {
		$this->raiseEvent('onFolderCreated', $event);
	}

	/**
	 * Raised when a folder was deleted on FTP server.
	 * Folder name is stored in : <code>$event->params</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFolderDeleted($event) {
		$this->raiseEvent('onFolderDeleted', $event);
	}

	/**
	 * Raised when current FTP server directory has changed.
	 * New current folder is stored in : <code>$event->params</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFolderChanged($event) {
		$this->raiseEvent('onFolderChanged', $event);
	}

	/**
	 * Raised when a file was downloaded from FTP server.
	 *
	 * Local filename is stored in : <code>$event->params['local_file']</code>.
	 * Remote filename is stored in : <code>$event->params['remote_file']</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFileDownloaded($event) {
		$this->raiseEvent('onFileDownloaded', $event);
	}

	/**
	 * Raised when a file was uploaded to FTP server.
	 *
	 * Local filename is stored in : <code>$event->params['local_file']</code>.
	 * Remote filename is stored in : <code>$event->params['remote_file']</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFileUploaded($event) {
		$this->raiseEvent('onFileUploaded', $event);
	}

	/**
	 * Raised when file's permissions was changed on FTP server.
	 *
	 * Remote filename is stored in : <code>$event->params['file']</code>.
	 * New permisseion are stored in octal value in : <code>$event->params['mode']</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFileModeChanged($event) {
		$this->raiseEvent('onFileModeChanged', $event);
	}

	/**
	 * Raised when a file was deleted on FTP server.
	 * Remote filename is stored in : <code>$event->params</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFileDeleted($event) {
		$this->raiseEvent('onFileDeleted', $event);
	}

	/**
	 * Raised when a file or folder was renamed on FTP server.
	 * Old filename is stored in : <code>$event->params['oldname']</code>.
	 * New filename is stored in : <code>$event->params['newname']</code>.
	 *
	 * @param $event CEvent Event parameter.
	 */
	public function onFileRenamed($event) {
		$this->raiseEvent('onFileRenamed', $event);
	}

}

