<?php



/**
 * When we update image, it doesn't work as cache time is added. To fix this error we need to add update time to the url. 
 * When we upload new image it will change update time to get newer version and display it to the browser immediately.
 * @param int $ct Number of seconds to cache image in browser (cache will be automatically cleared after that period)
 * @return string Image URL for avatar
 */
function getProfilePictureURL( int $ct = 31536000 ){
    $role = service('AuthLibrary')->getUserRole();
    if(is_object($role) AND $role->u_thumbnail_id > 0){
        $avUrl  = school_url("arf/p/{$role->u_thumbnail_id}.jpg?ct=$ct");
        $atAv   = service('AttachmentsThumbModel')->select('atp_updated_at')->find($role->u_thumbnail_id);
        if(is_object($atAv)){ $avUrl .= '&upt=' . urlencode($atAv->atp_updated_at); }
        return $avUrl;
    }
    $defaultImth = (is_object($role) AND $role->student_u_gender == 'female') ? 'profile-thumb-girl-240x240.jpg' : 'profile-thumb-boy-240x240.jpg';
    return cdn_url("default-images/$defaultImth");
} /*EOF*/


function get_attachment_full_path( object $att, string $type = 'original' ){
    return service('AttachmentsModel')->get_attachment_full_path( $att, $type );
}

function get_attachment_path( int $at_id, string $type = 'original' ){
    return service('AttachmentsModel')->get_attachment_path( $at_id, $type );
}

function set_flash_msg_from_event( string $msg ){
    // We might already have closed session using session_write_close(),
    @session_start(); // Rreopen session if already closed to write in session
    
    // We may have message already set by controllers. Do not overwrite it.
    $msg_set_from_controllers  = session()->getFlashdata('display_msg');
    session()->setFlashdata('display_msg', $msg_set_from_controllers . $msg );
    
    /**
    * Our writing to session done. So allow other request of the same browser(JSON request) load data faster.
    * CUTION: Use @session_start() before redirect()->with() to show display messages.
    */
    session_write_close(); 
} // EOF 


/**
 * Used to show tooltip
 * @param string $txt
 * @param string $placement
 * @return string
 */

function tt_title( string $txt = '', string $placement = null, bool $esc = true ){
    $txt        = $esc ? esc($txt) : $txt;
    $placement  = $esc ? esc($placement) : $placement;
    
    $str = ' title="'.$txt.'" data-toggle="tooltip" ';
    if($placement) {
        $str .= ' data-placement="'.$placement.'" ';
    }
    return $str;
}



/**
 * Wrap text with div to show in front end.
 */
function get_display_msg( string $msg, string $color = 'info', string $position = 'center'){
    return '<div class="alert alert-'.$color.' text-'.$position.' w-100" role="alert">'.$msg.'</div>';
}

/**
 * We may need to show danger/success/info message to the page of many actions.
 */
function get_initial_msg(){
    // After a redirect using redirect()->to()->with(display_msg)
    $fMsg  = session()->getFlashdata('display_msg');  
    if( strlen( $fMsg) < 1 ) return ''; // We have no message set to show
    // Wrap in a div if we have only text msg, Use "<div" not "<div>" as we have "<div class=..."
    if( strpos( $fMsg, '<div' ) === FALSE){ 
        $fMsg = get_display_msg($fMsg, 'info');
    }
    return $fMsg;
} // EOF

/**
 * If error exists in an input/form field then it will be returned. 
 * @param mixed $validation It can be object or null. If object then we will try to find error.
 * @param string $name Input field name to find error.
 * @param string $class css class can be added here.  Default is alert-danger
 * @return string Empty string will be returned if no error exists in the specified form field.
 */
function get_form_error_msg($validation, string $name, string $class = 'alert-danger', string $html = 'div'){
    if(is_object($validation) AND $validation->hasError($name)) 
        return "<$html class='$class'>" . $validation->getError($name) . "</$html>";
    else
        return '';
}


