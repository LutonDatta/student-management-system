/* 
 * Works in : http://ultra-school.com/258k/daily/attendance/book
 * It performs few works: 
 *  1. Check if class/course is selected. If selected then load students.
 *  2. Load students based on selected class and course.
 *  3. Automatically load more students if teacher scrolls to end of the page.
 *  4. Allows to mark a student is present or absent.
 *  5. Students can be marked (update) as present/absent in 24 hours after first marking. 
 */


document.addEventListener("DOMContentLoaded", function(){
    var tbody = $('tbody#stDPresentAbsentRows');
    first_check_if_class_or_course_is_selected(tbody); // Show warning message if not selected
    // Clear students as course is changed. Show message if needed.
    $('#da_att_course_subject').on('change', function(){
        /*Deselect dropdown, otherwise it might change accedently if page down/up is pressed.*/
        $(this).attr('disabled','disabled'); 
        setTimeout(function(){$('#da_att_course_subject').removeAttr('disabled');},2000);
        course_changed_take_action(tbody);
    });
    
    /* Call to load students when teacher scroll to bottom of the page */
    $(window).scroll(function() {
        if (!document.loadingMoreStudentsFromServer  && $(window).scrollTop() > $(document).height() - $(window).height() - 100){
            document.loadingMoreStudentsFromServer = true;
            load_students_rows_for_attendance();
        }
    });
    
    /*Teacher clicked to the button to mark a student absent or present. */
    $(document).on("click", '.mark_btn_present', function(event){send_request_to_mark_a_student_present(event,this);});
    $(document).on("click", '.mark_btn_absent', function(event){send_request_to_mark_a_student_absent(event,this);});
});

function course_changed_take_action(tbody){
    document.whichPageIsLoadedAjax = 1; /* Tell AJAX to load first page, as we change course.*/
    /* First clear all students/rows if added to tbody. We need new students based on courses. */
    tbody.empty();
    /* Second call this function to show message if class/course is selcted. */
    first_check_if_class_or_course_is_selected(tbody);
}

function first_check_if_class_or_course_is_selected(tbody){
    var show_messag = '';
    var attan_class = $('#da_att_selected_class').val();
    var attan_sessi = $('#att_sess_yrs_for_atdnce').val();    
    var attan_cours = $('#da_att_course_subject').val();
    
    if( attan_class.length < 1 ){ show_messag += 'Please select a class. No class is selected.'; }
    if( attan_sessi.length < 1 ){ show_messag += ' Please select a session/year.'; }
    if( attan_cours.length < 1 ){ show_messag += ' Please select a course/subject. No course/subject is selected.'; }
    
    if( attan_class.length < 1 || attan_sessi.length < 1 || attan_cours.length < 1 ){
        show_attendance_warning_msg(show_messag);
        return false; /* Do not try to load students. */
    }
    tbody.empty();
    
    /* Load students rows if checking is done. */
    load_students_rows_for_attendance(tbody);
} /* EOF */


