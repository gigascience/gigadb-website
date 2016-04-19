<?php

class ReadFile
{

    const TEMP_FOLDER = '/tmp/';
    const PYTHON_SCRIPT = '/vagrant/protected/scripts/read_stat_script.py';
    const RTF_SCRIPT = '/vagrant/protected/scripts/parsertf.php';

    /**
     * Download the file to the server to get easy way to read the file
     * 
     * @param type $file_url
     * @param type $file_name
     */
    public static function downloadRemoteFile($file_url, $file_name)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $file_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        curl_close($ch);

        $file = fopen(self::TEMP_FOLDER . $file_name, "w+");
        fputs($file, $data);
        fclose($file);
    }

    /**
     * Read content in text file
     * 
     * @param string $url
     * @return string
     */
    public static function readTextFileFromUrl($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);

        return $result;
    }
    
    /**
     * Read content in text file
     * 
     * @param string $name
     * @return string
     */
    public static function readTextFile($name)
    {
        $content = '';
        $contents = file(self::TEMP_FOLDER . $name);
        
        foreach ($contents as $line) {
            $content .= $line;
        }
        
        return $content;
    }

    /**
     * Read content in file doc
     * 
     * @param string $file_name
     * @return string
     */
    public static function readDocFile($file_name)
    {
        // Need to install antiword > sudo apt-get install antiword
        return shell_exec('antiword ' . self::TEMP_FOLDER . $file_name);
    }

    /**
     * Read content in file fasta, fastq, fa, seq, bam, sam, cram
     * 
     * @param string $file_name
     * @return string
     */
    public static function readPythonFile($file_name)
    {
        $aminoAcids = $nucleotides = 0;
        $result = $output = array();
        exec('python ' . self::PYTHON_SCRIPT . ' ' . self::TEMP_FOLDER . $file_name, $output);
        if (file_exists(self::TEMP_FOLDER . 'result.txt')) {
            $result = explode("\n", file_get_contents(self::TEMP_FOLDER . 'result.txt'));
            
            foreach ($result as $line) {
                $data = explode(',', $line);
                if ($data[0] == 'acids') {
                    $aminoAcids = $data[1];
                }
                if ($data[0] == 'nucleotides') {
                    $nucleotides = $data[1];
                }
            }
        }
        
        return array($aminoAcids, $nucleotides);
    }

    /**
     * Read table file and return number of columns and rows
     * 
     * @param string $file_name
     * @param string $extension
     * @return array
     */
    public static function readTableFile($file_name, $extension)
    {
        $columns = $rows = 0;
        $data = array();
        $separater = $extension == 'csv' ? "," : "\t";

        if (($handle = fopen(self::TEMP_FOLDER . $file_name, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, $separater)) !== FALSE) {
                $columns = count($data) > $columns ? count($data) : $columns;
                $rows ++;
            }
            fclose($handle);
        }

        return array($rows, $columns);
    }

    public static function readRtfFile($file_name)
    {
        return shell_exec('php ' . self::RTF_SCRIPT . ' ' . self::TEMP_FOLDER . $file_name);
    }

    private static function rtf_isPlainText($s)
    {
        $arrfailAt = array("*", "fonttbl", "colortbl", "datastore", "themedata");
        for ($i = 0; $i < count($arrfailAt); $i++)
            if (!empty($s[$arrfailAt[$i]]))
                return false;
        return true;
    }

    /**
     * Read Docx ODT content
     * 
     * @param string $file_name
     * @return string
     */
    public static function readDocxOdtFile($file_name)
    {
        //Check for extension
        $ext = end(explode('.', self::TEMP_FOLDER . $file_name));
        $dataFile = $ext == 'docx' ? "word/document.xml" : "content.xml";

        //Create a new ZIP archive object
        $zip = new ZipArchive;

        // Open the archive file
        if (true === $zip->open(self::TEMP_FOLDER . $file_name)) {
            // If successful, search for the data file in the archive
            if (($index = $zip->locateName($dataFile)) !== false) {
                // Index found! Now read it to a string
                $text = $zip->getFromIndex($index);
                // Load XML from a string
                // Ignore errors and warnings
                $xml = DOMDocument::loadXML($text, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                // Remove XML formatting tags and return the text
                return strip_tags($xml->saveXML());
            }
            //Close the archive file
            $zip->close();
        }

        // In case of failure return a message
        return "";
    }

    /**
     * Read PDf content
     * 
     * @param string $file_name
     * @return string
     */
    public static function readPdfFile($file_name)
    {
        $a = new PDF2Text;
        $a->setFilename(self::TEMP_FOLDER . $file_name);
        $a->decodePDF();
        return $a->output();
    }

    /**
     * Read Excel file
     * 
     * @param string $file_name
     * @return array
     */
    public static function readXlsFile($file_name)
    {
        $excel = new Spreadsheet_Excel_Reader(self::TEMP_FOLDER . $file_name);
        return array($excel->rowcount(), $excel->colcount());
    }

    /**
     * Read ExcelX  file
     * 
     * @param string $file_name
     * @return array
     */
    public static function readXlsxFile($file_name)
    {
        $xlsx = new SimpleXLSX(self::TEMP_FOLDER . $file_name);
        return $xlsx->dimension();
    }

    /**
     * Read Ods File
     * 
     * @param string $file_name
     * @return array
     */
    public static function readOdsFile($file_name)
    {
        $rows = $columns = 0;
        $reader = new SpreadsheetReader_ODS(self::TEMP_FOLDER . $file_name);
        foreach ($reader as $line) {
            $rows = empty($line[0]) ? $rows : $rows + 1;
            if (!empty($line[0])) {
                foreach ($line as $key => $cell) {
                    $columns = !empty($cell) ? $key : $columns;
                }
            }
        }
        
        return array($rows, $columns + 1);
    }

}
