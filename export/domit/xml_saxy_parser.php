<?php
//*******************************************************************
//SAXY version 0.4
//a non-validating, but lightweight and fast SAX parser for PHP
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//copyright 2003 Engage Interactive
//http://www.engageinteractive.com/saxy/
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
//and also in file license.txt included with SAXY 
//*******************************************************************

	define("SAXY_STATE_NONE", 0);
	define("SAXY_STATE_PARSING", 1);
	define("SAXY_SEARCH_CDATA", "![CDATA[");
	define("SAXY_SEARCH_NOTATION", "!NOTATION");
	define("SAXY_SEARCH_DOCTYPE", "!DOCTYPE");
	define("SAXY_STATE_ATTR_NONE", 0);
	define("SAXY_STATE_ATTR_KEY", 1);
	define("SAXY_STATE_ATTR_VALUE", 2);
	define("SAXY_CDATA_LEN", 8);
	
	class SAXY_Parser {
		var $state;
		var $charContainer;
		var $startElementHandler;
		var $endElementHandler;
		var $characterDataHandler;
		var $cDataSectionHandler = null;
			
		function SAXY_Parser() {
			$this->charContainer = "";
			$this->state = SAXY_STATE_NONE;
		} //SAXY_Parser
		
		function xml_set_element_handler($startHandler, $endHandler) {
			$this->startElementHandler = $startHandler;
			$this->endElementHandler = $endHandler;
		} //xml_set_element_handler
		
		function xml_set_character_data_handler($handler) {
			$this->characterDataHandler =& $handler;
		} //xml_set_character_data_handler
		
		function xml_set_cdata_section_handler($handler) {
			$this->cDataSectionHandler =& $handler;
		} //xml_set_cdata_section_handler
		
		function preprocessXML($xmlText) {
			//strip prolog
			$xmlText = trim($xmlText);
			$startChar = -1;
			$total = strlen($xmlText);
			
			for ($i = 0; $i < $total; $i++) {
				$currentChar = $xmlText{$i};
				$nextChar = $xmlText{($i + 1)};
				
				if (($currentChar == "<") && ($nextChar != "?")  && ($nextChar != "!")) {
					$startChar  = $i; 
					break;
				}
			}
			
			return (substr($xmlText, $startChar));
		} //preprocessXML
		
		function parse ($xmlText) {
			$xmlText = $this->preprocessXML($xmlText);			
			$total = strlen($xmlText);
	
			for ($i = 0; $i < $total; $i++) {
				$currentChar = $xmlText{$i};
				
				switch ($this->state) {
					case SAXY_STATE_NONE:
				
						switch ($currentChar) {
							case "<":
								$this->state = SAXY_STATE_PARSING;
								break;
						}	
						
						break;
						
					case SAXY_STATE_PARSING:
					
						switch ($currentChar) {
							case "<":
								if (substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) {
									$this->charContainer .= $currentChar;
								}
								else {
									$this->parseBetweenTags($this->charContainer);
									$this->charContainer = "";
								}
						
								break;
								
							case ">":
								if ((substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA)   &&
									($this->getCharFromEnd($this->charContainer, 0) != "]") &&
									($this->getCharFromEnd($this->charContainer, 1) != "]")) {
									$this->charContainer .= $currentChar;
								}
								else {
									$this->parseTag($this->charContainer);
									$this->charContainer = "";
								}
								break;
								
							default:
								$this->charContainer .= $currentChar;
						}
						
						break;
				}
			}	

			return true;
		} //parse

		function getCharFromEnd($text, $index) {
			$len = strlen($text);
			$char = $text{($len - 1 - $index)};
			
			return $char;
		} //getCharFromEnd
		
		function parseTag($tagText) {
			$tagText = trim($tagText);
			$firstChar = $tagText{0};
			$myAttributes = "";
		
			switch ($firstChar) {
				case "/":
					$tagName = substr($tagText, 1);				
					$this->fireEndElementEvent($tagName);
					break;
				
				case "!":
					$upperCaseTagText = strtoupper($tagText);
				
					if (strpos($upperCaseTagText, SAXY_SEARCH_CDATA) !== false) { //CDATA Section
						$total = strlen($tagText);
						$openBraceCount = 0;
						$textNodeText = "";
						
						for ($i = 0; $i < $total; $i++) {
							$currentChar = $tagText{$i};
							
							if ($currentChar == "]") {
								break;
							}
							else if ($openBraceCount > 1) {
								$textNodeText .= $currentChar;
							}
							else if ($currentChar == "[") {
								$openBraceCount ++;
							}
						}
						
						if ($this->cDataSectionHandler == null) {
							$this->fireCharacterDataEvent($textNodeText);
						}
						else {
							$this->fireCDataSectionEvent($textNodeText);
						}
					}
					else if (strpos($upperCaseTagText, SAXY_SEARCH_NOTATION) !== false) { //NOTATION node, discard
						return;
					}
					else if (substr($tagText, 0, 2) == "--") { //COMMENT node, discard
						return;
					}
					
					break;
					
				case "?": 
					//Processing Instruction node, discard
					break;
					
				default:				
					if ((strpos($tagText, "\"") !== false) || (strpos($tagText, "'") !== false)) {
						$total = strlen($tagText);
						$tagName = "";
	
						for ($i = 0; $i < $total; $i++) {
							$currentChar = $tagText{$i};
							
							if ($currentChar == " ") {
								$myAttributes = $this->parseAttributes(substr($tagText, $i));
								break;
							}
							else {
								$tagName.= $currentChar;
							}
						}
	
						if (strrpos($tagText, "/") == (strlen($tagText) - 1)) { //check $tagText, but send $tagName
							$this->fireStartElementEvent($tagName, $myAttributes);
							$this->fireEndElementEvent($tagName);
						}
						else {
							$this->fireStartElementEvent($tagName, $myAttributes);
						}
					}
					else {
						if (strpos($tagText, "/") !== false) {
							$tagText = trim(substr($tagText, 0, (strrchr($tagText, "/") - 1)));
							$this->fireStartElementEvent($tagText, $myAttributes);
							$this->fireEndElementEvent($tagText);
						}
						else {
							$this->fireStartElementEvent($tagText, $myAttributes);
						}
					}					
			}
		} //parseTag		
		
		function parseAttributes($attrText) {
			$attrText = trim($attrText);	
			$attrArray = array();
			
			$total = strlen($attrText);
			$keyDump = "";
			$valueDump = "";
			$currentState = SAXY_STATE_ATTR_NONE;
			$quoteType = "";
			
			for ($i = 0; $i < $total; $i++) {								
				$currentChar = $attrText{$i};
				
				if ($currentState == SAXY_STATE_ATTR_NONE) {
					if (trim($currentChar != "")) {
						$currentState = SAXY_STATE_ATTR_KEY;
					}
				}
				
				switch ($currentChar) {
					case "\t":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							$valueDump .= $currentChar;
						}
						else {
							$currentChar = "";
						}
					case "\n":
						$currentChar = "";
						
					case "=";
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							$valueDump .= $currentChar;
						}
						else {
							$currentState = SAXY_STATE_ATTR_VALUE;
							$quoteType = "";
						}
						break;
						
					case "\"":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							if ($quoteType == "") {
								$quoteType = "\"";
							}
							else {
								if ($quoteType == $currentChar) {
									$attrArray[trim($keyDump)] = trim($valueDump);
									$keyDump = $valueDump = $quoteType = "";
									$currentState = SAXY_STATE_ATTR_NONE;
								}
								else {
									$valueDump .= $currentChar;
								}
							}
						}
						break;
						
					case "'":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							if ($quoteType == "") {
								$quoteType = "'";
							}
							else {
								if ($quoteType == $currentChar) {
									$attrArray[$keyDump] = $valueDump;
									$keyDump = $valueDump = $quoteType = "";
									$currentState = SAXY_STATE_ATTR_NONE;
								}
								else {
									$valueDump .= $currentChar;
								}
							}
						}
						break;
						
					default:
						if ($currentState == SAXY_STATE_ATTR_KEY) {
							$keyDump .= $currentChar;
						}
						else {
							$valueDump .= $currentChar;
						}
				}
			}

			return $attrArray;
		} //parseAttributes		
		
		function parseBetweenTags($betweenTagText) {
			if (trim($betweenTagText) != "") {
				$this->fireCharacterDataEvent($betweenTagText);
			}
		} //betweenTagText	

		function fireStartElementEvent($tagName, $attributes) {
			call_user_func($this->startElementHandler, $this, $tagName, $attributes);
		} //fireStartElementEvent
		
		
		function fireEndElementEvent($tagName) {
			call_user_func($this->endElementHandler, $this, $tagName);
		} //fireEndElementEvent
		
		function fireCharacterDataEvent($data) {
			call_user_func($this->characterDataHandler, $this, $data);
		} //fireCharacterDataEvent	
		
		function fireCDataSectionEvent($data) {
			call_user_func($this->cDataSectionHandler, $this, $data);
		} //fireCDataSectionEvent	
	
	} //SAXY_Parser
?>