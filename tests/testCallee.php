#!env php
<?php
declare (strict_types = 1);

require __DIR__ . '/../vendor/autoload.php';

use \L\Debug\Utility as Debug;

class A
{
    public $g;

    public function __construct()
    {
        echo json_encode(
            Debug::dumpVariable(Debug::getCaller()),
            JSON_PRETTY_PRINT
        ), PHP_EOL;
    }

    public function test()
    {
        echo json_encode(
            Debug::dumpVariable(Debug::getCaller()),
            JSON_PRETTY_PRINT
        ), PHP_EOL;

        self::staticTest();
    }

    public static function staticTest()
    {
        echo json_encode(
            Debug::dumpVariable(Debug::getCaller()),
            JSON_PRETTY_PRINT
        ), PHP_EOL;

        (function($a) {

            echo json_encode(
                Debug::dumpVariable(Debug::getCaller()),
                JSON_PRETTY_PRINT
            ), PHP_EOL;


            echo json_encode(
                Debug::dumpVariable(Debug::getCallStack()),
                JSON_PRETTY_PRINT
            ), PHP_EOL;

        })(321);

    }
}

echo json_encode(
    Debug::dumpVariable(Debug::getCaller()),
    JSON_PRETTY_PRINT
), PHP_EOL;

function test1(int $x, A $a) {

    echo json_encode(
        Debug::dumpVariable(Debug::getCaller()),
        JSON_PRETTY_PRINT
    ), PHP_EOL;

    $a->test();
}

test1(123, new A());
