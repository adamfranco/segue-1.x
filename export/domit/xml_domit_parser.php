<?php
//*******************************************************************
//DOMIT! is a non-validating, but lightweight and fast DOM parser for PHP
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//Version 0.6
//copyright 2004 Engage Interactive
//http://www.engageinteractive.com/domit/
//All rights reserved
//*******************************************************************
//Licensed under the GNU General Public License (GPL)
//
//This program is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//*******************************************************************
//see GPL details at http://www.gnu.org/copyleft/gpl.html
//and also in file license.txt included with DOMIT! 
//*******************************************************************

define("DOMIT_ELEMENT_NODE", 1);
define("DOMIT_TEXT_NODE", 3);
define("DOMIT_CDATA_SECTION_NODE", 4);
define("DOMIT_DOCUMENT_NODE", 9);

$GLOBALS['uidFactory'] = new UIDGenerator();
	
class UIDGenerator {
	var $seed;
	var $counter = 0;
	
	function UIDGenerator() {
		$this->seed = "node" . rand();
	} //UIDGenerator
	
	function generateUID() {
		return ($this->seed . $this->counter++);
	} //generateUID
} //UIDGenerator


class DOMIT_Node {
	//Note: should never instantiate this class!
	var $nodeName = null;
	var $nodeValue = null;
	var $nodeType = null;
	var $parentNode = null;
	var $childNodes = null;
	var $firstChild = null;
	var $lastChild = null;
	var $previousSibling = null;
	var $nextSibling = null;
	var $attributes = null;
	var $ownerDocument = null;
	var $uid;
	
