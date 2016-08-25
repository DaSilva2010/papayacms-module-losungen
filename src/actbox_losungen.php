<?php
/**
* Losungen Box
*
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, version 2
*
* You can redistribute and/or modify this script under the terms of the GNU General Public
* License (GPL) version 2, provided that the copyright and license notes, including these
* lines, remain unmodified. papaya is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
*
* @author Jan Bšrner
* @version 0.1
* @package module_losungen
*/

/**
* page modules inherit from the base_actionbox super class
* This does not include any database access, because not any module needs it.
*/
require_once(PAPAYA_INCLUDE_PATH.'system/base_actionbox.php');

/**
* Losungen Module
* Display a bibletext
*
* @author Jan Bšrner
* @package module_losungen
*/
class actionbox_losungen extends base_actionbox {

  /**
  * The edit fields definition is used to create a dialog in the backend,
  * The input will go into the "data" property of this object
  * You can group the fields using subtitles.
  * @var array $editFields
  */
//  var $editFields = array(
//    'title' => array(
//      'Title', 'isNoHTML', TRUE, 'input', 500, '', 'Losung'
//    )
//  );

  var $losung = array();

  /**
  * The getParsedData() method is called by the page controller to get the content for a whole page.
  * The return value has to be an empty string or wellformed xml (it does not need an root tag).
  * It will be put into the <topic> tag of the page xml output.
  *
  * @param array $params Parameters provided by the output filter
  * @access public
  * @return string
  */
  function getParsedData() {
    /* setDefaultData() initializes the default data for undefined data fields from the edit fields definition */
    $this->setDefaultData();
    /* escape special chars in the title and add some xml to the result */
//    $result = sprintf(
//      '<title>%s</title>'.LF,
//      papaya_strings::escapeHTMLChars($this->data['title'])
//    );
    if (empty($this->losung)) {
      $this->loadDatafile();
    }
    $result .= $this->getLosungXML();
    return $result;
  }

  /**
  * The getParsedTeaser method is called by the page controller to get content for a teaser (in a category or box)
  * This implementation returns an empty string, so the page will be hidden in teaser lists
  *
  * @access public
  * @return string
  */
  function getParsedTeaser() {
    return '';
  }

  /**
   * return the actual Losung as XML
   * @return string
   */
  function getLosungXML() {
    $result = '';
    $result .= '<losungen>'.LF;
    if (!empty($this->losung)) {
      $result .= sprintf('<losung date="%s">'.LF, date('Y-m-d'));
      $result .= sprintf('<bibletext intro="%s" source="%s" href="%s">%s</bibletext>'.LF,
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[0])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[2])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[3])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[1]))
      );
      $result .= sprintf('<teachingtext intro="%s" source="%s" href="%s">%s</teachingtext>'.LF,
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[4])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[6])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[7])),
        $this->getXHTMLString(papaya_strings::cleanInputString($this->losung[5]))
      );
      $result .= '</losung>'.LF;
    } else {
      $result .= 'empty';
    }
    $result .= '</losungen>'.LF;
    return $result;
  }
  /**
   * Reads in the datafile
   * inspired by the solution of combib
   * @link http://www.combib.de/losungphp/index.html
   * @return void
   */
  function loadDatafile() {
    // choose right datafile
    $datafile = dirname(__FILE__).'/losungphp'.date("Y").'.dat';

    // read in
    if (file_exists($datafile)) {
      $file = @fopen($datafile,"rb");
      if ($file) {
        $day = date("z") + 1;
        fseek ($file, ($day * 12) - 12);
        $meta = fread($file, 12);
        $position = intval(substr($meta, 0, 6)) -1;
        $length = intval(substr($meta, 6, 6));
        fseek ($file, $position);
        $text = fread($file, $length);
        $this->losung = explode("§", $text);
        fclose($file);
      }
    }
  }
}
?>
