<?php

defined('ABSPATH') || exit;

/** Resolves YourProductID to MasterStudy LMS courses and verifies enrolment. */
class DRMX5_MSLMS_Course_Authorizer {
    /** Return the first listed MasterStudy LMS course the current user may access. */
    public static function get_accessible_course($product_ids, $user_id) {
        if (!class_exists('STM_LMS_Course') || !class_exists('STM_LMS_User')) {
            return false;
        }

        $tokens = preg_split('/\s*-\s*/', trim((string) $product_ids), -1, PREG_SPLIT_NO_EMPTY);
        foreach (array_unique($tokens) as $token) {
            if (!ctype_digit($token) || (int) $token <= 0) {
                continue;
            }
            $course_id = (int) $token;
            if (get_post_type($course_id) !== 'stm-courses') {
                continue;
            }

            try {
                $record = STM_LMS_Course::get_user_course((int) $user_id, $course_id);
                $has_access = (bool) STM_LMS_User::has_course_access($course_id, '', false);
            } catch (Throwable $exception) {
                $record = false;
                $has_access = false;
            }
            if (!$record || !$has_access) {
                continue;
            }

            $start = self::record_timestamp($record, 'start_time');
            $end = self::record_timestamp($record, 'end_time');
            if ($end && $end < time()) {
                continue;
            }
            if (!$start) {
                $start = time();
            }

            return array(
                'id' => $course_id,
                'start' => $start,
                'end' => $end ?: strtotime('+10 years'),
            );
        }
        return false;
    }

    /** Read a date field from the array/object returned by MasterStudy. */
    private static function record_timestamp($record, $field) {
        $value = is_array($record) ? ($record[$field] ?? null) :
            (is_object($record) && isset($record->{$field}) ? $record->{$field} : null);
        if (is_numeric($value)) {
            return (int) $value;
        }
        if (is_string($value) && $value !== '') {
            $timestamp = strtotime($value);
            return $timestamp ? (int) $timestamp : 0;
        }
        return 0;
    }
}