	function DOMIT_Node() {		
	    die("DOMIT_Node Error: this is an abstract class that should never be instantiated.\n" . "
		    Please ensure that you override the '_constructor' method in your derived classes.");
	} //DOMIT_Node
	
	function _constructor() {
		global  $uidFactory;
		$this->uid = $uidFactory->generateUID();
	} //_constructor

	function &insertBefore(&$newChild, &$refChild) {
		if (($refChild->nodeType == DOMIT_DOCUMENT_NODE) ||
			($refChild->parentNode->nodeType == DOMIT_DOCUMENT_NODE) || 
			($refChild->parentNode == null) ||
			($refChild->uid == $newChild->uid)) {
			return false;
		}
		
		//remove $newChild if it already exists
		$index = $this->getChildNodeIndex($this->childNodes, $newChild);
		if ($index != -1) {
			$this->removeChild($newChild);
		}
	
		//find index of $refChild in childNodes
		$index = $this->getChildNodeIndex($this->childNodes, $refChild);
				
		if ($index != -1) {
			//reset sibling chain
			if ($refChild->previousSibling != null) {			
				$refChild->previousSibling->nextSibling =& $newChild;
				$newChild->previousSibling =& $refChild->previousSibling;
			}
			else {
				$this->firstChild =& $newChild;
			}
			
			$newChild->parentNode =& $refChild->parentNode;
			$newChild->nextSibling =& $refChild;
			$refChild->previousSibling =& $newChild;
			
			//add node to childNodes
			$i = count($this->childNodes);
	
			while ($i >= 0) {		
				if ($i > $index) {
					$this->childNodes[$i] =& $this->childNodes[($i - 1)];
				}
				else if ($i == $index) {
					$this->childNodes[$i] =& $newChild;
				}
				$i--;
			}
		}
		else {
			$this->appendChild($newChild);
		}
		
		return $newChild;
	} //insertBefore
	
	function getChildNodeIndex(&$arr, &$child) {
		$index = -1;
		$total = count($arr);
		
		for ($i = 0; $i < $total; $i++) {
			if ($child->uid == $arr[$i]->uid) {
				$index = $i;
				break;
			}
		}
		
		return $index;
	} //getChildNodeIndex

	function &replaceChild(&$newChild, &$oldChild) {
		if ($this->hasChildNodes()) { 
			//remove $newChild if it already exists
			$index = $this->getChildNodeIndex($this->childNodes, $newChild);
			if ($index != -1) {
				$this->removeChild($newChild);
			}
		
			//find index of $this in childNodes
			$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
			
			if ($index != -1) {
				$newChild->ownerDocument =& $oldChild->ownerDocument;
				$newChild->parentNode =& $oldChild->parentNode;
				
				//reset sibling chain
				if ($oldChild->previousSibling == null) {
					unset($newChild->previousSibling);
					$newChild->previousSibling = null;
				}
				else {
					$oldChild->previousSibling->nextSibling =& $newChild;
					$newChild->previousSibling =& $oldChild->previousSibling;
				}
				
				if ($oldChild->nextSibling == null) {
					unset($newChild->nextSibling);
					$newChild->nextSibling = null;
				}
				else {
					$oldChild->nextSibling->previousSibling =& $newChild;
					$newChild->nextSibling =& $oldChild->nextSibling;
				}
	
				$this->childNodes[$index] =& $newChild;
				
				if ($index == 0) $this->firstChild =& $newChild;
				if ($index == (count($this->childNodes) - 1)) $this->lastChild =& $newChild;
				
				return $newChild;
			}
		}

		return false;
	} //replaceChild

	function &removeChild(&$oldChild) {
		if ($this->hasChildNodes()) { 
			//find index of $oldChild in childNodes
			$index = $this->getChildNodeIndex($this->childNodes, $oldChild);
				
			if ($index != -1) {
				//reset sibling chain
				if (($oldChild->previousSibling != null) && ($oldChild->nextSibling != null)) {
					$oldChild->previousSibling->nextSibling =& $oldChild->nextSibling;
					$oldChild->nextSibling->previousSibling =& $oldChild->previousSibling;			
				}
				else if (($oldChild->previousSibling != null) && ($oldChild->nextSibling == null)) {
					$this->lastChild =& $oldChild->previousSibling;
					unset($oldChild->previousSibling->nextSibling);
					$oldChild->previousSibling->nextSibling = null;
				}
				else if (($oldChild->previousSibling == null) && ($oldChild->nextSibling != null)) {
					unset($oldChild->nextSibling->previousSibling);
					$oldChild->nextSibling->previousSibling = null;			
					$this->firstChild =& $oldChild->nextSibling;
				}
				else if (($oldChild->previousSibling == null) && ($oldChild->nextSibling == null)) {
					unset($this->firstChild);
					$this->firstChild = null;					
					unset($this->lastChild);
					$this->lastChild = null;
				}
				
				$total = count($this->childNodes);

				//remove node from childNodes
				for ($i = 0; $i < $total; $i++) {
					if ($i == ($total - 1)) {
						array_splice($this->childNodes, $i, 1);
					}
					else if ($i >= $index) {
						$this->childNodes[$i] =& $this->childNodes[($i + 1)];
					}
				}

				return $oldChild;
			}
		}

		return false;
	} //removeChild

	function appendChild(&$child) {
		//this method overwritten for 
		//DOMIT_Document and DOMIT_Element
		return false;
	} //appendChild
	
	function hasChildNodes() {
		return (count($this->childNodes) > 0);
	} //hasChildNodes

	function &cloneNode($deep) {
		//must provide a createClone method for all subclasses of DOMIT_Node
		$clone =& $this->createClone();
		
		if ($deep) {
			//return clone of this node's children
			if ($this->hasChildNodes()) {
				$total = count($this->childNodes);
				
				for ($i = 0; $i < $total; $i++) {
					$clone->appendChild($this->childNodes[$i]->cloneNode($deep));
				}
			}
		}
		
		return $clone;
	} //cloneNode

	function getNamedElements(&$nodeList, $tagName) {
		if ($this->nodeType == DOMIT_ELEMENT_NODE) {
			if (($this->nodeName == $tagName) || ($tagName == "*")) {
				$nodeList->appendNode($this); 
			}
			
			if ($this->hasChildNodes()) {
				$total = count($this->childNodes);
				
				for ($i = 0; $i < $total; $i++) {
					$this->childNodes[$i]->getNamedElements($nodeList, $tagName);
				}
			}
		}
	} //getNamedElements
	
	function setOwnerDocument(&$rootNode) {
		if ($rootNode->ownerDocument == null) {
			unset($this->ownerDocument);
			$this->ownerDocument = null;
		}
		else {
			$this->ownerDocument =& $rootNode->ownerDocument;
		}
		
		if ($this->hasChildNodes()) {
			$total = count($this->childNodes);
			
			for ($i = 0; $i < $total; $i++) {
				$this->childNodes[$i]->setOwnerDocument($rootNode);
			}
		}
	} //setOwnerDocument
	
	function &nvl(&$value,$default) {
		  if (is_null($value)) return $default;
		  return $value;
	} //nvl	
	
	function &selectNodes($pattern) {
		require_once("xml_domit_xpath.php");
		
		$xpParser =& new DOMIT_XPath();
		
		return $xpParser->parsePattern($this, $pattern);		
	} //selectNodes	
	
	function &getElementsByPath($pattern, $nodeIndex = 0) {
		require_once("xml_domit_getelementsbypath.php");
	
		$gebp = new DOMIT_GetElementsByPath();
		$myResponse =& $gebp->parsePattern($this, $pattern, $nodeIndex);

		return $myResponse;
	} //getElementsByPath	
	
	function getText() {
		return $this->nodeValue;
	} //getText
	
	function getTypedNodes(&$nodeList, $type) {
		//overridden by DOMIT_Document and DOMIT_Element
	} //getTypedNodes
	
	function getValuedNodes(&$nodeList, $value) {
		//overridden by DOMIT_Document and DOMIT_Element
	} //getValuedNodes
	
	function toNormalizedString() {
		//require this file for generating a normalized (readable) xml string representation
		require_once("xml_domit_utilities.php");		
		return DOMIT_Utilities::toNormalizedString($this);
	} //toNormalizedString
} //DOMIT_Node



class DOMIT_Document extends DOMIT_Node {
	var $xmlDeclaration;
	var $doctype;
	var $documentElement;
	var $parser;
	
