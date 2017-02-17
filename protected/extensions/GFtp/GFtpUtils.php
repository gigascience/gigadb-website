<?php

/**
 * Utility class for FTP component.
 * 
 * @author Hervé Guenot
 * @link http://www.guenot.info
 * @copyright Copyright &copy; 2012 Hervé Guenot
 * @license GNU LESSER GPL 3
 * @version 1.0
 */
class GFtpUtils {

	/** @var GFtpUtils Global instance of GFtpUtils. */
	private static $_instance = null;

	/**
	 * Returns unique instance of GFtpUtils.
	 *
	 * @return GFtpUtils Unique instance of GFtpUtils
	 */
	public static function getInstance() {
		if (self::$_instance == null) {
			self::$_instance = new GFtpUtils();
		}

		return self::$_instance;
	}

	/**
	 * Return if a {@link GFtpFile} is a directory (based on user rights) or not.
	 *
	 * @param GFtpFile $data File to test
	 *
	 * @return boolean <strong>TRUE</strong> if file is a directory, <strong>FALSE</strong> otherwise.
	 */
	public static function isDir($data) {
		return substr($data->rights, 0, 1) == "d";
	}

	/**
	 * Build filename for GFtpWidget.
	 *
	 * @param GFtpFile $data Current {@link GFtpFile}
	 * @param string $navKey $_GET index used to get current folder.
	 * @param string $baseFolder Base folder for navigation on FTP server.
	 * @param boolean $allowNavigation Flag indicating if widget allows navigation on FTP server.
	 *
	 * @return string Displayed filename
	 */
	public static function displayFilename ($data, $navKey, $baseFolder, $allowNavigation) {
		if ($allowNavigation && self::isDir($data)) {
			$dir = $baseFolder."/".$data->filename;
			if ($baseFolder == "/") {
				$dir = "/".$data->filename;
			}
			if ($data->filename == '..') {
				$dir = dirname($baseFolder);
				$dir = str_replace('\\', '/', $dir);
			}
			$arr = array_merge(array(""), $_GET, array($navKey => $dir));
			return CHtml::link($data->filename, $arr);
		} else {
			return $data->filename;
		}
	}

	/**
	 * Initialize global instance.
	 */
	public static function initialize() {
		self::getInstance()->_init();
	}

	/**
	 * Initialize new object (register error handler if global variable <code>YII_ENABLE_ERROR_HANDLER</code> is set to true.
	 */
	private function _init() {
		if(YII_ENABLE_ERROR_HANDLER)
			set_error_handler(array($this,'handleError'),error_reporting());
	}

	/**
	 * PHP error handler method used to catch all FTP exception.
	 *
	 * @param integer $code Level of the error raised
	 * @param string $message Error message
	 * @param string $file Filename that the error was raised in
	 * @param integer $line Line number the error was raised at
	 * @param array $context array that points to the active symbol table at the point the error occurred.
	 */
	public function handleError($code,$message,$file,$line,$context)
	{
		if (isset($context['this']) && $context['this'] instanceof GFtpComponent) {
			// disable error capturing to avoid recursive errors
			restore_error_handler();
			restore_exception_handler();
			if (isset($message)) {
				// FTP error message are formed : ftp_***(): <message>
				$messages = explode(':', $message, 2);
				$func = explode(' ', $messages[0], 2);
				$ex = $context['this']->createException($func[0], $messages[1]);
				if ($ex != null) throw $ex;
			}
		}

		Yii::app()->handleError($code,$message,$file,$line);
	}
}
