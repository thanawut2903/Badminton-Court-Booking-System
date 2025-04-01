<?php 
    $bookingDate = '2025-03-11';
    $currentDate = date('Y-m-d');
    $currentDateObj = new DateTime($currentDate);
    $bookingDateObj = new DateTime($bookingDate);
    $dateDiff = $currentDateObj->diff($bookingDateObj)->days;
    echo ($dateDiff);
    echo '<br>';
    print_r ($currentDate);
    echo '<br>';
    print_r ($bookingDateObj);
?>