function get_form_error_msg_from_array($errors, string $key, $class = 'alert-danger', $html = 'div'){
    if(isset($errors) AND is_array($errors) AND array_key_exists($key,$errors) ){
        return "<$html class='$class'>" . esc($errors[$key]) . "</$html>";
    }
    return '';
}


function update_option( string $option_key, $option_value ){
    $oModel = service('OptionsModel');
    $value  = (is_string($option_value) AND strlen($option_value) > 0 ) ? $option_value : serialize($option_value);
    $tbl    = $oModel->prefixTable($oModel->table);
        
    $sql    = "INSERT INTO `$tbl` (`option_key`, `option_value`,`option_inserted_at`,`option_updated_at`) 
               VALUES (".$oModel->escape($option_key).", ".$oModel->escape($value).", CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) 
               ON DUPLICATE KEY UPDATE 
               `option_key` = VALUES(`option_key`), 
               `option_value` = VALUES(`option_value`), 
               `option_updated_at` = VALUES(`option_updated_at`);";
    return $oModel->simpleQuery($sql); // Boolean true or false
}


function get_option( string $option_key, $default = NULL ){   
    $row = service('OptionsModel')
            ->select('option_value')
            ->where('option_key', $option_key)  // Unique field
            ->first();                          // We should have only one row
    
    if(is_object($row)){
        $value =  @unserialize($row->option_value);
        if ($row->option_value === 'b:0;' || $value !== false) {
            return $value; // It is a serialized value. Return unserialized value. b:0; = false
        }
        if(strlen($row->option_value) > 0 ) return $row->option_value; // It might have simple string like date  (not serialized)
    }
    return is_null( $default ) ? '' : $default;
}


/**
 * Get human readable string of timestamp
 * @param string $datetime
 * @param bool $full
 * @return string Human readable time. Ex: 1 day ago, 2 weeks ago
 */