	function DOMIT_Document() {
		$this->_constructor();
		$this->xmlDeclaration = null;
		$this->doctype = null;
		$this->documentElement = null;
		$this->nodeType = DOMIT_DOCUMENT_NODE;
		$this->nodeName = "#document";
		$this->ownerDocument =& $this;
		$this->parser = "";
	} //DOMIT_Document	
	
	function &appendChild(&$node) {
		//note: will overwrite documentElement if it already exists!
		$this->documentElement =& $node;
		$this->childNodes[0] =& $node;
		$node->parentNode =& $this;
		$node->ownerDocument =& $this;
		
		//clear these references just in case 
		//they are left over from prior operations
		unset($node->nextSibling);
		$node->nextSibling = null;
		unset($node->previousSibling);
		$node->previousSibling = null;
		
		$node->setOwnerDocument($this);
		
		return $node;
	} //appendChild
	
	function createElement($name) {
		$node =& new DOMIT_Element($name);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createElement
	
	function createTextNode($text) {
		$node =& new DOMIT_TextNode($text);
		$node->ownerDocument =& $this;
	
		return $node;
	} //createTextNode
	
	function createCDATASection($text) {
		$node =& new DOMIT_CDATASection($text);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createCDATASection
	
	function &getElementsByTagName($tagName) {
		require_once("xml_domit_nodemaps.php");
		$nodeList =& new DOMIT_NodeList();
		
		if ($this->documentElement != null) {
			$this->documentElement->getNamedElements($nodeList, $tagName);
		}
		
		return $nodeList;
	} //getElementsByTagName
	
	function &getNodesByNodeType($type, &$contextNode) {
		require_once("xml_domit_nodemaps.php");
		$nodeList =& new DOMIT_NodeList();
		
		if ($type == DOMIT_DOCUMENT_NODE) {
			$nodeList->appendNode($this); 
		}
		else if ($contextNode->nodeType == DOMIT_ELEMENT_NODE) {
			$contextNode->getTypedNodes($nodeList, $type);
		}
		else if ($contextNode->uid == $this->uid) {
			if ($this->documentElement != null) {
				if ($type == DOMIT_ELEMENT_NODE) {
					$nodeList->appendNode($this->documentElement); 
				}
					
				$this->documentElement->getTypedNodes($nodeList, $type);
			}
		}
		
		return $nodeList;
	} //getNodesByNodeType	

	function &getNodesByNodeValue($value, &$contextNode) {
		require_once("xml_domit_nodemaps.php");
		$nodeList =& new DOMIT_NodeList();
		
		 if ($contextNode->uid == $this->uid) {
			 if ($this->nodeValue == $value) {
				 $nodeList->appendNode($this);
			 }
		 }
		
		if ($this->documentElement != null) {
			$this->documentElement->getValuedNodes($nodeList, $value);
		}
		
		return $nodeList;
	} //getNodesByNodeValue
	
	function parseXML($xmlText, $useSAXY = true, $preserveCDATA = true) {
		require_once("xml_domit_utilities.php");
		
		if (DOMIT_Utilities::validateXML($xmlText)) {
			$domParser =& new DOMIT_Parser();
			
			if ($useSAXY || (!function_exists("xml_parser_create"))) {
				//use SAXY parser to populate xml tree
				$this->parser = "SAXY";
				return $domParser->parseSAXY($this, $xmlText, $preserveCDATA);
			}
			else {
				//use expat parser to populate xml tree
				$this->parser = "EXPAT";
				return $domParser->parse($this, $xmlText);
			}
		}
		else {
			return false;
		}
	} //parseXML
	
	function loadXML($fileName, $useSAXY = true, $preserveCDATA = true) {
		$xmlText = $this->getTextFromFile($fileName);
		return $this->parseXML($xmlText, $useSAXY, $preserveCDATA);
	} //loadXML
	
	function getTextFromFile($filename) {
		if (function_exists("file_get_contents")) {
			return file_get_contents($filename);
		}
		else {
			$fileHandle = fopen($filename, "r"); 
			$fileContents = "";
			
			if($fileHandle){ 
				while(!feof($fileHandle)) { 
					$fileContents = $fileContents . fread($fileHandle, 1024); 
				} 
				
				fclose($fileHandle);
				
				return $fileContents;
			} 
		}
		
		return "";
	} //getTextFromFile
	
	function saveXML($fileName) {
		return $this->saveTextToFile($fileName, $this->toString());
	} //saveXML

	function saveTextToFile($fileName, $text) {
		if (function_exists("file_put_contents")) {
			file_put_contents($filename, $text);
		}
		else {
			$fileHandle = fopen($fileName, "w");
			fwrite($fileHandle, $text);	
			fclose($fileHandle);
		}
		
		return (file_exists($fileName) && is_writable($fileName));
	} //saveTextToFile
	
	function &createClone() {
		$clone =& new DOMIT_Document();	
		$clone->xmlDeclaration = $this->xmlDeclaration;
		$clone->doctype = $this->doctype;
		
		return $clone;
	} //createClone
	
	function parsedBy() {
		return $this->parser;
	} //parsedBy
	
	function getText() {
		if ($this->documentElement != null) {
			$root =& $this->documentElement; 
			return $root->getText();
		}
		else {
			return "";
		}
	} //getText
		
	function toString() {
		$result = "";
		$result .= $this->nvl($this->xmlDeclaration,"");
		$result .= $this->nvl($this->doctype, "");
		
		if ($this->documentElement != null) {
			$result .= $this->nvl($this->documentElement->toString(), "");
		}

		return $result;
	} //toString
} //DOMIT_Document



class DOMIT_Element extends DOMIT_Node {
	
	function DOMIT_Element($name) {
		$this->_constructor();		
		$this->nodeType = DOMIT_ELEMENT_NODE;
		$this->nodeName = $name;
		$this->attributes = array();
		$this->childNodes = array();
	} //DOMIT_Element
	
	function &appendChild(&$child) {
		if (!($this->hasChildNodes())) {
			$this->childNodes[0] =& $child;
			$this->firstChild =& $child;
		}
		else {
			//remove $child if it already exists
			$index = $this->getChildNodeIndex($this->childNodes, $child);
			
			if ($index != -1) {
				$this->removeChild($child);
			}
			
			//append child
			$numNodes = count($this->childNodes);
			$prevSibling =& $this->childNodes[($numNodes - 1)];
			
			$this->childNodes[$numNodes] =& $child; 
			
			//set next and previous relationships
			$child->previousSibling =& $prevSibling;
			$prevSibling->nextSibling =& $child;
		}

		$this->lastChild =& $child;
		$child->parentNode =& $this;
		
		unset($child->nextSibling);
		$child->nextSibling = null;
		
		$child->setOwnerDocument($this);
		
		return $child;
	} //appendChild
	
	function &createClone() {
		$clone =& new DOMIT_Element($this->nodeName);
		if ($this->attributes != null) $clone->attributes = $this->attributes;
		
		return $clone;
	} //createClone
	
	function getText() {
		$text = "";
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				$text .= $child->getText();
			}
		}
		
		return $text;
	} //getText
	
