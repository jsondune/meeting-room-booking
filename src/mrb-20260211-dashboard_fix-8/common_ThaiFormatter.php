<?php
/**
 * Thai Formatter Component
 * Formats dates using Buddhist Era (พ.ศ.) instead of Christian Era (ค.ศ.)
 * 
 * @author Digital Technology & AI Division
 * @version 1.1.0
 */

namespace common\components;

use Yii;
use yii\i18n\Formatter;

/**
 * ThaiFormatter extends Yii2's Formatter to display dates in Buddhist Era (พ.ศ.)
 * Buddhist Era = Christian Era + 543
 */
class ThaiFormatter extends Formatter
{
    /**
     * @var array Thai month names (full)
     */
    public static $thaiMonths = [
        1 => 'มกราคม',
        2 => 'กุมภาพันธ์',
        3 => 'มีนาคม',
        4 => 'เมษายน',
        5 => 'พฤษภาคม',
        6 => 'มิถุนายน',
        7 => 'กรกฎาคม',
        8 => 'สิงหาคม',
        9 => 'กันยายน',
        10 => 'ตุลาคม',
        11 => 'พฤศจิกายน',
        12 => 'ธันวาคม',
    ];

    /**
     * @var array Thai month names (short)
     */
    public static $thaiMonthsShort = [
        1 => 'ม.ค.',
        2 => 'ก.พ.',
        3 => 'มี.ค.',
        4 => 'เม.ย.',
        5 => 'พ.ค.',
        6 => 'มิ.ย.',
        7 => 'ก.ค.',
        8 => 'ส.ค.',
        9 => 'ก.ย.',
        10 => 'ต.ค.',
        11 => 'พ.ย.',
        12 => 'ธ.ค.',
    ];

    /**
     * @var array Thai day names
     */
    public static $thaiDays = [
        0 => 'อาทิตย์',
        1 => 'จันทร์',
        2 => 'อังคาร',
        3 => 'พุธ',
        4 => 'พฤหัสบดี',
        5 => 'ศุกร์',
        6 => 'เสาร์',
    ];

    /**
     * @var array Thai day names (full with prefix)
     */
    public static $thaiDaysFull = [
        0 => 'วันอาทิตย์',
        1 => 'วันจันทร์',
        2 => 'วันอังคาร',
        3 => 'วันพุธ',
        4 => 'วันพฤหัสบดี',
        5 => 'วันศุกร์',
        6 => 'วันเสาร์',
    ];

    /**
     * {@inheritdoc}
     */
    public function asDate($value, $format = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $timestamp = $this->normalizeDatetimeValue($value);
        if ($timestamp === false) {
            return $this->nullDisplay;
        }

        // Convert to Buddhist Era
        return $this->formatThaiDate($timestamp, $format ?? 'medium');
    }

    /**
     * {@inheritdoc}
     */
    public function asDatetime($value, $format = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $timestamp = $this->normalizeDatetimeValue($value);
        if ($timestamp === false) {
            return $this->nullDisplay;
        }

        return $this->formatThaiDatetime($timestamp, $format ?? 'medium');
    }

    /**
     * Format date in Thai Buddhist Era
     * 
     * @param int|\DateTime $timestamp
     * @param string $format short|medium|long|full or php:format or ICU format
     * @return string
     */
    protected function formatThaiDate($timestamp, $format)
    {
        if ($timestamp instanceof \DateTime) {
            $dt = $timestamp;
        } else {
            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);
        }

        $day = (int)$dt->format('j');
        $month = (int)$dt->format('n');
        $year = (int)$dt->format('Y') + 543; // Convert to Buddhist Era
        $dayOfWeek = (int)$dt->format('w');

        // Handle php: prefix format
        if (strpos($format, 'php:') === 0) {
            $phpFormat = substr($format, 4);
            return $this->formatWithPhpPattern($dt, $phpFormat, $year);
        }