function load_students_rows_for_attendance(){
    show_spiner_in_table();
    
    jQuery.ajaxSetup({ headers: { api_ajax_setup_csrf_header: api_ajax_setup_csrf_hash } }); 
    
    var last_row_roll = jQuery('tr.adStdRollAttendView:last').data('roll'); last_row_roll = last_row_roll ? last_row_roll : 0;
    var sele_class_id = $('#da_att_selected_class').val();
    var session_year  = $('#att_sess_yrs_for_atdnce').val();
    var se_subject_id = $('#da_att_course_subject').val();
    var page = document.whichPageIsLoadedAjax;
        
    jQuery.ajax({
        url: api_attendance_url_load_students,
        method: 'POST',
        dataType: 'json', /* Expecting from server */
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        xhrFields: { withCredentials: true },
        data: {'page':page,'last_row_roll': last_row_roll,'sele_class_id':sele_class_id,'session_year':session_year,'se_subject_id':se_subject_id}
    }).done(function(dta, textStatus, jqXHR){
        document.whichPageIsLoadedAjax = document.whichPageIsLoadedAjax + 1; /* Tell AJAX to load next page. As we loaded this page properly. */
        
        jQuery.each(dta, function(index, val){
            var isPreStat = (val.dab_is_present === '1') ? "<div><i class='fa fa-check text-navy' title='Present'></i> Present</div>" : ((val.dab_is_present === '0') ? "<div><i class='fa fa-times text-danger' title='Absent'></i> Absent</div>": '');
            
            var tr = "<tr class='adStdRollAttendView studentrow-"+val.scm_id+"' ";
                        tr += "data-roll='"+val.scm_c_roll+"' ";
                        tr += "data-classid='"+val.scm_class_id+"' ";
                        tr += "data-courseid='"+val.dab_course_id+"' ";
                        tr += "data-schid='"+val.scm_sch_id+"' ";
                        tr += "data-scmid='"+val.scm_id+"' ";
                        tr += "data-sessyear='"+val.scm_session_year+"' ";
                        tr += "data-uid='"+val.scm_u_id+"'>";
                tr += "<td><img width='40' src='"+val.thumb+"' alt='' class='m-0 p-0'></td>";
                tr += "<td>"+val.p_name+"</td>";
                tr += "<td>"+val.scm_c_roll+"</td>";
                tr += "<td class='bothButtons'>";
                if(val.is_attendance_updatable){
                    tr += "<button type='button' class='btn btn-info mark_btn_present'>";
                        tr += "<div class='spinner-border spinner-border-sm mark_spinner_present d-none' role='status'></div> Mark Present";
                    tr += "</button>";
                    tr += "<button type='button' class='btn btn-warning mark_btn_absent'>";
                        tr += "<div class='spinner-border spinner-border-sm mark_spinner_absent d-none' role='status'></div> Mark Absent";
                    tr += "</button>";
                    tr += "<div class='markingMsgs'></div>";
                }else{
                    tr += "Not allowed";
                }
                tr += "</td>";
                tr += "<td class='attStatus'>"+isPreStat+"</td>";
                tr += "<td>"+val.scm_u_id+"</td>";
            tr += "</tr>";

            $('tbody#stDPresentAbsentRows').append(tr);
            
            if(val.dab_is_present === '1'){ $("tr.studentrow-"+val.scm_id).find('button.mark_btn_present').addClass('d-none');}
            if(val.dab_is_present === '0'){ $("tr.studentrow-"+val.scm_id).find('button.mark_btn_absent').addClass('d-none');}
        });
    }).always(function(jqXHR, textStatus){
        hide_spiner_from_table();
        
        if(jqXHR.status === 0 ){show_attendance_warning_msg('Failed to connect to the server. May be internet connection error.');}
        if(jqXHR.status === 500 ){show_attendance_warning_msg('Internal server error. Something error happened in the server.');}
        if(jqXHR.status === 401 ){show_attendance_warning_msg('Unauthorized! Please login with right permission. Please check if your session time expired, please login again.');}
        if(textStatus==='parsererror'){show_attendance_warning_msg('Server returned wrong data. It might be 404 error.');}
        // show_attendance_warning_msg('Load More Students','success');
        if(jQuery.isArray(jqXHR)){
            /* Is success? and is not data to retrieve? We have loaded all rows, disable button. */
            if(jqXHR.length < 1){ 
                show_attendance_warning_msg('All roll numbers are showing here. Nothing to load from server.','info'); 
                /* Stop loader function permanently to load students from server. As we do not have any students to load from. */
                document.loadingMoreStudentsFromServer = true; 
            }else{
                /* Allow to call loader function again to load students from server. */
                document.loadingMoreStudentsFromServer = false; 
            }
        }
    });   
} /* EOF */



