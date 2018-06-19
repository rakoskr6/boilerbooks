
<?php
	$title = 'Boiler Books';
	$treasuereactive = "active";
	include '../menu.php';
	include '../dbinfo.php';

	$committee = test_input($_GET["committee"]);
	if ($committee == '' || $committee == "%") {
		$committee = "%";
		$committeeDisplay = "all committees";
	}
	else {
		$committeeDisplay = $committee;
	}


	$fiscalyear = test_input($_GET["fiscalyear"]);
	if ($fiscalyear == '') {
		$fiscalyear = '2017-2018';
	}

	$items = '';
	$usr = $_SESSION['user'];
?>



<div class="container">
	<div id="head" class="text-center">
		<h3>Currently viewing <?php echo $committeeDisplay ?> for fiscal year <?php echo $fiscalyear ?></h3>
	</div>
</div>


<br>


<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<select id="committee" name="committee" class="form-control" onchange="selectcommitteeyear()">
				<?php include '../committees.php'; ?>
			</select>
		</div>
		<div class="col-sm-6">
			<select id="fiscalyear" name="fiscalyear" class="form-control" onchange="selectcommitteeyear()">
				<option value="2017-2018">Select Year</option>
				<option value="2017-2018">2017 - 2018</option>
				<option value="2016-2017">2016 - 2017</option>
				<option value="2015-2016">2015 - 2016</option>
			</select>
		</div>
	</div>
</div>

<br>

<div class="container">
	
	<table id="treasurertable" class="display"> 
		<thead>
    		<tr>
      			<th>Purchase Id</th>
      			<th>Purchase Date</th>
      			<th>Item</th>
      			<th>Fund Source</th>
      			<th>Vendor</th>
      			<th>Committee</th>
      			<th>Purchaser</th>
      			<th>Status</th>
      			<th>Amount</th>
      			<th>Comments</th>
      			<th>Processing</th>
      			<th>Reimbursed</th>
    		</tr>
  		</thead>
	</table>


<script>

    function fetch_data()  
    {  

			$(document).ready(function() {
    		var com = document.getElementById('committee').value;
			if (com == '') {
				com = "<?php echo $committee ?>";
			}
			var title = "select.php?committee=";
			var partial  = title.concat(com);
			var com2 = document.getElementById('fiscalyear').value;

			if (com2 == '') {
				com2 = "<?php echo $fiscalyear ?>";
			}
			var fiscalyear = "&fiscalyear=";
			var tempFinal = fiscalyear.concat(com2);
			fullFinal = partial.concat(tempFinal);

			$.ajax({
    			method : 'POST',
    			url  : fullFinal,
    			dataType: 'json',

    	
    			success :  function(result)
       		 	{
       		 		console.log(result); // just to see I'm getting the correct data.
            		$('#treasurertable').DataTable({
            			"destroy": true,
                		"searching": true, 
                		"data": result,
                		"order": [[ 1, "desc" ]],
                		"columns": [
                			{ "data" : "html_purchase"},
            				{ "data" : "date" },
            				{ "data" : "html_receipt" },
            				{ "data" : "fundsource" },
            				{ "data" : "vendor" },
            				{ "data" : "committee" },
            				{ "data" : "html_purchasedby" },
            				{ "data" : "status" },
            				{ "data" : "cost" },
            				{ "data" : "comments" },
            				{ "data" : "html_processing" },
            				{ "data" : "html_reimbursed" }

                		]

            		});
        		} 
    		});

		});

    }  
 
   

    fetch_data();  

		function selectcommitteeyear() {
			document.getElementById("head").innerHTML = "<h3> Currently viewing " + document.getElementById('committee').value + " for fiscal year " + document.getElementById('fiscalyear').value + "</h3>";
		
			fetch_data();
		}

	</script>
</div>




<?php
	include '../smallfooter.php';
?>

