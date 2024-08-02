<?php namespace App\Models;

use CodeIgniter\Model;

class Hand_cash_collections_Model extends Model{
    protected $table            = 'hand_cash_collections';
    protected $primaryKey       = 'hc_id';
    protected $returnType       = 'object';
    
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $skipValidation   = false;
    protected $createdField     = 'hc_inserted_at';
    protected $updatedField     = 'hc_updated_at';
    protected $deletedField     = 'hc_deleted_at';
    
    protected $allowedFields    = [
        'hc_scm_id','hc_salary_months_txt','hc_is_paid',
        'hc_amt_salary','hc_amt_electricity_fee','hc_amt_ict_fee','hc_amt_welcome_fee',
        'hc_amt_farewell_fee','hc_amt_girls_guides_fee','hc_amt_printing_fee','hc_amt_sports_fee',
        'hc_amt_lab_fee','hc_amt_teacher_welfare_fee','hc_amt_milad_puja_fee','hc_amt_development_fee',
        'hc_amt_poverty_fund_fee','hc_amt_reading_room_fee','hc_amt_cultural_program','hc_amt_garden_fee',
        'hc_amt_common_room_fee','hc_amt_session_fee','hc_amt_id_fee','hc_amt_total','hc_deleted_at'
    ];

    protected $validationRules  = [
        'hc_scm_id'                 => ['label' => 'SCM Class ID',      'rules' => 'required|intval|min_length[1]|max_length[15]'],
        'hc_salary_months_txt'      => ['label' => 'Salary Months',     'rules' => 'permit_empty|min_length[2]|max_length[250]'],
        'sr_is_paid'                => ['label' => 'Is paid',           'rules' => 'required|in_list[0,1]'],
        
        'hc_amt_salary'             => ['label' => 'Salary Amount',             'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_electricity_fee'    => ['label' => 'Electricity Fee Amount',    'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_ict_fee'            => ['label' => 'ICT Fee Amount',            'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_welcome_fee'        => ['label' => 'Entrance Fee Amount',       'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_farewell_fee'       => ['label' => 'Farewell Fee Amount',       'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_girls_guides_fee'   => ['label' => 'Girls Guides Fee Amount',   'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_printing_fee'       => ['label' => 'Printing Fee Amount',       'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_sports_fee'         => ['label' => 'Sports Fee Amount',         'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_lab_fee'            => ['label' => 'Lab Fee Amount',            'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_teacher_welfare_fee'=> ['label' => 'Teacher Welfare Fee Amount','rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_milad_puja_fee'     => ['label' => 'Milad Puja Fee Amount',     'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_development_fee'    => ['label' => 'Development Fee Amount',    'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_poverty_fund_fee'   => ['label' => 'Poverty Fund Amount',       'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_reading_room_fee'   => ['label' => 'Reading Room Fee Amount',   'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_cultural_program'   => ['label' => 'Culture Program Fee Amount','rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_garden_fee'         => ['label' => 'Garden Fee Amount',         'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_common_room_fee'    => ['label' => 'Common Room Fee Amount',    'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_session_fee'        => ['label' => 'Session Fee Amount',        'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_id_fee'             => ['label' => 'ID Fee Amount',             'rules' => 'permit_empty|decimal|greater_than[1]|less_than[10000]'],
        'hc_amt_total'              => ['label' => 'Total Amount',              'rules' =>     'required|decimal|greater_than[9]|less_than[10000]'],
    ];
    
    public function getTableName( $withPrefix = false ){
        return $withPrefix ? $this->db->prefixTable($this->table) : $this->table;
    } /* EOM  */
    
    public function get_showable_column_names(){
        return [
            // These will be showin in form, are not equal to $validationRules columns
            'hc_amt_salary'             => [ 'lbl' => myLang('Monthly Salary/Tuition Fee','মাসিক বেতন/টিউশন ফি'), ],
            'hc_amt_electricity_fee'    => [ 'lbl' => myLang('Electricity Fee','বিদ্যুৎ ফি'), ],
            'hc_amt_ict_fee'            => [ 'lbl' => myLang('ICT Fee','আই.সি.টি ফি '), ],
            'hc_amt_farewell_fee'       => [ 'lbl' => myLang('Farewell/Nobin Boron/Bidai Fee','নবীন বরণ ও বিদায় অনুষ্ঠান ফি '), ],
            'hc_amt_girls_guides_fee'   => [ 'lbl' => myLang('Girls Guides Fee','গার্লস গাইডস'), ],
            'hc_amt_printing_fee'       => [ 'lbl' => myLang('Printing (Progree Report/Attendance/Cyllabus) Fee','মুদ্রণ (প্রগতি পত্র, হাজিরা পত্র, সিলেবাস)'), ],
            'hc_amt_sports_fee'         => [ 'lbl' => myLang('Sports Fee','ক্রীড়া ফি'), ],
            'hc_amt_lab_fee'            => [ 'lbl' => myLang('Lab Fee','বিজ্ঞানাগার ফি'), ],
            'hc_amt_teacher_welfare_fee'=> [ 'lbl' => myLang('Teacher Welfare Fee','শিক্ষক কল্যাণ ফি'), ],
            'hc_amt_milad_puja_fee'     => [ 'lbl' => myLang('Milad/Puja Fee','মিলাদ/পূজা'), ],
            'hc_amt_development_fee'    => [ 'lbl' => myLang('Development Fee','উন্নয়ন ফি'), ],
            'hc_amt_poverty_fund_fee'   => [ 'lbl' => myLang('Poverty Fund Fee','দারিদ্র তহবিল'), ],
            'hc_amt_reading_room_fee'   => [ 'lbl' => myLang('Pathagar/Reading Room Fee','পাঠাগার ফি'), ],
            'hc_amt_cultural_program'   => [ 'lbl' => myLang('Cultural Program Fee','সাংস্কৃতিক অনুষ্ঠান'), ],
            'hc_amt_garden_fee'         => [ 'lbl' => myLang('Garden Fee','বাগান উন্নয়ন ফি'), ],
            'hc_amt_common_room_fee'    => [ 'lbl' => myLang('Common Room Fee','কমন রুম ফি'), ],
            'hc_amt_session_fee'        => [ 'lbl' => myLang('Session Fee','সেশন ফি'), ],
            'hc_amt_id_fee'             => [ 'lbl' => myLang('ID Fee','পরিচয় পত্র'), ],
        ];
    } /* EOM */
    
    
    
        
    /**
     * Filter transaction based on time
     * @param object $request
     */
    public function time_filter($request){
        // CAUTION: Called from multiple places
        $filterTimeStart    = $request->getGetPost('timeHcRangeStart');
        $filterTimeStop     = $request->getGetPost('timeHcRangeEnd');
        // Apply time filter based on update time, insert time might not work if updated later
        if(strlen($filterTimeStart) > 6 )   $this->where('hc_updated_at > ', $filterTimeStart );
        if(strlen($filterTimeStop) > 6 )    $this->where('hc_updated_at < ', $filterTimeStop );
        return $this;
    } // EOM
    
    
} /*EOC*/