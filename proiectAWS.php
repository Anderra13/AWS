<!DOCTYPE html>
<html>
	<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

		<script type="text/javascript">
		$(document).ready(function() {
		    $('#example').DataTable( {
		        "pagingType": "full_numbers"
		    } );
		} );
		</script>

		<style type="text/css">
			.button {
			  background-color: #555555;
			  border: none;
			  color: white;
			  padding: 15px 32px;
			  margin: 5px;
			  text-align: center;
			  text-decoration: none;
			  display: inline-block;
			  font-size: 16px;
			  border-radius: 15%;
			  cursor: pointer;
			}

			.textfield {
				border-radius: 15%;
				border: 3px solid #555555;
				margin: 5px;
				padding: 5px;
				font-size: 16px;
			}

			form {
				display: inline-block;
			}

			.squareTrue {
			  height: 50px;
			  width: 50px;
			  border-radius: 30%;
			  margin: auto;
			  background-color: red;
			}

			.squareFalse {
			  height: 50px;
			  width: 50px;
			  border-radius: 30%;
			  margin: auto;
			  background-color: green;
			}
		</style>


	</head>  
	<body style="text-align: center;">
		<h1 style="color: #4286f4;  font-weight: 800;">Medicamente eliberate doar pe baza de prescriptie</h1>

		<form action="proiectAWS.php" method="post">
			<input type="text" name="name" placeholder="Search drug" class="textfield"><br>
			<input type="submit" name="label" value="Search by label" class="button">
			<input type="submit" name="den_comerciala" value="Search by den_comerciala" class="button">
			<input type="submit" name="DCI" value="Search by DCI" class="button">
		</form>

		<div>
			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="all" value="all" />
				<input type="submit" value="All drugs" class="button">
			</form>

			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="all_prescribed" value="all_prescribed" />
				<input type="submit" value="All prescribed drugs" class="button">
			</form>

			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="all_unprescribed" value="all_unprescribed" />
				<input type="submit" value="All unprescribed drugs" class="button">
			</form>

			<form action="proiectAWS.php" method="post">
				<input type="hidden" name="home" value="home" />
				<input type="submit" value="Home" class="button" style="background-color: #4286f4;">
			</form>
		</div>

	</body>
</html>

<?php
if (isset($_POST['home'])) {
	header('Location: http://localhost/proiectAWS.php');
	unset($_POST['home']);
}


if ((isset($_POST['name']) && ($_POST['name']) != "") || isset($_POST['all']) || isset($_POST['all_prescribed']) || isset($_POST['all_unprescribed'])) {
	require_once( "sparqllib.php" );

	$db = sparql_connect( "http://localhost:3030/DINTO-modified/sparql" );
	if( !$db ) {"connect - " . print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	
	if  (isset($_POST['name'])) {

		$name = strtolower($_POST['name']);

		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		prefix den_comerciala: <http://purl.obolibrary.org/obo/DINTO.owl/den_comerciala>
		prefix DCI: <http://purl.obolibrary.org/obo/DINTO.owl/DCI>
		SELECT DISTINCT ?label ?is_prescribed ?den_comerciala ?DCI
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed.
          OPTIONAL {?class den_comerciala: ?den_comerciala}.
  		  OPTIONAL {?class DCI: ?DCI}";

  		  if (isset($_POST['label'])) {
		  	$sparql .= "FILTER(contains(?label, '" . $name . "'))}";
		  	unset($_POST['label']);
		  }
		  else if (isset($_POST['den_comerciala'])) {
		  	$sparql .= "FILTER(contains(?den_comerciala, '" . $name . "'))}";
		  	unset($_POST['den_comerciala']);
		  }
		  else if (isset($_POST['DCI'])) {
		  	$sparql .= "FILTER(contains(?DCI,'" . $name . "'))}";
		  	unset($_POST['DCI']);
		  }

		unset($_POST['name']);
	}
	if (isset($_POST['all'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		prefix den_comerciala: <http://purl.obolibrary.org/obo/DINTO.owl/den_comerciala>
		prefix DCI: <http://purl.obolibrary.org/obo/DINTO.owl/DCI>
		SELECT DISTINCT ?label ?is_prescribed ?den_comerciala ?DCI
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed.
          OPTIONAL {?class den_comerciala: ?den_comerciala}.
  		  OPTIONAL {?class DCI: ?DCI}
		}";
		unset($_POST['all']);
	}
	if (isset($_POST['all_prescribed'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		prefix den_comerciala: <http://purl.obolibrary.org/obo/DINTO.owl/den_comerciala>
		prefix DCI: <http://purl.obolibrary.org/obo/DINTO.owl/DCI>
		SELECT DISTINCT ?label ?is_prescribed ?den_comerciala ?DCI
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed.
          OPTIONAL {?class den_comerciala: ?den_comerciala}.
  		  OPTIONAL {?class DCI: ?DCI}
		  FILTER(?is_prescribed = 'true'^^xsd:boolean)
		}";
		unset($_POST['all_prescribed']);
	}
	if (isset($_POST['all_unprescribed'])) {
		$sparql = "prefix owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		prefix rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		prefix dinto: <http://purl.obolibrary.org/obo/DINTO.owl>
		prefix is_pr: <http://purl.obolibrary.org/obo/DINTO.owl/is_prescribed>
		prefix den_comerciala: <http://purl.obolibrary.org/obo/DINTO.owl/den_comerciala>
		prefix DCI: <http://purl.obolibrary.org/obo/DINTO.owl/DCI>
		SELECT DISTINCT ?label ?is_prescribed ?den_comerciala ?DCI
		WHERE {
		  ?class rdfs:label ?label.
		  ?class is_pr: ?is_prescribed.
          OPTIONAL {?class den_comerciala: ?den_comerciala}.
  		  OPTIONAL {?class DCI: ?DCI}
		  FILTER(?is_prescribed = 'false'^^xsd:boolean)
		}";
		unset($_POST['all_unprescribed']);
	}



	$result = sparql_query( $sparql );
	if( !$result ) { print sparql_errno() . ": " . sparql_error(). "\n"; exit; }
	 
	$fields = sparql_field_array( $result );
	 
	print "<p style='font-weight:bold;''>Number of rows: ".sparql_num_rows( $result )." results.</p>";
	print "<table id='example' class='table table-striped table-bordered' style='width: 80%'>";
	print "<thead>";
	foreach( $fields as $field )
	{
		print "<th>$field</th>";
	}
	print "</thead>";
	print "<tbody>";
	while( $row = sparql_fetch_array( $result ) )
	{
		print "<tr>";
		foreach( $fields as $field )
		{
			if ($field == "is_prescribed") {
				if ($row[$field] == "true")
					print "<td><div class='squareTrue'></div></td>";
				else
					print "<td><div class='squareFalse'></div></td>";
			} else {
				if (!array_key_exists($field, $row))
					print "<td></td>";
				else
					print "<td>$row[$field]</td>";
			}
		}
		print "</tr>";
	}
	print "</tbody>";
	print "</table>";
}
else {
	print "<img src='drugs.jpeg' />";
}
?>