function time_elapsed_string($datetime, $full = false) {
    // Remove this line if not work.
    return App\Core\Time::parse(strval($datetime), 'Asia/Dhaka','en-US')->humanize();
    
    // The following lines will work if you remove previous line.
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array( 'y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second', );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


/**
 * How many days of an event is remaining. Get human readable string of timestamp. 
 * @param string $datetime
 * @param bool $full
 * @return string Human readable time. Ex: 1 day ago, 2 weeks ago
 */
function time_remaining_string($datetime, $full = false) {
    // Remove this line if not work.
    return App\Core\Time::parse(strval($datetime), 'Asia/Dhaka','en-US')->humanize();
    
    // The following lines will work if you remove previous line.
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array('y' => 'year','m' => 'month','w' => 'week','d' => 'day','h' => 'hour','i' => 'minute','s' => 'second',);
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? ($diff->invert ? implode(', ', $string) . ' ago' : implode(', ', $string) . ' remaining') : 'today';
}

function humanizeDateRanges($start, $end){
    $startTime=strtotime($start);
    $endTime=strtotime($end);

    if(date('Y',$startTime)!=date('Y',$endTime)){
        $formatted_range = date('F j, Y',$startTime) . " to " . date('F j, Y',$endTime);
    }else{
        if((date('j',$startTime)==1)&&(date('j',$endTime)==date('t',$endTime))){
            $formatted_range = date('F',$startTime) . " to " . date('F, Y',$endTime);
        }else{
            if(date('m',$startTime)!=date('m',$endTime)){
                $formatted_range = date('F j',$startTime) . " to " . date('F j, Y',$endTime);
            }else{
                $formatted_range = date('F j',$startTime) . " to " . date('j, Y',$endTime);
            }
        }
    }
    $replace = ['to' => '-', 'January' => 'জানুয়ারি', 'February' => 'ফেব্রুয়ারি', 'March' => 'মার্চ', 'April' => 'এপ্রিল', 'May' => 'মে', 'June' => 'জুন', 'July' => 'জুলাই', 'August' => 'আগস্ট', 'September' => 'সেপ্টেম্বর', 'October' => 'অক্টুবর','November' => 'নভেম্বর', 'December' => 'ডিসেম্বর'];
    
    $formatted_range_number = $formatted_range;
    foreach($replace as $find => $replace ){
        $formatted_range_number = str_replace( $find, $replace, $formatted_range_number);
    }
    return $formatted_range_number;
} /* EOF */


function get_allowed_image_extensions(){
    return ['gif','jpeg','jpg','png']; // bmp not allowed
}
function get_allowed_video_extensions(){
    return ['bmp', 'avi','mpeg','3gp','mpg','mov','qt','movie','m4a','mp4'];
}
function get_allowed_audio_extensions(){
    return ['mp3','wav','ogg'];
}
function get_allowed_other_extensions(){
    return ['pdf','gz','gzip','tar','tgz','zip','rar','txt','text','doc','docx'];
}

/**
 * Allowed file extensions in our site. File can be verified by using $file->guessExtension()
 */
function get_allowed_file_extensions( $type = 'all'){
    $ext = array();
    $ext['image'] = get_allowed_image_extensions(); // Allowed image extensions. 
    $ext['video'] = get_allowed_video_extensions(); // Allowed video extensions.
    $ext['audio'] = get_allowed_audio_extensions(); // Allowed audio extensions.
    $ext['others'] = get_allowed_other_extensions(); // Allowed other extensions.
    $ext['all'] = array_merge( $ext['audio'], $ext['video'], $ext['image'], $ext['others'] );
    
    if( isset( $ext[$type]) ) return $ext[$type];
    return array( 'audio' => $ext['audio'], 'video' => $ext['video'], 'image' => $ext['image'], 'others' => $ext['others'] );
}



/* Used in school front before the name of teachers/students */
function get_name_initials( $label_by_key = false ){
    $initials = array(
        '' => 'No Initial',    // Some one may want to ignore all kind of initials
        'adv'   => 'Adv.',
        'assocp'=> 'Assoc. Prof.',
        'babu'  => 'Babu',
        'dr'    => 'Dr.',
        'engr'  => 'Engr.',
        'lect'  => 'Lect.',
        'madam' => 'Madam',
        'mam'   => 'Mam.',
        'md'    => 'Md.',
        'mr'    => 'Mr.',
        'mrs'   => 'Mrs.',
        'ms'    => 'Ms.',
        'prof'  => 'Prof.',
        'profdr' => 'Prof. Dr.',
        'sir'   => 'Sir.',
        'sree'  => 'Sree',
        'uncle' => 'Uncle',
    );
    /* Requested to get string value. Return initial if key exists otherwise empty string. Normally used in profile view before name. */
    if(is_string($label_by_key)){
        if( strlen($label_by_key) > 0 AND array_key_exists( $label_by_key, $initials)){
            return $initials[$label_by_key];
        }else{ /* $label_by_key might be empty string, so return empty value */
            return '';
        }
    }
    /* Requested to get whole array. Normally Used in form. */
    return $initials;
}

/* These 3 index can be saved to database. other value will not be accepted in users table. */
function get_gender_list( $key = false ){
    $list = array(
        'male'      => 'Male',
        'female'    => 'Female', 
        '3rd'       => 'Third Gender'
    );
    if($key){
        // Trying to display in page using echo, so return empty string if key not exists.
        if( array_key_exists($key, $list) ) return $list[$key];
        return '';
    }
    return $list; // Return list it is being used in input dropdown
}

/* Accepted list of religions are given here. We will accept one of the following to our database .*/
function get_religion_list( $key = false ){
    $list = array(
        'christianity'      => 'Christianity',
        'islam'             => 'Islam',
        'hinduism'          => 'Hinduism',
        'nonreligious'      => 'Nonreligious',
        'buddhism'          => 'Buddhism',
        'primal-indigenous' => 'Primal Indigenous',
        'diasporic'         => 'Diasporic',
        'sikhism'           => 'Sikhism',
        'juche'             => 'Juche',
        'judaism'           => 'Judaism',
        'unaffiliated'      => 'Unaffiliated',
        'others'            => 'Others'
    );
    if($key){
        // Trying to display in page using echo, so return empty string if key not exists.
        if( array_key_exists($key, $list) ) return $list[$key];
        return '';
    }
    return $list; // Return list it is being used in input dropdown
}

function get_month_name_by_number( int $month_num ){
    return date("F", mktime(0, 0, 0, $month_num, 10));
}

/**
 * Return full month name if short name is exists. Otherwise return array of 12 months.
 * @param optional $short_name It can be empty string. If empty string return empty string to prevent error.
 * @return mixed Return array or string.
 */
function get_month_list_en( $short_name = false ){
    $months = [''=>'']; // array( jan => January, ... )
    
    for ($m=1; $m<=12; $m++) {
        $months[strtolower(date('M', mktime(0,0,0,$m, 1, date('Y'))))] = date('F', mktime(0,0,0,$m, 1, date('Y')));
    }
    if( $short_name === false ){
        return $months;
    }else{
        // We might need to get long month name using small month name
        return isset($months[$short_name]) ? $months[$short_name] : ''; 
    }
}

/**
 * Service will be available in Bangladesh and India only.
 * @return array
 */
function get_country_list( $key = false ){
    helper('country_list');
    $list = get_country_list_all();
    
    // Trying to get list, to show in dropdown
    if($key === false){ return $list; }
    
    // Trying to display in page using echo, return string value
    if( array_key_exists($key, $list) ){ return $list[$key]; }
    
    return 'Invalid Country Key';
} /* EOF */


function convert_number_to_words($number) {  
    $hyphen      = '-';
    $conjunction = '  ';
    $separator   = ' ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'Zero',
        1                   => 'One',
        2                   => 'Two',
        3                   => 'Three',
        4                   => 'Four',
        5                   => 'Five',
        6                   => 'Six',
        7                   => 'Seven',
        8                   => 'Eight',
        9                   => 'Nine',
        10                  => 'Ten',
        11                  => 'Eleven',
        12                  => 'Twelve',
        13                  => 'Thirteen',
        14                  => 'Fourteen',
        15                  => 'Fifteen',
        16                  => 'Sixteen',
        17                  => 'Seventeen',
        18                  => 'Eighteen',
        19                  => 'Nineteen',
        20                  => 'Twenty',
        30                  => 'Thirty',
        40                  => 'Fourty',
        50                  => 'Fifty',
        60                  => 'Sixty',
        70                  => 'Seventy',
        80                  => 'Eighty',
        90                  => 'Ninety',
        100                 => 'Hundred',
        1000                => 'Thousand',
        1000000             => 'Million',
        1000000000          => 'Billion',
        1000000000000       => 'Trillion',
        1000000000000000    => 'Quadrillion',
        1000000000000000000 => 'Quintillion'
    );
    if (!is_numeric($number)) { return false; }
   
    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow, accepts numbers between - PHP_INT_MAX and PHP_INT_MAX
        return false;
    }
 
    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }
   
    $string = $fraction = null;
   
    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }
   
    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }
   
    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }
    return $string;
}


