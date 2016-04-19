<?php

/**
 * Created by : Sriram Sharma
 */
class RTFParser
{

    var $fp;  //file pointer
    var $arg1;  //argument basically filename with path
    var $match;  // result 
    var $rtfCont; // raw rtf content
    var $cp;  //custom property in a neat format
    var $bkm;   //book mark in a neat format

    /*
      Constructor initializes values
     */

    function RTFParser()
    {
        
        $this->arg1 = trim($_SERVER['argv'][1]);
        $this->fp = fopen($this->arg1, "r");
        $this->cp = array();
        $this->bkm = array();
        $this->parsebdy = 1;
    }

    /*
      Read raw contents from the file
     */

    function ReadFile()
    {
        if (!$this->fp) {
            $this->fp = fopen($this->carg1, "rw");
        }
        $this->rtfCont = fread($this->fp, filesize($this->arg1));
        $this->rtfCont_b = $this->rtfCont;
    }

    /*
      Take out all custom property in an array
     */

    function parseCustomProperty()
    {
        $this->match = array();
        preg_match_all("/\{\\\propname (.*?)\}.+?staticval (.*?)\}/", $this->rtfCont, $this->match);
    }

    function parseBookMark()
    {
        $this->match = array();
        preg_match_all("/\{\\\*.*?bkmkstart (.*?)\}(.*?)\{/", $this->rtfCont, $this->match);
        //print_r( $this->match );
    }

    /*
      store all custom prop in a neat format
     */

    function cacheCustomProp()
    {
        if ($this->match[1]) {
            foreach ($this->match[1] as $key => $val) {
                $this->cp[$val] = $this->match[2][$key];
            }
        }
    }

    function cacheBookMark()
    {
        if ($this->match[1]) {
            foreach ($this->match[1] as $key => $val) {
                $this->bkm[$val] = $this->match[2][$key];
            }
        }
    }

    function parseBody()
    {

        #$cont = preg_replace("/\\\header [\w\W\s]*?((\{[\w\W\s]*?\})([\w\W\s]*?(\{[\w\W\s]*?\})))*?\}/m","",$cont);
        #$cont = preg_replace("/\\\footer [\w\W\s]*?((\{[\w\W\s]*?\})([\w\W\s]*?(\{[\w\W\s]*?\})))*?\}/m","",$cont);		
        $this->rtfCont_b = preg_replace("/\\\objdata[\w\W\s]*?\}/m", "}", $this->rtfCont_b);
        $this->parseOLEObject();
        $this->parsePictureObject();
        $this->removeSection('shp');
        $this->removeSection('levelnumbers');
        $this->parseCustProp();
        $this->rtfCont_b = preg_replace("/rtf1[\w\W\s]+?\\\header/", "{\\\header", $this->rtfCont_b);



        $this->header = $this->parseHeader();
        $this->headerl = $this->parseHeaderl();
        $this->headerr = $this->parseHeaderr();
        $this->headerf = $this->parseHeaderf();


        $this->footer = $this->parseFooter();
        $this->footerl = $this->parseFooterl();
        $this->footerr = $this->parseFooterr();
        $this->footerf = $this->parseFooterf();

        $cont = $this->rtfCont_b;
        //$cont = preg_replace("/rtf1[\w\W\s]+?\\\par \}/","",$cont);
        $cont = preg_replace("/\{\\\*.*?bkmkstart (.*?)\}/", "", $cont);
        $cont = preg_replace("/\{\\\*.*?bkmkend (.*?)\}/", "", $cont);
        $cont = preg_replace("/\\\[\w';.-]+/", "", $cont);
        $cont = preg_replace("/[\/*\(\)\.\\\]+/", "", $cont);
        #$cont = preg_replace("/\{[\w\W\s]*?\}/","",$cont);
        $cont = preg_replace("/\\\+/", "", $cont);
        $cont = preg_replace("/\{/", "", $cont);
        $cont = preg_replace("/\}/", "", $cont);
        unset($this->rtfCont_b);
        return $cont;
    }

    function writeCustomProperty($key, $v)
    {
        #preg_match_all("/(\{\\\propname $key)(\}.+?staticval )(.*?)(\})/",$this->rtfCont,$match);
        #print_r($match);

        $this->rtfCont = preg_replace("/(\{\\\propname $key\}.+?staticval )(.*?)(\})/", "\\1<sriramasharmaa>" . "}", $this->rtfCont);
        $this->rtfCont = str_replace("<sriramasharmaa>", $v, $this->rtfCont);
        echo $this->rtfCont;
        exit;
    }

    function parseOLEObject()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\object[\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        #echo $header;exit;
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);

        return($header);
    }

    function removeSection($secname)
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\\" . $secname . "[\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        #echo $header;exit;
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);

        return($header);
    }

    function parsePictureObject()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\pict[\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        //echo $header;exit;
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        if (preg_match("/\\\pict[\w\W\s]*\}/m", $this->rtfCont_b))
            $this->parsePictureObject();

        return($header);
    }

    function parseCustProp()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\userprops [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        #echo $header;exit;
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);

        return($header);
    }

    /* --------------------- header parsing functions------------------------------------------ */

    function parseHeader()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\header [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        #echo $header;exit;
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);

        return($header);
    }

    function parseHeaderl()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\headerl [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);

        return($header);
    }

    function parseHeaderr()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\headerr [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    function parseHeaderf()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\headerf [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    /* --------------------- header parsing functions------------------------------------------ */

    function parseFooter()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\footer [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    function parseFooterl()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\footerl [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    function parseFooterr()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\footerr [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    function parseFooterf()
    {
        $cont = $this->rtfCont_b;
        preg_match("/\\\footerf [\w\W\s]*\}/m", $cont, $match);
        $m = $match[0];
        $open = 1;
        $close = 0;
        $header = '';
        for ($i = 0; $i < strlen($m); $i++) {
            if ($m[$i] == '{') {
                $open++;
            }
            if ($m[$i] == '}') {
                $close++;
            }
            if ($open == $close) {
                break;
            }
            $header.=$m[$i];
        }
        $this->rtfCont_b = str_replace($header, "", $this->rtfCont_b);
        return($header);
    }

    function writeDoc()
    {
        @fwrite($this->fp, $this->rtfCont);
    }

    function closefp()
    {
        if ($this->fp)
            @fclose($this->fp);
    }

}

//End of the class RTFParser
#$obj = new RTFParser();
#$obj->ReadFile();
#$obj->parseBookMark();
#$obj->cacheBookMark();
#$obj->display();
?>
