<?php
include("db.php");


$error = "";
$film = $zanr = $godina = $trajanje = "";

$query = "SELECT * FROM zanr";
$result_zanr = $conn->query($query);


if(isset($_GET['obrisi']))
{
	$query = "DELETE FROM filmovi WHERE id = ?";
	$stmt = $conn->prepare($query);
	$stmt->bind_param("s", $_id);
	
	// Set
	$_id = $_GET['obrisi'];
	
	$stmt->execute();
			
	$stmt->close();
	
	
}

if(!empty($_POST))
{
	$film = $_POST['film'];
	$trajanje = $_POST['trajanje'];
	$godina = $_POST['godina'];
	$zanr = $_POST['zanr'];
	
	if(empty($film))
	{
		$error = "Unesite naziv filma!";
	}
	
	if(empty($trajanje) && !$error)
	{
		$error = "Unesite trajanje filma!";
	}
	
	$path = 'upload';
	
	if(!is_dir($path))
		mkdir($path, 0755);
	
	$file_name = $_FILES['file']['name'];
	$tmp_name = $_FILES['file']['tmp_name'];
	$error = $_FILES['file']['error'];
	$size = $_FILES['file']['size'];
	
	if(!$error && $size != 0)
	{
		$file_parts = pathinfo($file_name);
					
		$ext = $file_parts['extension'];
		
		$new_name = time().rand(100,999).uniqid().'.'.$ext;
		
		$dest = $path . '/' . $new_name;
		
		if(!move_uploaded_file($tmp_name,$dest))
		{
			$error = 'Došlo je do pogreške prilikom prijenosa datoteke!';
		}
		else
		{
			
			// Insert query
			$query = "INSERT INTO filmovi (naslov, id_zanr, godina, trajanje, slika) VALUES (?, ?, ?, ?, ?)";
			
			$stmt = $conn->prepare($query);
			
			$stmt->bind_param("sssss", $_naslov, $_zanr, $_godina, $_trajanje, $_new_name);
			
			$_naslov = $film;
			$_zanr = $zanr;
			$_godina = $godina;
			$_trajanje = $trajanje;
			$_new_name = $new_name;
			
			$stmt->execute();
			
			$stmt->close();
			
			
			// Reset variables and show success message
			$error = "Uspješno unešeni podaci!";
		}
	}
		
}


// FETCH ALL From ....
$query = "SELECT * FROM filmovi ORDER BY LOWER(naslov)";
	
$stmt = $conn->prepare($query);

$stmt->execute();

$result = $stmt->get_result();


?>
<html>
	<head>
		<title>Filmoteka</title>
		<style>table, th, td {
  border: 1px solid black;
}</style>
	</head>
	<meta charset = "UTF-8">
	<body>
	<?php if($error){echo "<center>{$error}</center>";} ?>
		<form method="post" action="unos.php" enctype="multipart/form-data">
			<table>
				<tr>
					<td><label>Naslov filma: </label></td>
					<td><input type="text"  name = "film" value ="<?php echo $film;?>"></input></td>
				</tr>
				<tr>
					<td>
						<label>Žanr : </label>
					</td>
					<td>
					<select name="zanr">
					<?php while ($row = $result_zanr->fetch_row()) 
					{
						echo "<option value='{$row[0]}'>{$row[1]}</option>";
					} 
					$result_zanr->close();
					?>
					</select>
					</td>
				</tr>
				<tr>
					<td>
						<label>Godina : </label>
					</td>
					<td>
						<select name="godina">
							<?php for ($i = 2019; $i>=1900; $i--):  ?>
								<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
							<?php endfor; ?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label>Trajanje : </label>
					</td>
					<td>
						<input type="number" name = "trajanje"  value="<?php echo $trajanje; ?>"></input>
					</td>
				</tr>
			</table>
			<br>
			<label>Slika: </label><input type="file" name = "file" ></input>
			<br><br>
			<input type ="submit"></input>
		</form>
		<p>
			<table>
				<tr>
					<th>Slika</th>
					<th>Naslov Filma</th>
					<th>Godina</th>
					<th>Trajanje</th>
					<th>Akcija</th>
				</tr>
				<?php while ($row = $result->fetch_row()): ?>
					<tr>
						<td><img src="upload/<?php echo $row[5];?>" style="width:100px;height:100px;"></td>
						<td><?php echo $row[1];?></td>
						<td><?php echo $row[3];?></td>
						<td><?php echo $row[4];?> min</td>
						<td><a href ="?obrisi=<?php echo $row[0];?>">[obriši]</a></td>
					</tr>
					<?php endwhile; ?>
					<?php
					$result->close();
				?>
			</table>
		</p>
	</body>
</html>