        // Handle standard named formats
        switch ($format) {
            case 'short':
                return sprintf('%d/%d/%d', $day, $month, $year % 100);
            case 'medium':
                return sprintf('%d %s %d', $day, self::$thaiMonthsShort[$month], $year);
            case 'long':
                return sprintf('%d %s พ.ศ. %d', $day, self::$thaiMonths[$month], $year);
            case 'full':
                return sprintf('%s ที่ %d %s พ.ศ. %d', self::$thaiDaysFull[$dayOfWeek], $day, self::$thaiMonths[$month], $year);
        }

        // Handle ICU-style format patterns
        return $this->formatWithIcuPattern($dt, $format, $year, $month, $day, $dayOfWeek);
    }

    /**
     * Format datetime in Thai Buddhist Era
     * 
     * @param int|\DateTime $timestamp
     * @param string $format
     * @return string
     */
    protected function formatThaiDatetime($timestamp, $format)
    {
        if ($timestamp instanceof \DateTime) {
            $dt = $timestamp;
        } else {
            $dt = new \DateTime();
            $dt->setTimestamp($timestamp);
        }

        $time = $dt->format('H:i');
        
        // Handle php: prefix format
        if (strpos($format, 'php:') === 0) {
            $phpFormat = substr($format, 4);
            $year = (int)$dt->format('Y') + 543;
            return $this->formatWithPhpPattern($dt, $phpFormat, $year);
        }

        $date = $this->formatThaiDate($dt, $format);

        return $date . ' ' . $time . ' น.';
    }

    /**
     * Format with ICU-style pattern, converting to Thai Buddhist Era
     * 
     * @param \DateTime $dt
     * @param string $pattern
     * @param int $year Buddhist year
     * @param int $month
     * @param int $day
     * @param int $dayOfWeek
     * @return string
     */
    protected function formatWithIcuPattern($dt, $pattern, $year, $month, $day, $dayOfWeek)
    {
        $result = $pattern;
        
        // Replace year patterns (must do longer patterns first)
        $result = str_replace('yyyy', (string)$year, $result);
        $result = str_replace('yy', substr((string)$year, -2), $result);
        
        // Replace month patterns (longer first)
        $result = str_replace('MMMM', self::$thaiMonths[$month], $result);
        $result = str_replace('MMM', self::$thaiMonthsShort[$month], $result);
        $result = str_replace('MM', str_pad($month, 2, '0', STR_PAD_LEFT), $result);
        
        // Replace day patterns (longer first)
        $result = str_replace('dd', str_pad($day, 2, '0', STR_PAD_LEFT), $result);
        // Handle single 'd' - match word boundary or at start/end
        $result = preg_replace('/(?<![a-zA-Z])d(?![a-zA-Z])/', (string)$day, $result);
        
        // Replace day of week patterns
        $result = str_replace('EEEE', self::$thaiDaysFull[$dayOfWeek], $result);
        $result = str_replace('EEE', self::$thaiDays[$dayOfWeek], $result);
        
        // Replace time patterns
        $result = str_replace('HH', $dt->format('H'), $result);
        $result = str_replace('mm', $dt->format('i'), $result);
        $result = str_replace('ss', $dt->format('s'), $result);
        
        return $result;
    }

    /**
     * Format with PHP date pattern, converting year to Buddhist Era
     * 
     * @param \DateTime $dt
     * @param string $pattern
     * @param int $buddhistYear
     * @return string
     */
    protected function formatWithPhpPattern($dt, $pattern, $buddhistYear)
    {
        // Replace year patterns with Buddhist Era
        $result = '';
        $len = strlen($pattern);
        
        for ($i = 0; $i < $len; $i++) {
            $char = $pattern[$i];
            switch ($char) {
                case 'Y': // 4-digit year
                    $result .= $buddhistYear;
                    break;
                case 'y': // 2-digit year
                    $result .= $buddhistYear % 100;
                    break;
                case 'F': // Full month name
                    $result .= self::$thaiMonths[(int)$dt->format('n')];
                    break;
                case 'M': // Short month name
                    $result .= self::$thaiMonthsShort[(int)$dt->format('n')];
                    break;
                case 'l': // Full day name
                    $result .= self::$thaiDaysFull[(int)$dt->format('w')];
                    break;
                case 'D': // Short day name
                    $result .= self::$thaiDays[(int)$dt->format('w')];
                    break;
                default:
                    $result .= $dt->format($char);
            }
        }
        
        return $result;
    }

    /**
     * Normalize datetime value to timestamp or DateTime
     * 
     * @param mixed $value
     * @return int|\DateTime|false
     */
    protected function normalizeDatetimeValue($value)
    {
        if ($value instanceof \DateTime || $value instanceof \DateTimeInterface) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int)$value;
        }

        if (is_string($value)) {
            $timestamp = strtotime($value);
            if ($timestamp !== false) {
                return $timestamp;
            }
        }

        return false;
    }

    /**
     * Get current year in Buddhist Era
     * 
     * @return int
     */
    public static function getBuddhistYear()
    {
        return (int)date('Y') + 543;
    }

    /**
     * Convert Christian year to Buddhist year
     * 
     * @param int $year
     * @return int
     */
    public static function toBuddhistYear($year)
    {
        return $year + 543;
    }

    /**
     * Convert Buddhist year to Christian year
     * 
     * @param int $year
     * @return int
     */
    public static function toChristianYear($year)
    {
        return $year - 543;
    }

    /**
     * Format relative time in Thai
     * 
     * @param mixed $value
     * @param mixed $referenceTime
     * @return string
     */
    public function asRelativeTime($value, $referenceTime = null)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        $timestamp = $this->normalizeDatetimeValue($value);
        if ($timestamp === false) {
            return $this->nullDisplay;
        }

        if ($timestamp instanceof \DateTime) {
            $timestamp = $timestamp->getTimestamp();
        }

        $referenceTimestamp = $referenceTime === null ? time() : $this->normalizeDatetimeValue($referenceTime);
        if ($referenceTimestamp instanceof \DateTime) {
            $referenceTimestamp = $referenceTimestamp->getTimestamp();
        }

        $diff = $referenceTimestamp - $timestamp;
        $absDiff = abs($diff);
        $isFuture = $diff < 0;

        if ($absDiff < 60) {
            return 'เมื่อสักครู่';
        } elseif ($absDiff < 3600) {
            $minutes = floor($absDiff / 60);
            return $isFuture ? "อีก {$minutes} นาที" : "{$minutes} นาทีที่แล้ว";
        } elseif ($absDiff < 86400) {
            $hours = floor($absDiff / 3600);
            return $isFuture ? "อีก {$hours} ชั่วโมง" : "{$hours} ชั่วโมงที่แล้ว";
        } elseif ($absDiff < 604800) {
            $days = floor($absDiff / 86400);
            if ($days == 1) {
                return $isFuture ? 'พรุ่งนี้' : 'เมื่อวาน';
            }
            return $isFuture ? "อีก {$days} วัน" : "{$days} วันที่แล้ว";
        } elseif ($absDiff < 2592000) {
            $weeks = floor($absDiff / 604800);
            return $isFuture ? "อีก {$weeks} สัปดาห์" : "{$weeks} สัปดาห์ที่แล้ว";
        } elseif ($absDiff < 31536000) {
            $months = floor($absDiff / 2592000);
            return $isFuture ? "อีก {$months} เดือน" : "{$months} เดือนที่แล้ว";
        } else {
            $years = floor($absDiff / 31536000);
            return $isFuture ? "อีก {$years} ปี" : "{$years} ปีที่แล้ว";
        }
    }

    /**
     * Format as Thai currency
     * 
     * @param mixed $value
     * @param string $currency
     * @param array $options
     * @param array $textOptions
     * @return string
     */
    public function asCurrency($value, $currency = 'THB', $options = [], $textOptions = [])
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return number_format((float)$value, 2) . ' บาท';
    }

    /**
     * Format as Thai number
     * 
     * @param mixed $value
     * @param int $decimals
     * @return string
     */
    public function asThaiNumber($value, $decimals = 0)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }

        return number_format((float)$value, $decimals);
    }
}
