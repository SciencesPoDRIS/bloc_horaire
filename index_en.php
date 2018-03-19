<?php

/***
 * Variables
 ***/

$schedule_date_format = 'm/d';
$schedule_url = 'https://api3-eu.libcal.com/api_hours_grid.php?iid=3328&format=json&weeks=2&systemTime=0&lid=5832&t=' . (integer) time();


/***
 * Functions
 ***/

function get_final_schedule($schedule_array, $schedule_date = null) {
    $schedule_date_format = 'm/d';
    $schedule_count_values = array_count_values($schedule_array);
    $schedule_return = key($schedule_count_values);
    $schedule_return_bis = '';
    $schedule_first_open_day = 'Monday ' . date($schedule_date_format, strtotime($schedule_date));
    $schedule_last_open_day = 'Friday ' . date($schedule_date_format, strtotime($schedule_date . ' +4 day'));
    // If there is only one value in this array, return this value
    if($schedule_count_values[$schedule_return] != 5 && !empty($schedule_date)) {
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
$tmp = get_final_schedule($schedule_27rsg_week_close_array);
$schedule_27rsg_week_close = $tmp[0];
// 30 rue Saint Guillaume, Week, Opening hour
$schedule_30rsg_week_open_array = Array(
    'Monday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['from'],
    'Tuesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['from'],
    'Wednesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['from'],
    'Thursday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['from'],
    'Friday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['from']
);
$tmp = get_final_schedule($schedule_30rsg_week_open_array);
$schedule_30rsg_week_open = $tmp[0];
// 30 rue Saint Guillaume, Week, Closing hour
$schedule_30rsg_week_close_array = Array(
    'Monday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Monday']['times']['hours'][0]['to'],
    'Tuesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Tuesday']['times']['hours'][0]['to'],
    'Wednesday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Wednesday']['times']['hours'][0]['to'],
    'Thursday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Thursday']['times']['hours'][0]['to'],
    'Friday' => $schedule_data['loc_5859']['weeks'][$schedule_week_index]['Friday']['times']['hours'][0]['to']
);
$tmp = get_final_schedule($schedule_30rsg_week_close_array);
$schedule_30rsg_week_close = $tmp[0];
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

?>

<div id="entre-etudiant">
    <h2>Students</h2>
    <ul>
        <li><a href="http://sciencespo.libcal.com/booking/salles-travail-groupe" target="_blank">Book a group study room</a></li>
        <li><a href="/bibliotheque/en/use-the-library/open-access-rooms">Open access rooms</a></li>
        <li><a href="en/use-the-library/copy-print">Print, scan and Killprint</a></li>
        <li><a href="http://www.sciencespo.fr/bibliotheque/en/search/eresources">Online newspapers</a></li>
        <li><a href="en/use-the-library/local-campuses">Local Campus</a></li>
    </ul>
</div>
<div id="entre-enseignant">
    <h2>Faculty and researchers</h2>
    <ul>
        <li><a href="en/faculty-researchers/delivery-service">Delivery service</a></li>
        <li><a href="/bibliotheque/en/faculty-researchers/sciencespo-academic-staff">Send a reading list</a></li>
        <li><a href="http://spire.sciencespo.fr/web/?lang=en" target="_blank">Spire, open archive</a></li>
        <li><a href="/bibliotheque/fr/enseignants-chercheurs/trucs-astuces/">Trucs et astuces (FR)</a></li>
        <li><a href="/bibliotheque/en/faculty-researchers/sciencespo-academic-staff">All services</a></li>
    </ul>
</div>
<div id="entre-venir">
    <h2>Visit</h2>
    <ul>
        <?php print $schedule_block; ?>
        <li><a href="http://www.sciencespo.fr/bibliotheque/en/visit/opening-hours">All about opening hours</a></li>
        <li><a href="en/visit/library-access">Library access</a></li>
    </ul>
</div>