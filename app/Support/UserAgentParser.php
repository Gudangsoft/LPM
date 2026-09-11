<?php

namespace App\Support;

/**
 * Very small, dependency-free UA sniffer - just enough to show a friendly
 * "Chrome on Windows" label + icon on the active-sessions panel. Not meant
 * to be authoritative (no bot detection, no version parsing).
 */
class UserAgentParser
{
    public static function parse(?string $userAgent): array
    {
        $ua = $userAgent ?? '';

        return [
            'os' => self::os($ua),
            'browser' => self::browser($ua),
            'icon' => self::icon($ua),
        ];
    }

    public static function label(?string $userAgent): string
    {
        $info = self::parse($userAgent);

        if ($info['browser'] === 'Unknown' && $info['os'] === 'Unknown') {
            return 'Perangkat tidak dikenal';
        }

        return trim("{$info['browser']} di {$info['os']}");
    }

    private static function os(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/windows/i', $ua) => 'Windows',
            (bool) preg_match('/iphone|ipad|ipod/i', $ua) => 'iOS',
            (bool) preg_match('/mac os x|macintosh/i', $ua) => 'macOS',
            (bool) preg_match('/android/i', $ua) => 'Android',
            (bool) preg_match('/linux/i', $ua) => 'Linux',
            default => 'Unknown',
        };
    }

    private static function browser(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/edg\//i', $ua) => 'Edge',
            (bool) preg_match('/opr\/|opera/i', $ua) => 'Opera',
            (bool) preg_match('/firefox/i', $ua) => 'Firefox',
            (bool) preg_match('/chrome/i', $ua) => 'Chrome',
            (bool) preg_match('/safari/i', $ua) => 'Safari',
            default => 'Unknown',
        };
    }

    private static function icon(string $ua): string
    {
        return match (true) {
            (bool) preg_match('/iphone|ipod|android.*mobile/i', $ua) => 'bi-phone',
            (bool) preg_match('/ipad|android(?!.*mobile)/i', $ua) => 'bi-tablet',
            default => 'bi-laptop',
        };
    }
}
