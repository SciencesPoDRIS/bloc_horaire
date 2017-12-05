<?php

// TODO
// 1. Erreur PHP en PPRD
// 2. French date should be formatted into dd/mm/yyyy ?

/***
 * Variables
 ***/

$schedule_date_format = 'm-d-Y';
$schedule_url = 'https://api3-eu.libcal.com/api_hours_grid.php?iid=3328&format=json&weeks=2&systemTime=0&lid=5832';
// $schedule_url = 'data_en.json';


/***
 * Functions
 ***/

function get_final_schedule($schedule_array, $schedule_date = null) {
    global $schedule_date_format;
    $schedule_return = key(array_count_values($schedule_array));
    $schedule_return_bis = '';
    $schedule_first_open_day = 'Monday ' . date($schedule_date_format, strtotime($schedule_date));
    $schedule_last_open_day = 'Friday ' . date($schedule_date_format, strtotime($schedule_date . ' +4 day'));
    // If there is only one value in this array, return this value
    if(array_count_values($schedule_array)[$schedule_return] != 5 && !empty($schedule_date)) {
        // If there is a holiday in this week
        // If the librairies are closed on Monday
        if($schedule_array['Monday'] != $schedule_return) {
            $schedule_return_bis .= '<li>Warning, closed on Monday ' . date($schedule_date_format, strtotime($schedule_date)) . '.</li>';
            $schedule_first_open_day = 'Tuesday ' . date($schedule_date_format, strtotime($schedule_date . ' +1 day'));
        }
        // If the librairies are closed on Tuesday
        if($schedule_array['Tuesday'] != $schedule_return) {
            $schedule_return_bis .= '<li>Warning, closed on Tuesday ' . date($schedule_date_format, strtotime($schedule_date . ' +1 day')) . '.</li>';
            // If the libraries are also closed on Monday
            if($schedule_array['Monday'] != $schedule_return) {
                $schedule_first_open_day = 'Wednesday ' . date($schedule_date_format, strtotime($schedule_date . ' +2 day'));
            }
            // If the librairies are also closed on Wednesday, Thursday and Friday
            if(($schedule_array['Wednesday'] != $schedule_return) and ($schedule_array['Thursday'] != $schedule_return) and ($schedule_array['Friday'] != $schedule_return)) {
                $schedule_last_open_day = 'Monday ' . date($schedule_date_format, strtotime($schedule_date));
            }
        }
        // If the librairies are closed on Wednesday
        if($schedule_array['Wednesday'] != $schedule_return) {
            $schedule_return_bis .= '<li>Warning, closed on Wednesday ' . date($schedule_date_format, strtotime($schedule_date . ' +2 day')) . '.</li>';
            // If the librairies are also closed on Monday and Tuesday
            if(($schedule_array['Monday'] != $schedule_return) && ($schedule_array['Tuesday'] != $schedule_return)) {
                $schedule_first_open_day = 'Thursday ' . date($schedule_date_format, strtotime($schedule_date . ' +3 day'));
            }
            // If the librairies are also closed on Thursday and Friday
            if(($schedule_array['Thursday'] != $schedule_return) and ($schedule_array['Friday'] != $schedule_return)) {
                $schedule_last_open_day = 'Tuesday ' . date($schedule_date_format, strtotime($schedule_date . ' +1 day'));
            }
        }
        // If the librairies are closed on Thursday
        if($schedule_array['Thursday'] != $schedule_return) {
            $schedule_return_bis .= '<li>Warning, closed on Thursday ' . date($schedule_date_format, strtotime($schedule_date . ' +3 day')) . '.</li>';
            // If the librairies are also closed on Monday, Tuesday and Wednesday
            if(($schedule_array['Monday'] != $schedule_return) && ($schedule_array['Tuesday'] != $schedule_return) && ($schedule_array['Wednesday'] != $schedule_return)) {
                $schedule_first_open_day = 'Friday ' . date($schedule_date_format, strtotime($schedule_date . ' +4 day'));
            }
            // If the librairies are also closed on Friday
            if($schedule_array['Friday'] != $schedule_return) {
                $schedule_last_open_day = 'Wednesday ' . date($schedule_date_format, strtotime($schedule_date . ' +2 day'));
            }
        }
        // If the librairies are closed on Friday
        if($schedule_array['Friday'] != $schedule_return) {
            $schedule_return_bis .= '<li>Warning, closed on Friday ' . date($schedule_date_format, strtotime($schedule_date . ' +4 day')) . '.</li>';
            $schedule_last_open_day = 'Thursday ' . date($schedule_date_format, strtotime($schedule_date . ' +3 day'));
        }
    }
    return Array($schedule_return, $schedule_return_bis, $schedule_first_open_day, $schedule_last_open_day);
}

// This function return True if today is sunday and I can stay all day long in my bed
// Else return false
function is_today_sunday() {
    if (date('N') == 7) {
        return True;
    } else {
        return False;
    }
}


/***
 * Script / Main
 ***/

// Collect data from Libcal API
$schedule_data = json_decode(file_get_contents($schedule_url), TRUE);
// Set week index
$schedule_week_index = (is_today_sunday() ? 1 : 0);

