<?php

namespace App\Services;

class UrlSerivece
{
    public static function cleanUrl(string $url): string {
        $parts = parse_url($url);

        if (!isset($parts['query'])) {
            return $url;
        }

        parse_str($parts['query'], $query);

        unset($query['tab']);

        $newQuery = http_build_query($query, '', '&', PHP_QUERY_RFC3986);

        if ($newQuery === '') {
            $result = $parts['scheme'] . '://' . $parts['host'];
            if (isset($parts['port'])) {
                $result .= ':' . $parts['port'];
            }
            if (isset($parts['path'])) {
                $result .= $parts['path'];
            }
            return $result;
        }

        $result = $parts['scheme'] . '://' . $parts['host'];
        if (isset($parts['port'])) {
            $result .= ':' . $parts['port'];
        }
        if (isset($parts['path'])) {
            $result .= $parts['path'];
        }
        $result .= '?' . $newQuery;

        if (isset($parts['fragment'])) {
            $result .= '#' . $parts['fragment'];
        }

        return $result;
    }
}
