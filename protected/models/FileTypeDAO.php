<?php
/**
 * Business object to interact with the FileType ActiveRecord model.
 *
 * @author Rija Menage <rija+git@cinecinetique.com>
 * @license GPL-3.0
 */
class FileTypeDAO extends yii\base\BaseObject
{
	/**
	 * function to export the list of file types as JSON
	 * @return string a JSON string representing the list of file types
	 */
	public function toJSON(): string
	{
		return json_encode(array_flip(FileType::getListTypes()));
	}
}


?>