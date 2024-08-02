<?php namespace App\Core;


use CodeIgniter\I18n\Exceptions\I18nException;
use DateInterval;
use DateTime;
use DateTimeZone;
use IntlCalendar;
use IntlDateFormatter;
use Locale;


class Time extends \CodeIgniter\I18n\Time 
{

    public function __construct(string $time = null, $timezone = null, string $locale = null){
        return parent::__construct($time, $timezone, $locale );
    }
    
    public static function parse(string $datetime, $timezone = null, string $locale = null){
        return new Time($datetime, $timezone, $locale);
    }
    
    public function humanize(){
        // Use English en_US at the time here, other wise it will generate bangla characters and
        // As a result "fromDateTime" will generate error of unexpecyed characters.
        $now  = IntlCalendar::fromDateTime(Time::now($this->timezone, 'en_US')->toDateTimeString());
        $time = $this->getCalendar()->getTime();

        $years   = $now->fieldDifference($time, IntlCalendar::FIELD_YEAR);
        $months  = $now->fieldDifference($time, IntlCalendar::FIELD_MONTH);
        $days    = $now->fieldDifference($time, IntlCalendar::FIELD_DAY_OF_YEAR);
        $hours   = $now->fieldDifference($time, IntlCalendar::FIELD_HOUR_OF_DAY);
        $minutes = $now->fieldDifference($time, IntlCalendar::FIELD_MINUTE);

        $phrase = null;

        if ($years !== 0)
        {
                $phrase = lang('Time.years', [abs($years)]);
                $before = $years < 0;
        }
        else if ($months !== 0)
        {
                $phrase = lang('Time.months', [abs($months)]);
                $before = $months < 0;
        }
        else if ($days !== 0 && (abs($days) >= 7))
        {
                $weeks  = ceil($days / 7);
                $phrase = lang('Time.weeks', [abs($weeks)]);
                $before = $days < 0;
        }
        else if ($days !== 0)
        {
                $before = $days < 0;

                // Yesterday/Tomorrow special cases
                if (abs($days) === 1)
                {
                        return $before ? lang('Time.yesterday') : lang('Time.tomorrow');
                }

                $phrase = lang('Time.days', [abs($days)]);
        }
        else if ($hours !== 0)
        {
                // Display the actual time instead of a regular phrase.
                return $this->format('g:i a');
        }
        else if ($minutes !== 0)
        {
                $phrase = lang('Time.minutes', [abs($minutes)]);
                $before = $minutes < 0;
        }
        else
        {
                return lang('Time.now');
        }

        return $before ? lang('Time.ago', [$phrase]) : lang('Time.inFuture', [$phrase]);
    } // EOF

        
} // EOC