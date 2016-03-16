<?php
use League\CommonMark\CommonMarkConverter;
$converter = new CommonMarkConverter();

echo $converter->convertToHtml($input);

//$allowedTags = '<i><b><em><strong>';
//$t = strip_tags($t, $allowedTags);
