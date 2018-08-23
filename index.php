<?php

/**
 * @Author: indran
 * @Date:   2018-08-22 21:01:53
 * @Last Modified by:   indran
 * @Last Modified time: 2018-08-23 17:04:36
 */
?>
<!DOCTYPE html>
<html>
<head>
	<title>new db cretion</title>
</head>
<body>
	<center>
		<?php

		function fromTable (   ) {
			$arrayOne = array();

			if(
				isset($_POST['servername']) &&
				isset($_POST['username']) &&
				isset($_POST['password']) &&
				isset($_POST['new_dbname']) &&
				isset($_POST['dbname']) 

			) { 
				$prF = $_POST['new_dbname'];
				echo "<b>gat vaues </b><br>";


				$new_dbname = $_POST['new_dbname'];

				$servername = $_POST['servername'];
				$username = $_POST['username'];
				$password = $_POST['password'];
				$dbname = $_POST['dbname'];

				try {


					$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
					$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



					$stmt = $conn->prepare("SHOW tables;"); 
					$stmt->execute(); 
					$result = $stmt->setFetchMode(PDO::FETCH_ASSOC); 
					$array = $stmt->fetchAll();



					foreach( $array as $k=>$v) { 					

						$oldName =  $v['Tables_in_ritsoftv3']; 
						$oldNameOld =  $v['Tables_in_ritsoftv3']; 

						$oldName = str_replace("old","_old", $oldName);
						$oldName = str_replace("__","_", $oldName); 

						$newName = ""; 
						$tmpo = explode("_", $oldName  );  
						echo "-";
						for ($i=0; $i < sizeof($tmpo) ; $i++) { 
							$dnr = 4;
							if(sizeof($tmpo) > 1 )
								$dnr= 2; 
							if(sizeof($tmpo) > 2 && $i > 0 )
								$dnr= 1; 
							$newName .= substr( $tmpo[$i], 0 ,$dnr);
						}  
						if(strlen($newName) < 4)
							$newName = str_pad($newName,4,"_");  

						$nowData = "";





						$stmta = $conn->prepare("DESC $oldNameOld " ); 
						$stmta->execute(); 
						$resulta = $stmta->setFetchMode(PDO::FETCH_ASSOC); 
						$arraya = $stmta->fetchAll();

						$trtu = array();

						foreach ($arraya as $key => $value) {
							$tnsd45 = trim($newName)."_".trim($value['Field']);
							array_push($trtu , array( 
								'old_column' => trim($value['Field']), 
								'new_column' => trim($tnsd45) ,

								'Type' => trim($value['Type']), 
								'Null' => trim($value['Null']), 
								'Key' => trim($value['Key']), 
								'Default' => trim($value['Default']), 
								'Extra' => trim($value['Extra'])

							));


						}

						array_push($arrayOne, array('old' => trim($oldNameOld ), 'new' => trim($prF) ."_". trim($oldName), 'key' => trim($newName), 'column' =>  $trtu)  );

					}  
				}
				catch(PDOException $e)
				{
					echo $sql . "<br>" . $e->getMessage();
				}

				$conn = null;




			}
			return $arrayOne;

		}



		function goForQuery ($retun) {

			echo "<br>===============================================<br>";


			foreach( $retun as $k=>$v) { 

				echo "<br>-------<br>"; 
				echo "RENAME TABLE `".trim($v['old'])."` TO `".trim($v['new'])."`;"; 
				echo "<br>"; 

				$arraya = $v['column'];


				foreach ($arraya as $key => $value) {  
					echo "<br>";    
					$isNull = " NULL ";
					$isAutoIn = "";
					$isDefault = "";


					if($value['Null'] == 'NO')
						$isNull = " NOT NULL ";

					if($value['Extra'] == 'auto_increment')
						$isAutoIn = " AUTO_INCREMENT ";

					if( !is_null($value['Default'] ) && strlen(trim($value['Default'])) > 0  ){						
						$isDefault = "  DEFAULT '".$value['Default']."'  ";
						if( strtolower($value['Default']) == strtolower('CURRENT_TIMESTAMP') )
							$isDefault = "  DEFAULT ".$value['Default']."  ";
					}



					echo "ALTER TABLE `".trim($v['new'])."` CHANGE `".trim($value['old_column'])."` `".trim($value['new_column'])."` ".trim($value['Type'])."  $isNull $isDefault  $isAutoIn ;";

				} 
				echo "<br><br>-------<br><br>";

			}


		}


		$retun = fromTable();

		// var_dump($retun );

		if(!empty($retun)){
			echo "<b>table names</b> <br><br>";

			echo "<table border='1'>";
			echo "<tr>
			<th>Table name </th>
			<th>new Table Name </th>
			<th>Key </th>
			<th>Columns </th>

			</tr>";
			foreach( $retun as $k=>$v) { 
				echo "<tr>"; 

				echo "<td>". $v['old']. "</td>";

				echo "<td>" .$v['new']. "</td>";  
				echo "<td>" .$v['key']. "</td>";  

				echo "<td>";  

				$arraya = $v['column'];

				echo "<table border='1' width='100%'>";  


				echo "<tr>";  

				echo "<th>";  							
				echo 'old';							
				echo "</th>";  

				echo "<th>";  							
				echo 'new';							
				echo "</th>";  

				echo "<th>";  							
				echo 'Type';							
				echo "</th>";  

				echo "<th>";  							
				echo 'Null';							
				echo "</th>";  

				echo "<th>";  							
				echo 'Key';							
				echo "</th>";  

				echo "<th>";  							
				echo 'Default';							
				echo "</th>";  

				echo "<th>";  							
				echo 'Extra';							
				echo "</th>";   
				echo "</tr>";  




				foreach ($arraya as $key => $value) { 
					echo "<tr>";  

					echo "<td>";  							
					echo $value['old_column'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['new_column'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['Type'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['Null'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['Key'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['Default'];							
					echo "</td>";  

					echo "<td>";  							
					echo $value['Extra'];							
					echo "</td>";  









					echo "</tr>";  
				}

				echo "</table>";  


				echo "</td>";  





				echo "</tr>";
			}
			echo "</table>";

			goForQuery ($retun);

		}



		?>







	</center>







	<?php if( !(
		isset($_POST['servername']) &&
		isset($_POST['username']) &&
		isset($_POST['password']) &&
		isset($_POST['new_dbname']) &&
		isset($_POST['dbname']) 

	)): ?>

	<div>
		<center>
			<form action="" method="post">
				<table>
					<tr>
						<td>servername</td>
						<td><input type="text" name="servername" value="localhost"></td>

					</tr>
					<tr>
						<td>username</td>
						<td><input type="text" name="username" value=""></td>
					</tr>
					<tr>
						<td>password</td>
						<td><input type="password" name="password" value=""></td>
					</tr>
					<tr>
						<td>dbname</td>  
						<td><input type="text" name="dbname" value=""></td>


					</tr>
					<tr>
						<td></td>
						<td><br></td>
					</tr>
					<tr>
						<td>table prefix</td>  
						<td><input type="text" name="new_dbname" value="nr"></td>


					</tr>
					<tr>
						<td></td>  
						<td><input type="submit" name="path_" value="submit"></td>


					</tr>
				</table>
			</form>
		</center>
	</div>

<?php endif; ?>



</body>
</html>