/**
 * Status of a student in a class. He might be admitted, passed or rejected from the class or other status.
 * @param boolean $include_empty
 * @return array
 */
function get_student_class_status( bool $include_empty = true ){
    $empty = ['' => ''];
    $student_status_in_a_class = [
        'requested'     => 'Applied',   // Student requested to get admitted to this class/semester
        'whitelisted'   => 'White Listed',  // Admin whitelisted this student for admission consideration
        'exam_phase'   => 'Wait for Exam',  // This student is in exam phase, need to sit in examination like Written/MCQ
        'viva_phase'   => 'Wait for Viva',  // Selected for Viva, need to sit for viva
        'rejected'  => 'Rejected',   // authority rejected admission requests of the students 
        'cancelled' => 'Cancelled',  // student/user cancelled his own admission requests to that institution
        'admitted'  => 'Admitted',   // authority made this user a student of this class/semester
        'passed'    => 'Passed',     // this student passed this course
        'dropped'   => 'Dropped',    // student is dropped from the class or session, or failed
    ];
    
    if( $include_empty ){
        return array_merge( $empty, $student_status_in_a_class );
    }
    return $student_status_in_a_class;
}



/**
* No cdn, use from public directory
*/
function cdn_url($uri = ''): string
{
       $config = \CodeIgniter\Config\Services::request()->config; 
       $cdnURL = ! empty($config->baseURL) && $config->baseURL !== '/'
               ? rtrim($config->baseURL, '/ ') . '/'
               : $config->baseURL;
       
       unset($config);
       return $cdnURL . 'assets/' . ltrim((string) $uri, '/ ');
} // EOF



