<?php

/**
 * @package		My Blog
 * @copyright (C) 2008 by Slashes & Dots Sdn Bhd - All rights reserved!
 * @license http://www.azrul.com Copyrighted Commercial Software
 */
defined('_JEXEC') or die('Restricted access');

require_once( MY_COM_PATH . DS . 'task' . DS . 'base.php' );
require_once( MY_COM_PATH . DS . 'task' . DS . 'show.base.php' );
require_once( MY_COM_PATH . DS . 'libraries' . DS . 'tcpdf' . DS . 'tcpdf.php');
require_once( MY_COM_PATH . DS . 'libraries' . DS . 'tcpdf' . DS . 'config'. DS .'lang' . DS . 'eng.php');

class MyblogPdfTask extends MyblogBaseController {

    /**
     * PDF generate here
     */
    function display() {
        global $_MY_CONFIG;

        $template		= new AzrulJXTemplate();
        
        // get entry detail
        $db = & JFactory::getDBO();
        $show = JRequest::getVar('show', '', 'GET');
        $id = JRequest::getVar('id', '', 'GET');
        $this->uid = (!empty($show) ) ? $show : $id;
        $uid = $this->uid;

        // Get blog entry
        if (is_numeric($uid)) {
            $date = & JFactory::getDate();

            $query = "SELECT c.*,p." . $db->nameQuote('permalink') . ", '" . $date->toMySQL() . "' as " . $db->nameQuote('curr_time') .
                    ", r." . $db->nameQuote('rating_sum') . "/r." . $db->nameQuote('rating_count') . " as " . $db->nameQuote('rating') . ", r." . $db->nameQuote('rating_count') .
                    " FROM (" . $db->nameQuote('#__content') . " as c, " . $db->nameQuote('#__myblog_permalinks') . " as p) left outer join " . $db->nameQuote('#__content_rating') .
                    " as r on (r." . $db->nameQuote('content_id') . "=c." . $db->nameQuote('id') .
                    ") WHERE c." . $db->nameQuote('id') . "=p." . $db->nameQuote('contentid') . " and c." . $db->nameQuote('id') . "=" . $db->Quote($uid);
            $db->setQuery($query);
            $row = $db->loadObject();

            if (!$row) {
                $row = & JTable::getInstance('BlogContent', 'Myblog');
                $row->load($uid);
            }
        } else {
            $uid = stripslashes($uid);
            //$uid = urldecode($uid);
            $uid = $db->getEscaped($uid);

            $row = & JTable::getInstance('BlogContent', 'Myblog');
            $row->load($uid);
        }

        $row->text = '';

        if ($row->introtext && trim($row->introtext) != '') {
            $row->text .= $row->introtext;
        }

        // Add the rest of the fulltext
        if ($row->fulltext && trim($row->fulltext) != '') {
            // Process anchor for #readmore only when there is fulltext
            $row->text .= $row->fulltext;
        }
        $row->author = myUserGetName($row->created_by, $_MY_CONFIG->get('useFullName'));
        $row->categories = myCategoriesURLGet($row->id, true, '');
        $row->jcategory		= '<a href="' . JRoute::_('index.php?option=com_myblog&task=tag&jcategory=' . $row->catid ) . '">' . myGetJoomlaCategoryName( $row->catid ) . '</a>';
	$row->createdFormatted	= $date->toFormat( $_MY_CONFIG->get('dateFormat') );
	$row->created			= $date->toFormat();

        
        $avatar	= 'My' . ucfirst($_MY_CONFIG->get('avatar')) . 'Avatar';
        $avatar	= new $avatar($row->created_by);
        $row->avatar	= $avatar->get();
        
        // load PDF library here
        $pdf = new TCPDF();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator($row->author);
        $pdf->SetAuthor($row->author);
        $pdf->SetTitle($row->title);
        $pdf->SetKeywords($row->categories);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        //set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetFont('dejavusans', '', 11, '', true);

        // Add a page
        $pdf->AddPage();

        // Set some content to print
        $template->set('entry', $template->object_to_array($row));
        $template->set( 'categoryDisplay' , $_MY_CONFIG->get('categoryDisplay') );

        $html	= $template->fetch($this->_getTemplateName('pdf'));
        
        // Print text using writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

        $pdf->Output($row->title.'.pdf', 'I');
        unset($row->_table);
	unset($row->_key);
        // Set document type headers
        exit;
    }

}

