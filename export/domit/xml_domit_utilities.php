<?php
//*******************************************************************
//DOMIT_Utilities is a set of XML utilities to be used with DOMIT!
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//Version 0.2
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

class DOMIT_Utilities {

	function DOMIT_Utilities() {		
	    die("DOMIT_Utilities Error: this is a static class that should never be instantiated.\n" . 
		    "Please use the following syntax to access methods of this class:\n" .
		    "DOMIT_Utilities::methodName(parameters)");
	} //DOMIT_Utilities
	

	function toNormalizedString (&$node) {
		$node_level = 0;
		
		return DOMIT_Utilities::getNormalizedString($node, $node_level);
	} //toNormalizedString	
	
		
	function getNormalizedString(&$node, $node_level) {
		$response = "";
		
		//$node is a DOMIT_Document
		if ($node->nodeType == DOMIT_DOCUMENT_NODE) { 
			$response .= $node->xmlDeclaration . "\n" .
							$node->doctype . "\n";
							
			$node =& $node->documentElement;
		}

		switch ($node->nodeType)  {
			case DOMIT_ELEMENT_NODE; //element
				$response .= "<" . $node->nodeName;
				
				//get attributes text
				if (count($node->attributes) != 0) {
					foreach ($node->attributes as $key => $value) {
						$response .= " $key=\"$value\"";
					}
				}
				
				$node_level++;
				
				//if node is childless
				if (($node->childNodes == null) || (count($node->childNodes) == 0)) {
					$response .= " />";
				}
				else {
					$response .= ">";						
										
					$response .= DOMIT_Utilities::getIndentation($node_level);
					
					//get children
					$myNodes =& $node->childNodes;
					$total = count($myNodes);
	
					if ($total != 0) {
						for ($i = 0; $i < $total; $i++) {
							$child =& $myNodes[$i];
							$response .= DOMIT_Utilities::getNormalizedString($child, $node_level);
						}
					}
			
					$response .= "</" . $node->nodeName . ">";
				}

				$node_level--;
	
				if ($node->nextSibling == null) {
					$node_level--;
				}
								
				$response .= DOMIT_Utilities::getIndentation($node_level) ;
				
				break;
			
			case DOMIT_TEXT_NODE: //text node
				if ($node->nextSibling == null) {
					$node_level--;
				}
						
				$response .= $node->nodeValue . DOMIT_Utilities::getIndentation($node_level);
				break;
			
			case DOMIT_CDATA_SECTION_NODE: //CData node
				if ($node->nextSibling == null) {
					$node_level--;
				}
				
				$response .= ("<![CDATA[" . $node->nodeValue . "]]>" . 
									DOMIT_Utilities::getIndentation($node_level));
				break;
		} 
		
		return $response;
	} //getNormalizedString
	
	
	function getIndentation($node_level) {
		$INDENT_LEN = "    ";
		$indentation = "\n";

		for ($i = 0; $i < $node_level; $i++) {
			$indentation .= $INDENT_LEN;
		}
		
		return $indentation;
	} //getIndentation
	
	
	function validateXML($xmlText) {
		//this does only rudimentary validation
		//at this point in time
		$isValid = true;
		
		if (is_string($xmlText)) {		
			$text = trim($xmlText);
			
			switch ($text) {
				case "":
					$isValid = false;
					break;
			}
		}
		else {
			$isValid = false;
		}
		
		return $isValid;
	} //validateXML
	
	
	function printNode(&$node) {
		return " id: " . $node->uid . "\n" .
				"nodeName: " . $node->nodeName . "\n" .
				"nodeType: " . $node->nodeType . "\n" .
				"nodeValue: " . $node->nodeValue;
	} //printNode
	
	
	function printNodeAndSiblings(&$node) {
		$ns = "";
		
		if ($node != null) {
			$ns = "\n\n************************\nNODE: \n" . DOMIT_Utilities::printNode($node);
		
			if ($node->previousSibling != null) {
				$ns .= "\n\nPREVIOUS SIBLING:\n" . DOMIT_Utilities::printNode($node->previousSibling);
			}
			else {
				$ns .= "\n\nPREVIOUS SIBLING:\nnull"; 
			}
			
			if ($node->nextSibling != null) {
				$ns .= "\n\nNEXT SIBLING:\n" . DOMIT_Utilities::printNode($node->nextSibling);
			}
			else {
				$ns .= "\n\nNEXT SIBLING:\nnull"; 
			}
		}
		
		$ns .= "\n************************\n";
		
		return $ns;
	} //printNodeAndSiblings
} //DOMIT_Utilities
?>