<!DOCTYPE html>
<html>
<meta charset="UTF-8">
<head>
<title>Booking Table</title>
</head>
<body onload="colorBookingCell()">

<?php
	$nCourt = 5;
	$closedCourt = array(0);
	$courtStart = 11;
	$courtEnd = 24;
	// การจองที่มีสถานะ จองแล้ว
	$confirmBooking = array(
		array( 3, 20, 21 ), // สนาม 4
		array( 4, 20, 21 )	// สนาม 5
	);
	$booking = array( 
		array( 11, 12, "รออนุมัติ" ), array( 11, 12, "รออนุมัติ" ), array( 11, 12, "รอชำระเงิน" ),
		array( 15, 17, "รออนุมัติ" ), array( 18, 22, "รออนุมัติ" ), array( 20, 22, "รออนุมัติ" )
	);
	$currentBooking = array( 18, 22, "รออนุมัติ" );
	
	// draw a table
	echo "<table border='1' id='myTable'><tr bgcolor='black'><td style='color:white'>สนาม/เวลา</td>";
		for( $j = $courtStart; $j < $courtEnd; $j++ ){
			echo "<td style='color:white' align='center'>".$j."-",($j+1)."</td>";
		}
	echo "</tr>";
	
	$iClosedCourt = 0;
	$nClosedCourt = count($closedCourt);
	for( $i = 0; $i < $nCourt; $i++ ){
		echo "<tr>";
		echo "<td>สนามที่ ".($i+1)."</td>";
		if( $i == $closedCourt[$iClosedCourt] ){
			$color = "#7f8c8d";
			$text = "สนามปิด";
		}else{
			$color = "green";
			$text = "";
		}
		
		for( $j = $courtStart; $j < $courtEnd; $j++ ){
			echo "<td align='center' bgcolor='$color'>$text</td>";
		}
		echo "</tr>";		
	}
	echo "</table>";

		
?>
</body>
<script type="text/javascript">
	function colorBookingCell(){
		let bCurrentBooking = false;
		let nCourt = <?= $nCourt?>;
		let booking = [<?php 
			foreach( $booking as $item ){
				echo "[".$item[0].",".$item[1].",'".$item[2]."'], ";
			}
		?>];
		let confirmBooking = [<?php 
			foreach( $confirmBooking as $item ){
				echo "[".$item[0].",".$item[1].",".$item[2]."], ";
			}
		?>];		
		let currentBooking = [<?php echo $currentBooking[0].",".$currentBooking[1].",'".$currentBooking[2]."'" ?>];
		
		// process confirmed bookings
		for( let i = 0; i < confirmBooking.length; i++ ){
			colorConfirmedCell( confirmBooking[i][0], confirmBooking[i][1], confirmBooking[i][2] );
		}
			
		for( let i = 0; i < booking.length; i++ ){
			if( bCurrentBooking == false ){
				if( currentBooking[0] == booking[i][0] 
					&& currentBooking[1] == booking[i][1] 
					&& currentBooking[2] == booking[i][2]
				){
					colorCell( nCourt, booking[i][0], booking[i][1], booking[i][2], true );
				}else{
					colorCell( nCourt, booking[i][0], booking[i][1], booking[i][2], false );
				}
			}else{
				colorCell( nCourt, booking[i][0], booking[i][1], booking[i][2], false );
			}
		}
		
	}
	
	function colorConfirmedCell( iCourt, startTime, endTime ){
		console.log(iCourt);
		console.log(startTime);
		console.log(endTime);
		myTable = document.getElementById("myTable");
		for( let i = (startTime-10); i < (endTime-10); i++ ){
			myTable.rows[iCourt].cells[i].innerHTML = "จองแล้ว";
			myTable.rows[iCourt].cells[i].style.backgroundColor = "red";
		}
	}
		
	function colorCell( nCourt, startTime, endTime, status, bCurrentBooking ){
		console.log(nCourt);
		console.log(startTime);
		console.log(endTime);
		console.log(status);
		console.log(bCurrentBooking);
		
		let bgcolor = "";
		switch(status) {
		  case "รอชำระเงิน": bgcolor = "orange";	break;
		  case "รออนุมัติ": 
			if( bCurrentBooking === true ){
				bgcolor = "#bb8fce";	
			}else{
				bgcolor = "#33fff6";	
			}
			break;
		}
		
		myTable = document.getElementById("myTable");
		for( let i = 1; i <= nCourt; i++ ){
			let bClear = true;
			for( let j = (startTime-10); j < (endTime-10); j++ ){
				if( myTable.rows[i].cells[j].innerHTML !== "" ){
					bClear = false;
					break;
				}
			}
			if( bClear === true ){
				// color cell
				for( let j = (startTime-10); j < (endTime-10); j++ ){
					myTable.rows[i].cells[j].innerHTML = status;
					myTable.rows[i].cells[j].style.backgroundColor = bgcolor;
				}
				break;
			}
		}
	}
</script>
</html>

