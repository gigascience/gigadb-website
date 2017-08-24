<?php

Yii::import('ext.GFtp.*');

/**
 * FTP connection to use as an Application Component.
 * 
 * @author Hervé Guenot
 * @link http://www.guenot.info
 * @copyright Copyright &copy; 2012 Hervé Guenot
 * @license GNU LESSER GPL 3
 * @version 1.0
 */
class GFtpApplicationComponent extends CApplicationComponent {
	
	/**
	 * @var GFtp Direct FTP connection.
	 */
	public $gftp;
	
	/**
	 * @var bool Flag indicating if component is initialized.
	 */
	private $initialized = false;

	public function __construct() {
		$this->gftp = new GFtpComponent();
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
	 * Method call for component initialization.
	 */
	public function init() {
		$this->gftp->onConnectionOpen  = array($this, 'onConnectionOpen');
		$this->gftp->onConnectionClose = array($this, 'onConnectionClose');
		$this->gftp->onLogin           = array($this, 'onLogin');
		$this->gftp->onFolderCreated   = array($this, 'onFolderCreated');
		$this->gftp->onFolderDeleted   = array($this, 'onFolderDeleted');
		$this->gftp->onFolderChanged   = array($this, 'onFolderChanged');
		$this->gftp->onFileDownloaded  = array($this, 'onFileDownloaded');
		$this->gftp->onFileUploaded    = array($this, 'onFileUploaded');
		$this->gftp->onFileModeChanged = array($this, 'onFileModeChanged');
		$this->gftp->onFileDeleted     = array($this, 'onFileDeleted');
		$this->gftp->onFileRenamed     = array($this, 'onFileRenamed');
		
		$this->gftp->connect();
		$this->gftp->login();
		$this->initialized = true;
	}
	
	public function getIsInitialized() {
		return $this->initialized;
	}
	
	/**
	 * Sets a new connection string. If connection is already openned, try to close it before.
	 * 
	 * @param string    $connectionString FTP connection string (like ftp://[<user>[:<pass>]@]<host>[:<port>])
	 * 
	 * @throws GFtpException if <i>connectionString</i> is not valid or if could not close an already openned connection.
	 */
	public function setConnectionString($connectionString) {
		$this->gftp->connectionString = $connectionString;
	}
	
	/**
	 * Returns the connection string with or without password.
	 * 
	 * @param bool      $withPassword     if <strong>TRUE</strong>, include password in returned connection string.
	 * 
	 * @return string Connection string.
	 */
	public function getConnectionString($withPassword=false) {
		return $this->gftp->getConnectionString($withPassword);
	}
	
	/**
	 * Returns the file list converter.
	 * 
	 * @return GFtpFileListConverter The current file list converter.
	 */
	public function getFileListConverter() {
		return $this->gftp->fileListConverter;
	}
	
	/**
	 * Set up the file list converter.
	 * 
	 * @param mixed $fileListConverter The file list converter should be :<ul><li>a string referencing an existing class (must inherit GFtpFileListConverter),</li><li>or an array used to build a new Yii component (@see Yii::createComponent)</li><li>or an instance of a child class of GFtpFileListConverter</li></ul>
	 */
	public function setFileListConverter($fileListConverter) {
		if ($fileListConverter instanceof GFtpFileListConverter) {
			// nothing to do
		} else if (is_string($fileListConverter)) {
			$fileListConverter = Yii::createComponent($fileListConverter);
		} else if (is_array($fileListConverter)) {
			$fileListConverter = Yii::createComponent($fileListConverter);
		} else {
			throw new GFtpException(
				Yii::t('gftp', 'Could not create file list converter component.')
			);
		}
	
		$this->gftp->fileListConverter = $fileListConverter;
	}
	
	/**
	 * Set the connection timeout.
	 * 
	 * @param int $timeout  The new connection timeout.
	 */
	public function setTimeout($timeout) {
		$this->gftp->timeout = $timeout;
	}
	
	
	/**
	 * Returns the connection timeout.
	 *
	 * @return int The connection timeout.
	 */
	public function getTimeout() {
		return $this->gftp->timeout;
	}
	
	/**
	 * Changes thepassive mode for the FTP connection.
	 * 
	 * @param bool $passive TRUE to set passive mode FALSE, otherwise.
	 */
	public function setPassive($passive) {
		$this->gftp->passive = $passive;
	}
	
	/**
	 * Returns the current passive mode state.
	 * 
	 * @return bool The current passive mode state.
	 */
	public function getPassive() {
		return $this->gftp->passive;
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
		$this->gftp->connect();
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
		$this->gftp->login($user, $password);
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
		return $this->gftp->ls($dir, $full, $recursive);
	}
	
	/**
	 * Turns on or off passive mode. 
	 * 
	 * @param bool      $pasv          If <strong>TRUE</strong>, the passive mode is turned on, else it's turned off.
	 */
	public function pasv($pasv) {
		$this->gftp->pasv($pasv);
	}
	
	/**
	 * Close FTP connection.
	 * 
	 * @throws GFtpException Raised when error occured when closing FTP connection.
	 */
	public function close() {
		$this->gftp->close();
	}
	
	/**
	 * Create a new folder on FTP server.
	 * 
	 * @param string    $dir           Folder to create on server (relative or absolute path).
	 * 
	 * @throws GFtpException If folder creation failed.
	 */
	public function mkdir($dir) {
		$this->gftp->mkdir($dir);
	}
	
	/**
	 * Removes a folder on FTP server.
	 * 
	 * @param string    $dir           Folder to delete from server (relative or absolute path).
	 * 
	 * @throws GFtpException If folder deletion failed.
	 */
	public function rmdir($dir) {
		$this->gftp->rmdir($dir);
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
		return $this->gftp->chdir($dir);
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
	public function get($remote_file, $local_file = null, $asynchronous = false, $mode = FTP_BINARY) {
		return $this->gftp->get($mode, $remote_file, $local_file, $asynchronous);
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
	public function put($local_file, $remote_file = null, $asynchronous = false, $mode = FTP_BINARY) {
		return $this->gftp->put($mode, $local_file, $remote_file, $asynchronous);
	}
	
	/**
	 * Deletes specified files from FTP server.
	 * 
	 * @param string    $path          The file to delete.
	 * 
	 * @throws GFtpException If file could not be deleted.
	 */
	public function delete($path) {
		$this->gftp->delete($path);
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
		return $this->gftp->size($path);
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
		$this->gftp->rename($oldname, $newname);
	}
	
	/**
	 * Returns the current directory name.
	 * 
	 * @return The current directory name.
	 * 
	 * @throws GFtpException If an error occured while getting current folder name.
	 */
	public function pwd() {
		return $this->gftp->pwd();
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
		return $this->gftp->chmod($mode, $file);
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
		return $this->gftp->execute($command, $raw);
	}

	/**
	 * Sends a SITE EXEC command request to the FTP server.
	 * 	
	 * @param string    $command       FTP command (does not include <i>SITE EXEC</i> words).
	 * 
	 * @throws GFtpException If command execution fails.
	 */
	public function exec($command) {
		$this->gftp->exec($command);
	}
	
	/**
	 * Sends a SITE command request to the FTP server.
	 * 	
	 * @param string    $command       FTP command (does not include <strong>SITE</strong> word).
	 * 
	 * @throws GFtpException If command execution fails.
	 */
	public function site($command) {
		$this->gftp->site($command);
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
		return $this->gftp->raw($command);
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
		return $this->gftp->mdtm($path);;
	}
	
	public function systype() {
		return $this->gftp->systype();
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

?>