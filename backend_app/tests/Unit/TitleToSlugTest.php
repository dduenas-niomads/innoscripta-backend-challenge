<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class TitleToSlugTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_convert_title_to_slug_function(): void
    {
        // Set up variables
        $divider = '_';
        $text = 'This article about Perú is amazing';

        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        // Just in case...
        if (empty($text)) {
            $text = 'no_text';
        }
        
        // Compare values with $this->assertSame($expected, $actual);
        $this->assertSame('this_article_about_peru_is_amazing', $text);
    }
}