function send_request_to_mark_a_student_present(eEvent,eThis){
    show_spiner_for_marking_request(eEvent,eThis);
    jQuery.ajaxSetup({ headers: { api_ajax_setup_csrf_header: api_ajax_setup_csrf_hash } }); 
    
    var $row            = $(eThis).closest("tr");
    var $row_roll       = $row.data('roll');
    var $row_class_id   = $row.data('classid');
    var $row_course_id  = $row.data('courseid');
    var $row_sch_id     = $row.data('schid');
    var $row_sess_year  = $row.data('sessyear');
    var $row_uid        = $row.data('uid');
    var $row_scmid      = $row.data('scmid');
    
    jQuery.ajax({
        url: api_attendance_url_change_status,
        method: 'POST',
        dataType: 'json', /* Expecting from server */
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        xhrFields: { withCredentials: true },
        data: {
            'attendance_roll':$row_roll,'attendance_class_id':$row_class_id,
            'attendance_course_id':$row_course_id,'attendance_sch_id':$row_sch_id,
            'attendance_session_year':$row_sess_year,'attendance_uid':$row_uid,
            'attendance_status':'present', 'attendance_scmid':$row_scmid
        }
    }).done(function(dta, textStatus, jqXHR){
        if(dta.hasOwnProperty('is_attendance_updatable')){
            if( ! dta.is_attendance_updatable){
                $($(eThis).closest('td').find('button.mark_btn_present')).hide(); $(eThis).closest('td').find('button.mark_btn_absent').hide();
            }
        }
        if(dta.hasOwnProperty('error')){
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-times text-danger'></i> " + dta.error);
        }else{
            var presentHtml = "<div><i class='fa fa-check text-navy' title='Present'></i> Present</div>";
            $('tbody#stDPresentAbsentRows').find('tr.studentrow-' + dta.scm_id ).find('td.attStatus').html(presentHtml);
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-check text-navy'></i> Status updated.");
                        
            if(dta.m_present === 'p'){ $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_absent').removeClass('d-none'); $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_present').addClass('d-none');}
            if(dta.m_present === 'a'){ $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_absent').addClass('d-none'); $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_present').removeClass('d-none');}
        }
    }).always(function(jqXHR, textStatus){
        hide_spiner_for_marking_request(eEvent,eThis);
        
        if(jqXHR.status === 0 ){var ajaxEqErr = 'Failed to connect to the server. May be internet connection error.';}
        if(jqXHR.status === 500 ){var ajaxEqErr = 'Internal server error. Something error happened in the server.';}
        if(jqXHR.status === 401 ){var ajaxEqErr = 'Unauthorized! Please login with right permission. Please check if your session time expired, please login again.';}
        if(textStatus==='parsererror'){var ajaxEqErr = 'Server returned wrong data. It might be 404 error.';}
        
        if(typeof(jqXHR.responseJSON) !== 'undefined'){
            if( typeof(jqXHR.responseJSON.message) !== 'undefined' && jqXHR.responseJSON.message.length > 0){
                if( typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
                    var ajaxEqErr = ajaxEqErr + ' ' + jqXHR.responseJSON.message;
                }else{
                    var ajaxEqErr = jqXHR.responseJSON.message;
                }
            }
        }
        if( typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-times text-danger'></i> " + ajaxEqErr);
        }
    });  
}

