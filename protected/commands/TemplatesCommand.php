<?

class TemplatesCommand extends CConsoleCommand {
    public function actionAdd() {
        $dir = MainHelper::getTemplatesDir();
        if ($handle = opendir($dir)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    echo "$entry:\n";

                    $fileName = pathinfo($entry, PATHINFO_FILENAME);
                    $templateName = str_replace('sample_template-', '', $fileName);
                    $template = TemplateName::findByAttrName($templateName);
                    if ($template) {
                        echo "Template $templateName already exist.\n";
                        continue;
                    }

                    $rows = CsvHelper::parse($dir . '/' . $entry);
                    if (!$rows) {
                        echo "Cannot be empty.\n";
                        continue;
                    }

                    $row = isset($rows[0]) ? $rows[0] : array();
                    if (!isset($row[0]) || strtolower($row[0]) !== strtolower('Sample name')) {
                        echo "First column name must be \"Sample name\".\n";
                    } elseif (!isset($row[1]) || strtolower($row[1]) !== strtolower('Species name')) {
                        echo "Second column name must be \"Species name\".\n";
                    } elseif (!isset($row[2]) || strtolower($row[2]) !== strtolower('Description')) {
                        echo "Third column name must be \"Description\".\n";
                    } else {
                        $attrs = array();
                        for ($i = 3, $n = count($row); $i < $n; $i++) {
                            $attrName = trim($row[$i]);
                            if (!$attrName) {
                                continue;
                            }
                            $attr = Attribute::findByAttrName($attrName);
                            if (!$attr) {
                                $attr = new Attribute();
                                $attr->attribute_name = $attrName;
                                $attr->save();
                            }

                            $attrs[] = $attr;
                        }

                        $template =  new TemplateName();
                        $template->template_name = $templateName;
                        $template->save();

                        $template->addAttributes($attrs);

                        echo "Template $templateName successfully added.\n";
                    }
                }
            }

            closedir($handle);
        } else {
            echo $dir . ' does\'nt exist.';
        }
    } 
}
?>