	function &getElementsByTagName($tagName) {
		require_once("xml_domit_nodemaps.php");
		$nodeList =& new DOMIT_NodeList();		
		$this->getNamedElements($nodeList, $tagName);
		
		return $nodeList;
	} //getElementsByTagName
	
	function getTypedNodes(&$nodeList, $type) {
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				
				if ($child->nodeType == $type) {
					$nodeList->appendNode($child);
				}
				
				$child->getTypedNodes($nodeList, $type);
			}
		}
	} //getTypedNodes
	
	function getValuedNodes(&$nodeList, $value) {
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				
				if ($child->nodeValue == $value) {
					$nodeList->appendNode($child);
				}
				
				$child->getValuedNodes($nodeList, $value);
			}
		}
	} //getValuedNodes
	
	function getAttribute($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
		else {
			return "";
		}
	} //getAttribute	
	
	function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	} //setAttribute	
	
	function removeAttribute($name) {
		if (isset($this->attributes[$name])) {
			unset($this->attributes[$name]);
		}
	} //removeAttribute
	
	function hasAttribute($name) {
		if (isset($this->attributes[$name])) {
			return true;
		}
		else {
			return false;
		}
	} //hasAttribute

	function toString() {
		$returnString = "<" . $this->nodeName;
		
		//get attributes text
		if (count($this->attributes) != 0) {
			foreach ($this->attributes as $key => $value) {
				$returnString .= " $key=\"$value\"";
			}
		}
		
		$returnString .= ">";
		
		//get children
		$myNodes =& $this->childNodes;
		$total = count($myNodes);
		
		if ($total != 0) {
			for ($i = 0; $i < $total; $i++) {
				$child =& $myNodes[$i];
				$returnString .= $child->toString();
			}
		}

		$returnString .= "</" . $this->nodeName . ">";
		
		return $returnString;
	} //toString
} //DOMIT_Element


