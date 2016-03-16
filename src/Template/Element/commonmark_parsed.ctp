<?php
use League\CommonMark\CommonMarkConverter;
$converter = new CommonMarkConverter();

// Prevent any HTML tags entered by the user from appearing
$input = strip_tags($input);

$html = $converter->convertToHtml($input);

// Strip out any tags created by CommonMark but not approved
$allowedTags = [
    '<p>', '<br>',
    '<i>', '<em>',
    '<b>', '<strong>',
    '<a>',
    '<ul>', '<ol>', '<li>',
    '<h1>', '<h2>', '<h3>', '<h4>', '<h5>', '<h6>',
    '<blockquote>'
];
$allowedTags = implode('', $allowedTags);
$html = strip_tags($html, $allowedTags);

echo $html;