// Grab interesting data
// Week
// 27 rue Saint Guillaume, Week, Opening hour
$schedule_27rsg_week_open_array = Array(
    'Monday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['from'],
    'Tuesday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['from'],
    'Wednesday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['from'],
    'Thursday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['from'],
    'Friday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['from']
);
list($schedule_27rsg_week_open, $schedule_27rsg_week_message, $schedule_first_open_day, $schedule_last_open_day) = get_final_schedule($schedule_27rsg_week_open_array, $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Monday']['date']);
// 27 rue Saint Guillaume, Week, Closing hour
$schedule_27rsg_week_close_array = Array(
    'Monday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['to'],
    'Tuesday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['to'],
    'Wednesday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['to'],
    'Thursday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['to'],
    'Friday' => $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['to']
);
$schedule_27rsg_week_close = get_final_schedule($schedule_27rsg_week_close_array)[0];
// 30 rue Saint Guillaume, Week, Opening hour
$schedule_30rsg_week_open_array = Array(
    'Monday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['from'],
    'Tuesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['from'],
    'Wednesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['from'],
    'Thursday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['from'],
    'Friday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['from']
);
$schedule_30rsg_week_open = get_final_schedule($schedule_30rsg_week_open_array)[0];
// 30 rue Saint Guillaume, Week, Closing hour
$schedule_30rsg_week_close_array = Array(
    'Monday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['to'],
    'Tuesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['to'],
    'Wednesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['to'],
    'Thursday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['to'],
    'Friday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['to']
);
$schedule_30rsg_week_close = get_final_schedule($schedule_30rsg_week_close_array)[0];
// 27 rue Saint Guillaume, Saturday, Opening hour
$schedule_27rsg_saturday_open = $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Saturday']['times']['hours'][0]['from'];
// 27 rue Saint Guillaume, Saturday, Closing hour
$schedule_27rsg_saturday_close = $schedule_data['loc_5858']['weeks'][$schedule_week_index]['Saturday']['times']['hours'][0]['to'];
// 30 rue Saint Guillaume, Saturday, Opening hour
$schedule_30rsg_saturday_open = $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Saturday']['times']['hours'][0]['from'];
// 30 rue Saint Guillaume, Saturday, Closing hour
$schedule_30rsg_saturday_close = $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Saturday']['times']['hours'][0]['to'];

// Build the schedules bloc
$schedule_block = "<li>$schedule_first_open_day to $schedule_last_open_day:</li>";
$schedule_block .= "<li>$schedule_27rsg_week_open - $schedule_27rsg_week_close (27SG) | $schedule_30rsg_week_open - $schedule_30rsg_week_close (30SG)</li>";
// If the library is closed on saturday
if(empty($schedule_27rsg_saturday_open)) {
    $schedule_27rsg_week_message .= '<li>Warning, closed on Saturday ' . date($schedule_date_format, strtotime($schedule_data['loc_5858']['weeks'][$schedule_week_index]['Saturday']['date'])) . '.</li>';
} else {
    $schedule_block .= '<li>Saturday ' . date($schedule_date_format, strtotime($schedule_data['loc_5858']['weeks'][$schedule_week_index]['Saturday']['date'])) . ':</li>';
    $schedule_block .= "<li>$schedule_27rsg_saturday_open - $schedule_27rsg_saturday_close (27SG) | $schedule_30rsg_saturday_open - $schedule_30rsg_saturday_close (30SG)</li>";
}
if(!empty($schedule_27rsg_week_message)) {
    $schedule_block .= "$schedule_27rsg_week_message";
}

// Build the block content in HTML
$schedule_html = '';
$schedule_html .= '<div id="entre-etudiant">';
$schedule_html .= '<h2>Students</h2>';
$schedule_html .= '<ul>';
$schedule_html .= '<li><a href="http://sciencespo.libcal.com/booking/salles-travail-groupe" target="_blank">Book a group study room</a></li>';
$schedule_html .= '<li><a href="en/use-the-library/copy-print">Print, scan and Killprint</a></li>';
$schedule_html .= '<li><a href="http://www.sciencespo.fr/bibliotheque/en/search/eresources">Online newspapers</a></li>';
$schedule_html .= '<li><a href="en/use-the-library/local-campuses">Local Campus</a></li>';
$schedule_html .= '<li><a href="http://www.sciencespo.fr/bibliotheque/en/ask-us/surveys/libqual/Libqual2017">Libqual+ 2017 Survey</a></li>';
$schedule_html .= '</ul>';
$schedule_html .= '</div>';
$schedule_html .= '<div id="entre-enseignant">';
$schedule_html .= '<h2>Faculty and researchers</h2>';
$schedule_html .= '<ul>';
$schedule_html .= '<li><a href="en/faculty-researchers/delivery-service">Delivery service</a></li>';
$schedule_html .= '<li><a href="http://www.sciencespo.fr/ecole-doctorale/en/content/graduate-school-library" target="_blank">Research library (FR)</a></li>';
$schedule_html .= '<li><a href="http://spire.sciencespo.fr/web/?lang=en" target="_blank">Spire, open archive</a></li>';
$schedule_html .= '<li><a href="https://docs.google.com/a/sciencespo.fr/forms/d/e/1FAIpQLSfVnVnYZIW8QpVm5p8TEjoQQccdLHDdThJa-jkj7Q_tqGqIwQ/viewform" target="_blank">Digitizing on request (FR)</a></li>';
$schedule_html .= '<li><a href="en/faculty-researchers/research-data-management">Research data management (FR)</a></li>';
$schedule_html .= '<li><a href="/bibliotheque/fr/rechercher/trucs-astuces">Trucs et astuces (FR)</a></li>';
$schedule_html .= '</ul>';
$schedule_html .= '</div>';
$schedule_html .= '<div id="entre-venir">';
$schedule_html .= '<h2>Visit</h2>';
$schedule_html .= '<ul>';
$schedule_html .= $schedule_block;
$schedule_html .= '<li><a href="http://www.sciencespo.fr/bibliotheque/en/visit/opening-hours">All about opening hours</a></li>';
$schedule_html .= '<li><a href="en/visit/library-access">Library access</a></li>';
$schedule_html .= '</ul>';
$schedule_html .= '</div>';

// Display the whole block
print $schedule_html;

?>