function send_request_to_mark_a_student_absent(eEvent,eThis){
    show_spiner_for_marking_request(eEvent,eThis);
    jQuery.ajaxSetup({ headers: { api_ajax_setup_csrf_header: api_ajax_setup_csrf_hash } }); 
    
    var $row            = $(eThis).closest("tr");
    var $row_roll       = $row.data('roll');
    var $row_class_id   = $row.data('classid');
    var $row_course_id  = $row.data('courseid');
    var $row_sch_id     = $row.data('schid');
    var $row_sess_year  = $row.data('sessyear');
    var $row_uid        = $row.data('uid');
    var $row_scmid      = $row.data('scmid');
    
    jQuery.ajax({
        url: api_attendance_url_change_status,
        method: 'POST',
        dataType: 'json', /* Expecting from server */
        contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
        xhrFields: { withCredentials: true },
        data: {
            'attendance_roll':$row_roll,'attendance_class_id':$row_class_id,
            'attendance_course_id':$row_course_id,'attendance_sch_id':$row_sch_id,
            'attendance_session_year':$row_sess_year,'attendance_uid':$row_uid,
            'attendance_status':'absent', 'attendance_scmid':$row_scmid
        }
    }).done(function(dta, textStatus, jqXHR){   
        if(dta.hasOwnProperty('is_attendance_updatable')){
            if( ! dta.is_attendance_updatable){
                $($(eThis).closest('td').find('button.mark_btn_absent')).hide(); $(eThis).closest('td').find('button.mark_btn_present').hide();
            }
        }
        if(dta.hasOwnProperty('error')){
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-times text-danger'></i> " + dta.error);
        }else{
            var absentHtml = "<div><i class='fa fa-times text-danger' title='Absent'></i> Absent</div>";
            $('tbody#stDPresentAbsentRows').find('tr.studentrow-' + dta.scm_id ).find('td.attStatus').html(absentHtml);
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-check text-navy'></i> Status updated.");
            
            if(dta.m_present === 'p'){ $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_absent').removeClass('d-none'); $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_present').addClass('d-none');}
            if(dta.m_present === 'a'){ $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_absent').addClass('d-none'); $("tr.studentrow-"+dta.scm_id).find('button.mark_btn_present').removeClass('d-none');}
        }
    }).always(function(jqXHR, textStatus){
        hide_spiner_for_marking_request(eEvent,eThis);
        
        if(jqXHR.status === 0 ){var ajaxEqErr = 'Failed to connect to the server. May be internet connection error.';}
        if(jqXHR.status === 500 ){var ajaxEqErr = 'Internal server error. Something error happened in the server.';}
        if(jqXHR.status === 401 ){var ajaxEqErr = 'Unauthorized! Please login with right permission. Please check if your session time expired, please login again.';}
        if(textStatus==='parsererror'){var ajaxEqErr = 'Server returned wrong data. It might be 404 error.';}
        if(typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
            $(eThis).closest('td').find('div.markingMsgs').html("<i class='fa fa-times text-danger'></i> " + ajaxEqErr);
        }        
        if(typeof(jqXHR.responseJSON) !== 'undefined'){
            if( typeof(jqXHR.responseJSON.message) !== 'undefined' && jqXHR.responseJSON.message.length > 0){
                if( typeof(ajaxEqErr) !== 'undefined' && ajaxEqErr.length > 0){
                    var ajaxEqErr = ajaxEqErr + ' ' + jqXHR.responseJSON.message;
                }else{
                    var ajaxEqErr = jqXHR.responseJSON.message;
                }
            }
        }
    });  
} /* EOF */


function show_spiner_for_marking_request(xEvent,xThis){
    $(xThis).find('.spinner-border').removeClass('d-none');
    $(xThis).closest('td').find('button').attr('disabled','disabled');
}

function hide_spiner_for_marking_request(xEvent,xThis){
    $(xThis).find('.spinner-border').addClass('d-none');
    $(xThis).closest('td').find('button').removeAttr('disabled');
}

function show_attendance_warning_msg(w_msg, w_css_cls = 'warning'){
    $('tbody#stDPresentAbsentRows').append("<tr class='attendance_warning_msg'><td colspan='6' class='text-center label-"+w_css_cls+"'>"+w_msg+"</td></tr>");
}

function hide_attendance_warning_msg(){
    $('tbody#stDPresentAbsentRows > tr.attendance_warning_msg').remove();
}

function show_spiner_in_table(){
    $('tbody#stDPresentAbsentRows').append("<tr id='attendance_sts_tr_row_spiner'><td colspan='6' class='text-center'><div class='spinner-border spinner-border-lg' role='status'></div></td></tr>");
}

function hide_spiner_from_table(){
    $('table > tbody > tr#attendance_sts_tr_row_spiner').remove();
}
