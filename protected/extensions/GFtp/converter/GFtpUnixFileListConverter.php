<?php

/**
 * Converts FTP file list results to GFile.
 * 
 * @author Hervé Guenot
 * @link http://www.guenot.info
 * @copyright Copyright &copy; 2012 Hervé Guenot
 * @license GNU LESSER GPL 3
 * @version 1.0
 */
class GFtpUnixFileListConverter extends GFtpFileListConverter {
	
	public function format($arr) {
		
		// line format : 
		// 0:[rights] 2:[??] 4:[user] 6:[group] 8:[size] 10-14:[mdTime] 16-:[filename]

		$config = array( 
			'class' => 'GFtpFile',
			'rights' => $arr[0],
			'user' => $arr[4],
			'group' => $arr[6],
			'size' => $arr[8],
			'mdTime' => $arr[10]." ".$arr[12]." ".$arr[14],
			'filename' => implode('', array_slice($arr, 16))
		);
		
		return Yii::createComponent($config);
	}

}