/**
 * We are replacing codeigniter form helper function. Because it has not escaped fields. As a result it broke 
 * pages if article/notice/events has html tag in title. Now we added esc() function to prevent page breakings.
 */
if (! function_exists('form_dropdown')){
    function form_dropdown($data = '', $options = [], $selected = [], $extra = ''): string
    {
            $defaults = [];
            if (is_array($data))
            {
                    if (isset($data['selected']))
                    {
                            $selected = $data['selected'];
                            unset($data['selected']); // select tags don't have a selected attribute
                    }
                    if (isset($data['options']))
                    {
                            $options = $data['options'];
                            unset($data['options']); // select tags don't use an options attribute
                    }
            }
            else
            {
                    $defaults = ['name' => $data];
            }

            is_array($selected) || $selected = [$selected];
            is_array($options) || $options   = [$options];

            // If no selected state was submitted we will attempt to set it automatically
            if (empty($selected))
            {
                    if (is_array($data))
                    {
                            if (isset($data['name'], $_POST[$data['name']]))
                            {
                                    $selected = [$_POST[$data['name']]];
                            }
                    }
                    elseif (isset($_POST[$data]))
                    {
                            $selected = [$_POST[$data]];
                    }
            }

            $extra    = stringify_attributes($extra);
            $multiple = (count($selected) > 1 && stripos($extra, 'multiple') === false) ? ' multiple="multiple"' : '';
            $form     = '<select ' . rtrim(parse_form_attributes($data, $defaults)) . $extra . $multiple . " custom_dd='dropdown'>\n";
            foreach ($options as $key => $val)
            {
                    $key = (string) $key;
                    if (is_array($val))
                    {
                            if (empty($val))
                            {
                                    continue;
                            }
                            $form .= '<optgroup label="' . $key . "\">\n";
                            foreach ($val as $optgroup_key => $optgroup_val)
                            {
                                    $sel   = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
                                    $form .= '<option value="' . htmlspecialchars($optgroup_key) . '"' . $sel . '>'
                                                    . esc( (string) $optgroup_val) . "</option>\n";
                            }
                            $form .= "</optgroup>\n";
                    }
                    else
                    {
                            $form .= '<option value="' . htmlspecialchars($key) . '"'
                                            . (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
                                            . esc( (string) $val) . "</option>\n";
                    }
            }

            return $form . "</select>\n";
    }
}


if (! function_exists('today_is')){
    function today_is(): string
    {
        if(service('request')->getLocale() == 'bn'){
            return  'আজ ' .['রবিবার','সোমবার','মঙ্গলবার','বুধবার','বৃহস্পতিবার','শুক্রবার','শনিবার'][Date('w')] . ' ' . implode( ' ',  (service('BanglaDateLibrary'))->get_date() ) . ' বঙ্গাব্দ';
        }else{
            return 'Today is ' . Date('l jS F Y');
        }   
    }
}


function get_available_class_exam_options( string $singleIndex = null, string $singleDefault = '' ){
    $lang = (service('request')->getLocale() == 'en') ? 'en' : 'bn';
    
    $options = [
            '1st_mid'   => ($lang == 'en') ? '1st Mid Term' : '১ম সাময়িক',
            '2nd_mid'   => ($lang == 'en') ? '2nd Mid Term' : '২য় সাময়িক', 
            '3rd_mid'   => ($lang == 'en') ? '3rd Mid Term' : '৩য় সাময়িক', 
            '4th_mid'   => ($lang == 'en') ? '4th Mid Term' : '৪র্থ সাময়িক' , 
            'pretest'   => ($lang == 'en') ? 'Pre Test'     : 'প্রাক-যাচাইকরন', 
            'test'      => ($lang == 'en') ? 'Test'         : 'যাচাইকরন', 
            'sem_fin'   => ($lang == 'en') ? 'Semi Final'   : 'আধা চূড়ান্ত',
            'final'     => ($lang == 'en') ? 'Final'        : 'চূড়ান্ত',
            'mcq'       => ($lang == 'en') ? 'MCQ'          : 'নৈব্যাক্তিক', 
            'assign'    => ($lang == 'en') ? 'Assignment'   : 'অ্যাসাইনমেন্ট', 
            'presen'    => ($lang == 'en') ? 'Presentation' : 'উপস্থাপনা',
            'ses_chan'  => ($lang == 'en') ? 'Session Change' : 'সেশন পরিবর্তন',
            'yr_chan'   => ($lang == 'en') ? 'Year Change'  : 'বর্ষ পরিবর্তন',
            'others'    => ($lang == 'en') ? 'Others'       : 'অন্যান্য'
        ];
    
    if(is_null($singleIndex)){
        return $options;   
    }else{
        return array_key_exists($singleIndex, $options) ? $options[$singleIndex] : $singleDefault;
    }
} /* EOF */


/* Return en or bn txt based on language */
function myLang( string $enTxt, string $bnTxt ){
    $lang = (service('request')->getLocale() == 'bn') ? 'bn' : 'en'; // Default Language changed to english
    return ($lang == 'en') ? $enTxt : $bnTxt;
} /* EOF */


if (! function_exists('isMobile')){
    function isMobile(): bool
    {
        return service('request')->getUserAgent()->isMobile(); 
    }
} /* End IF */



function get_exam_grade_by_percent( float $mark_percent, string $type = 'LG'){
    if($mark_percent < 33 ){ return ($type === 'LG') ? 'F' : '0.0'; }
    elseif($mark_percent < 40 ){ return ($type === 'LG') ? 'D' : '1.0'; }
    elseif($mark_percent < 50 ){ return ($type === 'LG') ? 'C' : '2.0'; }
    elseif($mark_percent < 60 ){ return ($type === 'LG') ? 'B' : '3.0'; }
    elseif($mark_percent < 70 ){ return ($type === 'LG') ? 'A-' : '3.5'; }
    elseif($mark_percent < 80 ){ return ($type === 'LG') ? 'A' : '4.0'; }
    else{ return ($type === 'LG') ? 'A+' : '5.0'; }
} /* EOF */



function getSSMSAdminEmail(): string
{
       $config = \CodeIgniter\Config\Services::request()->config;
       $config_value = $config->SSMSAdminEmail;
       unset($config);
       return $config_value;
} // EOF


function getSSMSAdminPassword(): string
{
       $config = \CodeIgniter\Config\Services::request()->config;
       $config_value = $config->SSMSAdminPassword;
       unset($config);
       return $config_value;
} // EOF



function getSchool(){
    $ob = new stdClass();
    $ob->sch_name       = get_option('instNameEn');
    $ob->sch_tagline    = get_option('instNameEn');
    $ob->sch_email      = get_option('schOffEmailAddr');
    $ob->sch_contact    = get_option('schOffPhonNum');
    $ob->sch_eiin       = get_option('schOffSchEiin');
    $ob->sch_address    = implode(',',array_filter([
                                get_option('schOfficialAddressPostCode'),
                                get_option('schOfficialAddressPost'),
                                get_option('schOfficialAddressDistrict'),
                                get_option('schOfficialAddressCountry'),
                            ]));
    return $ob;
} // EOF