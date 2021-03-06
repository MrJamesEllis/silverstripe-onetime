<?php
namespace Codem\OneTime;

use SilverStripe\Core\Config\Configurable;

class PartialValue
{
    use Configurable;

    const FILTER_HIDE_MIDDLE = 'filter_hide_middle';

    private static $min_characters_replaced = 6;// always show this number of characters replaced, at a minimum
    private static $max_characters_exposed = 6;// always show this number of characters replaced, at a minimum
    private static $max_characters_replaced = 18;// always show this number of characters replaced, at a minimum
    private static $percent_cleared = 80;// number of characters replaced
    private static $replacement_character = "●";// replacement character to use

    /**
     * @param string $value
     * @param string $filter
     * @todo needs some work
     */
    public function get($value, $filter = '')
    {
        $replacement_character = $this->config()->get('replacement_character');
        $min = $this->config()->get('min_characters_replaced');
        $max = $this->config()->get('max_characters_replaced');
        $max_characters_exposed = $this->config()->get('max_characters_exposed');

        $percent_cleared = $this->config()->get('percent_cleared');
        $partial_value = "";
        $length = mb_strlen($value);

        switch ($filter) {
            case self::FILTER_HIDE_MIDDLE:
                // this rule shows the first 3 and last 6 characters
                $pattern = "/^(.{3})(.+)(.{6})$/";
                $result = preg_match($pattern, $value, $matches);
                if ($result == 1 && count($matches) == 4) {
                    $replacement_length = mb_strlen($matches[2]);
                    if ($replacement_length > $max) {
                        $replacement_length = $max;
                    } elseif ($replacement_length < $min) {
                        $replacement_length = $min;
                    }
                    $partial_value = $matches[1]
                        . str_repeat($replacement_character, $replacement_length)
                        . $matches[3];
                } else {
                    // mask the entire value
                    $partial_value = $this->replaceAllWith($replacement_character, $length);
                }
                break;
            default:
                // the default is to hide the last number of characters based on rules
                $length = mb_strlen($value);

                if ($length < $min) {
                    $partial_value = $this->replaceAllWith($replacement_character, $length);
                    return $partial_value;
                }

                $characters_cleared = ceil($length * ($percent_cleared / 100));
                if ($characters_cleared < $min) {
                    $characters_cleared = $min;
                }

                $replacement_length = $characters_cleared;
                if ($replacement_length > $max) {
                    $replacement_length = $max;
                }

                $characters_retained = $length - $characters_cleared;
                if ($characters_retained > $max_characters_exposed) {
                    $characters_retained = $max_characters_exposed;
                    $characters_cleared = $length - $characters_retained;
                }
                $partial_value = substr_replace($value, str_repeat($replacement_character, $replacement_length), $characters_retained);
                break;
        }

        if ($partial_value == $value) {
            // obfuscate all if somehow we have the value
            $partial_value = $this->replaceAllWith($replacement_character, $length);
        }
        return $partial_value;
    }

    public function replaceAllWith($replacement_character, $length)
    {
        return str_repeat($replacement_character, $length);
    }
}
