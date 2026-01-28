<?php

class Time {

    public static function get_time($pasttime, $today = null) {
        if (!$today) {
            $today = date("Y-m-d H:i:s");
        }

        $datetime1 = date_create($pasttime);
        $datetime2 = date_create($today);

        if (!$datetime1 || !$datetime2) {
            return 'Invalid date';
        }

        $interval = date_diff($datetime1, $datetime2);

        // Check for years
        if ($interval->y > 0) {
            return $interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
        }

        // Check for months
        if ($interval->m > 0) {
            return $interval->m . " month" . ($interval->m > 1 ? "s" : "") . " ago";
        }

        // Check for days
        if ($interval->d > 1) {
            return $interval->d . " days ago";
        } elseif ($interval->d == 1) {
            return "1 day ago";
        }

        // Check for hours
        if ($interval->h > 0) {
            return $interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
        }

        // Check for minutes
        if ($interval->i > 0) {
            return $interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
        }

        // Check for seconds
        if ($interval->s > 10) {
            return $interval->s . " seconds ago";
        } elseif ($interval->s > 0) {
            return "few seconds ago";
        }

        return 'Just now';
    }

}
?>