class DOMIT_TextNode extends DOMIT_Node {
	function DOMIT_TextNode($nodeValue) {
		$this->_constructor();	
		$this->nodeType = DOMIT_TEXT_NODE;
		$this->nodeName = "#text";
		$this->nodeValue = $nodeValue;
	} //DOMIT_TextNode	
	
	function &createClone() {
		$clone =& new DOMIT_TextNode($this->nodeValue);		
		return $clone;
	} //createClone
	
	function toString() {
		return $this->nodeValue;
	} //toString
} //DOMIT_TextNode


class DOMIT_CDATASection extends DOMIT_Node {
	function DOMIT_CDATASection($nodeValue) {
		$this->_constructor();		
		$this->nodeType = DOMIT_CDATA_SECTION_NODE;
		$this->nodeName = "#cdata-section";
		$this->nodeValue = $nodeValue;
	} //DOMIT_CDATASection

	function &createClone() {
		$clone =& new DOMIT_CDATASection($this->nodeValue);		
		return $clone;
	} //createClone
	
	function toString() {
		return ("<![CDATA[" . $this->nodeValue . "]]>");
	} //toString
} //DOMIT_CDATASection


class DOMIT_Parser {
	var $xmlDoc = null;
	var $currentNode = null;
	var $lastChild = null;
	
	function parse (& $myXMLDoc, $xmlText, $preserveCDATA = true) {
		$this->xmlDoc =& $myXMLDoc;
		
		//create instance of expat parser (should be included in php distro)
		$parser = xml_parser_create();
		
		//set handlers for SAX events
		xml_set_element_handler($parser, array(&$this, "startElement"), array(&$this, "endElement")); 
		xml_set_character_data_handler($parser, array(&$this, "dataElement")); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 	
		
		//parse out whitespace -  (XML_OPTION_SKIP_WHITE = 1 does not 
		//seem to work consistently across versions of PHP and Expat
		$xmlText = eregi_replace(">" . "[[:space:]]+" . "<" , "><", $xmlText);
		
		$success = xml_parse($parser, $xmlText);
		xml_parser_free($parser); 
		
		return $success;
	} //parse
	
	function parseSAXY(& $myXMLDoc, $xmlText, $preserveCDATA) {
		require_once("xml_saxy_parser.php");
		
		$this->xmlDoc =& $myXMLDoc;
		
		//create instance of SAXY parser 
		$parser =& new SAXY_Parser();
		
		$parser->xml_set_element_handler(array(&$this, "startElement"), array(&$this, "endElement"));
		$parser->xml_set_character_data_handler(array(&$this, "dataElement"));
		
		if ($preserveCDATA) {
			$parser->xml_set_cdata_section_handler(array(&$this, "cdataElement"));
		}
		
		return $parser->parse($xmlText);
	} //parseSAXY

	function startElement($parser, $name, $attrs) {
		$currentNode =& $this->xmlDoc->createElement($name);
		
		if ($this->lastChild == null) {
			$this->xmlDoc->documentElement =& $currentNode; 
		}
		else {
			$this->lastChild->appendChild($currentNode);
		}
		
		$numAttrs = count($attrs);
		
		if (($numAttrs > 0) && is_array($attrs)) {
			reset ($attrs);
			
			while (list($key, $value) = each ($attrs)) {
				$currentNode->attributes[$key] = $value;
			}
		}		
		
		$this->lastChild =& $currentNode;
	} //startElement	
	
	function endElement($parser, $name) {
		$this->lastChild =& $this->lastChild->parentNode;
	} //endElement	 
	
	function dataElement($parser, $data) {
		$currentNode =& $this->xmlDoc->createTextNode($data);

		$this->lastChild->appendChild($currentNode);
	} //dataElement	
	
	function cdataElement($parser, $data) {
		$currentNode =& $this->xmlDoc->createCDATASection($data);

		$this->lastChild->appendChild($currentNode);
	} //dataElement	
} //DOMIT_Parser

?>