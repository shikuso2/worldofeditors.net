<?php

function parse_color_tags($text)
{
    // Regular expression to match the custom color format
    $pattern = '/\|.{3}([0-9a-fA-F]{6})(.*?)(\|r|$)/i';
    
    // Replace the custom format with a span element with inline CSS color
    $parsed_text = preg_replace_callback($pattern, function($matches) {
        $color_code = $matches[1]; // The hex color code
        $text = $matches[2]; // The text to be colored
        // Return the text wrapped in a span with the corresponding color
        return '<span style="color: #' . $color_code . '">' . htmlspecialchars($text) . '</span>';
    }, $text);
    
    return $parsed_text;
}
