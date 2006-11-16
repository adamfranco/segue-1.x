HTML Editor Notes

Segue currently uses 2 HTML Editors:
1. HTMLArea
2. FCKeditor

The FCKeditor is the default editor.  New versions on this editor can be placed 
in the following directory with Segue:
htmleditor/FCKeditor

(newer versions of the FCKeditor should be tested before being used in production)


This editor has been integrated with Segue's Media Library.  In order to integrate 
the some versions of FCKEditor (versions 2.3.1 or earlier) with Segue, the 
following file needed to be changed:
FCKeditor/editor/dialog/common/fck_dialog_common.js

This file contains a function called OpenFileBrowser.  Within this function are
variables related to how the file browser is opened.  In order to make use of
Segue's Media Library scrollbars need to made visible.  Here is the change:

Change:
var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=no" ;
to:
var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes,scrollbars=yes" ;




