<?php
declare (strict_types=1);

namespace L\Debug;

use L\Core\Exception;

class Utility
{
    /**
     * Get the caller of current stack.
     *
     * @return array
     */
    public static function getCaller(): array
    {
        $stacks = debug_backtrace();

        if ($stacks[1] ?? false) {

            $cursor = 1;
            return self::_getStackLayer(
                $stacks,
                $cursor
            );
        }

        return [];
    }

    /**
     * Get the full caller of current stack.
     *
     * @return array
     */
    public static function getCallStack(): array
    {
        $phpStacks = debug_backtrace();

        $stacks = [];

        $cursor = 1;

        while ($phpStacks[$cursor] ?? false) {

            $stacks[] = self::_getStackLayer(
                $phpStacks,
                $cursor
            );
        }

        return $stacks;
    }

    protected static function _getStackLayer(
        array $phpStacks,
        int &$cursor
    ): array
    {
        $stack = $phpStacks[$cursor];

        $ret = [
            'file' => $stack['file'],
            'line' => $stack['line'],
            'callee' => [
                'name' => $stack['function'],
                'context' => '{global}',
                'type' => 'function'
            ],
            'arguments' => $stack['args'],
        ];

        if ($stack['function'] === '{closure}') {

            $ret['callee']['type'] = 'lambda';

            if (isset($stack['class'])) {

                $ret['callee']['context'] = $stack['class'];
            }
        }
        else {

            switch ($stack['type'] ?? '') {
            case '->':

                $ret['callee']['context'] = $stack['class'];
                $ret['callee']['type'] = 'method';
                break;

            case '::':

                $ret['callee']['context'] = $stack['class'];
                $ret['callee']['type'] = 'static-method';
                break;
            }
        }

        if ($phpStacks[++$cursor] ?? false) {

            $stack = $phpStacks[$cursor];

            if ($stack['type'] ?? false) {

                $ret['caller'] = [
                    'name' => $stack['function'],
                    'context' => $stack['class'],
                    'type' => $stack['type'] === '->' ?
                        'method' :
                        'static-method'
                ];
            }
            else {

                $ret['caller'] = [
                    'name' => $stack['function'],
                    'context' => $stack['class'] ?? '{global}',
                    'type' => $stack['function'] === '{closure}' ?
                        'lambda' :
                        'function'
                ];
            }
        }
        else {

            $ret['caller'] = [
                'name' => '{global}',
                'context' => '{global}',
                'type' => 'statement'
            ];
        }

        return $ret;
    }

    /**
     * Dump a variable into easy-storing array.
     *
     * @param $data
     *
     * @return array|string
     */
    public static function dumpVariable($data)
    {
        if (is_object($data)) {

            return '[object:' . get_class($data) . ']';
        }
        elseif (is_array($data)) {

            $ret = [];

            foreach ($data as $key => $item) {

                $ret[$key] = self::dumpVariable($item);
            }

            return $ret;
        }
        else {

            return $data;
        }
    }
}
