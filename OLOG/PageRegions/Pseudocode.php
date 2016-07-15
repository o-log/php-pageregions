<?php

namespace OLOG\PageRegions;

class Pseudocode
{
    const TAG_CALL_METHOD_START = '##@call_method';
    const TAG_CALL_METHOD_END = '##@call_method_end';

    protected static $callable_by_method_name_arr = array(
    );

    /**
     * Проверить текст блока на наличие псевдокода
     *
     * @param string $text
     * @return bool
     */
    public static function hasPseudocode($text)
    {
        return (
            (strpos($text, self::TAG_CALL_METHOD_START) !== false) &&
            (strpos($text, self::TAG_CALL_METHOD_END) !== false)
        );
    }

    /**
     * Заменить псевдокод в тексте блока на результат выполнения методов
     *
     * @param string $text
     * @return string
     */
    public static function parse($text)
    {
        $text = self::parseCallMethod($text);
        return $text;
    }

    /**
     * @param $text
     * @return mixed
     */
    protected static function parseCallMethod($text)
    {
        $tag_start = preg_quote(self::TAG_CALL_METHOD_START, '/');
        $tag_end = preg_quote(self::TAG_CALL_METHOD_END, '/');
        $regexp = '/' . $tag_start . '\s*(.*?)\s*' . $tag_end . '/s';

        if (!preg_match_all($regexp, $text, $matches)) {
            return $text;
        }

        foreach ($matches[1] as $key => $params) {
            $params_arr = explode("\n", $params);
            $params_arr = array_map('trim', $params_arr);

            $method_name = array_shift($params_arr);

            $ob_level = ob_get_level();

            try {
                $result = self::callMethod($method_name, $params_arr);
            } catch (\Exception $exception) {
                $ob_level_delta = ob_get_level() - $ob_level;
                for ($i = 0; $i < $ob_level_delta; $i++) {
                    ob_end_clean();
                }

                $result = '';

                error_log("Catched exception '(Catched) " . $exception->getMessage() . "'\n" . $exception->getTraceAsString());
            }

            $text = str_replace($matches[0][$key], $result, $text);
        }

        return $text;
    }

    /**
     * Вызвать метод
     *
     * @param string $method_name
     * @param array $params_arr
     * @return string
     */
    protected static function callMethod($method_name, $params_arr)
    {
        if (!array_key_exists($method_name, self::$callable_by_method_name_arr)) {
            return 'Неправильный метод: ' . $method_name;
        }

        $callable = self::$callable_by_method_name_arr[$method_name];

        $result = call_user_func_array($callable, $params_arr);
        return $result;
    }

    /**
     * Получить список методов
     *
     * @return array
     */
    public static function getMethodNamesArr()
    {
        return array_keys(self::$callable_by_method_name_arr);
    }
}
