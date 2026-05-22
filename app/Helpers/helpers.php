<?php

if (!function_exists('formatDateTime')) {
    function formatDateTime($datetime, $includeSeconds = true)
    {
        if (!$datetime) return 'N/A';
        if ($includeSeconds) {
            return $datetime->format('F d, Y | h:i:s A');
        }
        return $datetime->format('F d, Y | h:i A');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($datetime)
    {
        if (!$datetime) return 'N/A';
        return $datetime->format('F d, Y');
    }
}

if (!function_exists('formatTime')) {
    function formatTime($datetime, $includeSeconds = true)
    {
        if (!$datetime) return 'N/A';
        if ($includeSeconds) {
            return $datetime->format('h:i:s A');
        }
        return $datetime->format('h:i A');
    }
}