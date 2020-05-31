<?php

use LaraAreaMake\Exceptions\LaraAreaCommandException;
use LaraAreaSupport\Str;

/**
 * @param $data
 * @param $start
 * @param $end
 * @return string
 */
function implode_wrap($data, $start, $end)
{
    return $start . implode($end . $start, $data) .   $end;
}

/**
 * @param $value
 * @param int $options
 * @param int $depth
 * @return mixed
 */
function lara_maker_array_encode($value, $options = 0, $depth = 512)
{
    $json = json_encode($value, $options, $depth);
    return str_replace(['{', '}', ':', '"', ' ', ';'], ['[', ']', '=', '', '___', '____'], $json);
}

/**
 * @param $json
 * @return array
 * @throws LaraAreaCommandException
 */
function lara_maker_array_decode($json)
{
    $json = str_replace(['____', '___'], [';', ' '], $json);
    if ('[]' == $json) {
        return [];
    }

    $json = \Illuminate\Support\Str::replaceFirst('[', '', $json);
    $json = \Illuminate\Support\Str::replaceLast(']', '', $json);
    $result = [];
    if (\Illuminate\Support\Str::startsWith($json, '[')) {
        $result[] = lara_maker_array_decode($json);
        return $result;
    }
    do {
        $commaPos = strpos($json, ',');
        $equalPos = strpos($json, '=');
        if (false !== $commaPos && false != $equalPos) {
            if ($commaPos > $equalPos) {
                $key = Str::before($json, '=', 1);
                $json = Str::after($json, '=');
                if (\Illuminate\Support\Str::startsWith($json, '[')) {
                    $result[$key] = Str::before($json, ',', 1);
                    $endPos = get_position($json);
                    if (false == $endPos) {
                        throw new LaraAreaCommandException('incorrect maker array format' . $json);
                    }

                    $value = substr($json, 0, $endPos + 1);
                    $json = substr($json, $endPos + 1, strlen($json) - $endPos);
                    $json = trim($json, ',');
                    $result[$key] = lara_maker_array_decode($value);
                } else {
                    $value = Str::before($json, ',', 1);
                    $json = Str::after($json, ',');
                    $result[$key] = $value;
                }
            } else {
                $value = Str::before($json, ',' ,1);
                $json = Str::after($json, ',');
                $result[] = $value;
            }
        } else {
            if (false !== $commaPos) {
                $value = Str::before($json, ',', 1);
                $json = Str::after($json, ',');
                $result[] = $value;
            } elseif (false !== $equalPos) {
                $key = Str::before($json, '=', 1);
                $value = Str::after($json, '=');
                $result[$key] = \Illuminate\Support\Str::startsWith($value, '[')
                    ? lara_maker_array_decode($value)
                    : Str::after($json, '=');
                $json = '';
            } else {
                $result[] = $json;
                $json = '';
            }
        }
    }
    while (!empty($json));

    return $result;
}

/**
 * @param $json
 * @return bool
 */
function get_position($json)
{
    $startPositions = Str::positions($json, '[');
    $endPositions = Str::positions($json, ']');

    if (count($startPositions) != count($endPositions)) {
        return false;
    }

    foreach ($endPositions as $position => $endPosition) {
        $nextPosition = $position + 1;

        if (! isset($startPositions[$nextPosition])) {
            return $endPosition;
        }

        if ($startPositions[$nextPosition] > $endPosition) {
            return $endPosition;
        }
    }

    return false;
}