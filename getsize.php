<? /* $Id$ */
// this script is included in:
// query.php

$width = 0;
$height = 0;
function getSize($pic) {
  global $width, $height;
  $size = GetImageSize($pic);
  $width = $size[0];
  $height = $size[1];
}

?>
