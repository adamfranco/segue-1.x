<? $title = "Editors/Permissions"; ?>

<p>The ability to assign editors and permissions to your site is one of the most powerful features of Segue.

<p>Any editors you add will see your site under their sites list. This, however, does not mean that they can edit your site, at least not yet. As site owner, you can specify which editors can do what to your site, on any level.

<p>To add an editor to your site, click the "add editor" link. You will be presented with a dialog that allows you to search for <? echo $cfg[inst_name]; ?> users and add them as editors.

<p>Once they are editors you can specify what they can do to your site. Permissions are organized into the following categories:

<ul>
<li> <b>add</b> - add permissions allow editors to add content to your site under the part you assign them permission. For example, if you assign an editor add permission to a section, they can add pages and content to those pages below the section.
<li> <b>edit</b> - edit permissions allow editors to edit any part below the one you specify as well as edit the settings for the part you specify.
<li> <b>delete</b> - delete permissions allow editors to delete parts below the one you specify.
<li> <b>view</b> - view permissions allow the specified editor (or special editors: everyone and institution) to view the selected part of your site when browsing.
</ul>