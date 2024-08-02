/* 
 * Works in : http://ultra-school.com/258k/daily/attendance/book/view
 * It performs few works: 
 *  1. Check if class/course is selected. If selected then load students.
 *  2. Load students based on selected class and course.
 *  3. Automatically load more students if teacher scrolls to end of the page.
 *  4. Allows to mark a student is present or absent.
 *  5. Students can be marked (update) as present/absent in 24 hours after first marking. 
 */

document.addEventListener("DOMContentLoaded", function(){
    aTtViewerDataTable = $('#mainAttendanceViewerTable').dataTable({
        "dom" : 'lrtip', /* Remove filter input box */
        buttons: [{
            extend: 'print',
            text: 'Print',
            autoPrint: true
        }],
        "language": {"processing": "<div class='spinner-border spinner-border-sm' role='status'><span class='sr-only'>Loading...</span></div> Loading. Please wait..."},
        "searching": true, /* Allow searching based on our input fields */
        "lengthMenu": [ 10, 25, 50, 75, 100 ], /* Show x number of rows*/
        "processing": true, /* Shows a processing text */
        "serverSide": true, /* All processing is made on server */
        search: { return: true }, /* When press enter then searching will be started to reduce load on each key stroke*/
        "ajax": {
            "url": api_attendance_viwer_url,
            "type": "POST",
            "data": function(d){
                d.atViFilter_cla_id = $('#atViFilter_cla_id').val();
                d.atViFilter_coSuID = $('#atViFilter_coSuID').val();
                d.atViFilter_sessYr = $('#atViFilter_sessYr').val();
                d.atViFilter_stu_id = $('#atViFilter_stu_id').val();
                d.atViFilter_c_roll = $('#atViFilter_c_roll').val();
                d.atViFilter_v_date = $('#atViFilter_v_date').val();
                d.atViFilter_v_mnth = $('#atViFilter_v_mnth').val();
                d.atViFilter_v_year = $('#atViFilter_v_year').val();
            },
        },
        columns: [
            {"data": "uid","name": "uid","class": "m-0 p-0 text-center"},
            {"data": "thumb","name": "thumb","class": "m-0 p-0"},
            {"data": "name","name": "name","class": "m-0 p-0"},
            {"data": "roll","name": "roll","class": "m-0 p-0"},
            {"data": "class_name","name": "class_name","class": "m-0 p-0"},
            {"data": "course_name","name": "course_name","class": "m-0 p-0"},
            {"data": "is_present","name": "is_present","class": "m-0 p-0"},
            {"data": "date","name": "date","class": "m-0 p-0"},
            {"data": "action","name": "action","class": "d-print-none"},
        ],
    });
    
    /* Add some event to search attendance rows based on input filter */
    $('#atViFilter_cla_id').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_coSuID').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_sessYr').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_stu_id').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_c_roll').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_v_date').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_v_mnth').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    $('#atViFilter_v_year').on('change', function(){ aTtViewerDataTable.dataTable().fnFilter( this.value ); });
    
});

