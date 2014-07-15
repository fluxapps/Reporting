<?php

/**
 * Class ilReportingFormatter
 *
 * @author Stefan Wanzenried <sw@studer-raimann.ch>
 */
class ilReportingFormatter {

    const FORMAT_INT_YES_NO = 1;        // 1 => Yes, 0 => No
    const FORMAT_INT_BOOLEAN = 2;       // 1 => True, 0 => False
    const FORMAT_INT_STATUS = 3;        // Status code to status string
    const FORMAT_STR_DATE = 4;          // Format date string to another format
    const FORMAT_STR_PERCENTAGE = 5;    // Append percentage to string
    const FORMAT_STR_OBJECT_TYPE = 6;   // Object type to String, e.g. crs to Course


    const DEFAULT_DATE_FORMAT = 'd.M Y, H:i';

    /**
     * @var ilReportingFormatter
     */
    private static $instance;

    /**
     * @var ilReportingPlugin
     */
    protected $pl;

    /**
     * @var ilLanguage
     */
    protected $lng;

    private function __construct() {
        global $lng;
        $this->lng = $lng;
        $this->pl = new ilReportingPlugin();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            $instance = new self();
            self::$instance = $instance;
            return $instance;
        } else {
            return self::$instance;
        }
    }

    /**
     * Format a value with a format.
     * If no format is specified and the value is
     *  - an array: Implodes values
     *  - a string: Returns same value
     *
     * @param $value
     * @param int $format
     * @param array $options
     * @return string
     */
    public function format($value, $format=0, $options=array()) {
        switch ($format) {
            case self::FORMAT_INT_YES_NO:
                return $value ? $this->pl->txt('yes') : $this->pl->txt('no');
            case self::FORMAT_INT_STATUS:
                $status = (int) $value;
                return $this->pl->txt("status$status");
            case self::FORMAT_STR_DATE:
                if (!$value) return '';
                $timestamp = strtotime($value);
                $format = isset($options['format']) ? $options['format'] : self::DEFAULT_DATE_FORMAT;
                return date($format, $timestamp);
            case self::FORMAT_STR_PERCENTAGE:
                return (int) $value . '%';
            case self::FORMAT_STR_OBJECT_TYPE:
                return ($value) ? $this->lng->txt($value) : '';
            default:
                return (is_array($value)) ? implode(',', $value) : $value;
        }
    }

}