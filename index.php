<?php

include 'db.php';

$letters = range('A', 'Z');


if(isset($_GET['show']))
{
	
	$query = "SELECT * FROM filmovi WHERE LOWER(naslov) LIKE '". strtolower($_GET['show']). "%'";
	
	$stmt = $conn->prepare($query);
	
	$stmt->execute();
	
	$result = $stmt->get_result();
	// Fetch
}

?>


<html>
	<head>
		<title>Filmoteka</title>
	</head>
	<meta charset = "UTF-8">
	<body>
		<div>
			<table style="border: 1px;">
				<tr>
					<?php foreach ($letters as $leter): ?>
						<td><a href="?show=<?php echo $leter; ?>"><?php echo $leter; ?></a></td>
					<?php endforeach; ?>
				</tr>
			</table>
		</div>
		<br><br>
		<div style="text-align:center;">
		<?php if(isset($result)): ?>
		<?php while ($row = $result->fetch_row()): ?>
					<img src="upload/<?php echo $row[5];?>" style="width:100px;height:100px;"><br>
					<?php echo $row[1] . "(" . $row[3] . ")";?><br>
					Trajanje : <?php echo $row[4]; ?> min <br><br><br>
		<?php endwhile; endif;?>
			
			
		</div>
	</body>
</html>