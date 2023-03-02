<?php


error_reporting(E_ALL & ~E_NOTICE);
// fopen, fread, fwrite, fclose, fseek, fgets,
$data_con = mysqli_connect("localhost","root","","movies");
if(mysqli_connect_error()){
	echo "Database Error:".mysqli_connect_error();
	exit();
}

mysqli_query($data_con, "delete from movies_data");
mysqli_query($data_con, "delete from movies_genres");
mysqli_query($data_con, "delete from movies_genres_data");
mysqli_query($data_con, "delete from movies_keywords");
mysqli_query($data_con, "delete from movies_keywords_data");
mysqli_query($data_con, "delete from movies_prod_comp");
mysqli_query($data_con, "delete from movies_prod_countries");

$fp = fopen("tmdb_5000_movies.csv", "r");
$data = fgets($fp, 2000);
$cnt = 0;
$genres_data = [];
while( $data = fgetcsv($fp, 2000) ){
	$cnt++;
	if( $data == "" ){
		break;
	}
	$query = "insert into movies_data set 
	movie_name = '" .mysqli_escape_string($data_con,$data[6]) . "',
	link = '" .mysqli_escape_string($data_con,$data[2]) . "',
	release_date = '" .mysqli_escape_string($data_con,$data[11]) . "',
	runtime = '" .mysqli_escape_string($data_con,$data[13]) . "',
	language = '" .mysqli_escape_string($data_con,$data[5]) . "',
	budget = '" .mysqli_escape_string($data_con,$data[0]) . "',
	gross = '" .mysqli_escape_string($data_con,$data[12]) . "',
	rating = '" .mysqli_escape_string($data_con,$data[18]) . "',
	genres = '" .mysqli_escape_string($data_con,$data[1]) . "',
	keyword = '" .mysqli_escape_string($data_con,$data[4]) . "',
	prod_companies = '" .mysqli_escape_string($data_con,$data[9]) . "',
	prod_countries = '" .mysqli_escape_string($data_con,$data[10]) . "'";

	mysqli_query($data_con,$query);
	$id = mysqli_insert_id($data_con);
	if( mysqli_error($data_con) ){
		echo mysqli_error($data_con);
		exit;
	}
	echo "<div>" . $cnt . ": " . $id . ": " . $data[6] . "</div>";


	if( $data[1] ){
	$genres = json_decode($data[1],true);
	if( json_last_error() ){
		echo "<div>Record: " . $cnt . ": genres json decode erorr: " . json_last_error_msg() . "</div>";
	}else{
		foreach( $genres as $k => $v) {
			if( $v['id'] && $v['name'] ){
				$query = "select * from movies_genres where id = " . $v['id'];
				$res = mysqli_query($data_con, $query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
				$row = mysqli_fetch_assoc($res);
				if( !$row ){
					$query = "insert into movies_genres set
					id = '".$v['id']."',
					genre = '".mysqli_escape_string($data_con,$v['name'])."'";
					mysqli_query($data_con,$query);
					if( mysqli_error($data_con) ){
						echo $query . "<BR>";
						echo mysqli_error($data_con);
						exit;
					}
				}
				$query = "insert into movies_genres_data set 
				movie_id = " . $id . ", 
				genre_id = " . $v['id'] . " ";
				mysqli_query($data_con,$query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
			}
		}
	}
	}
	if( $data[4] ){
	$keywords = json_decode($data[4],true);
	if( json_last_error() ){
		echo "<div>Record: " . $cnt . ": keywords json decode erorr: " . json_last_error_msg() . "</div>";
	}else{	
		if( !is_array($keywords) ){
			echo "<div>Keywords var is not an array!</div>";
			print_r( $keywords );
		}else{
		foreach( $keywords as $k => $v) {
			if( $v['id'] && $v['name'] ){
				$query = "select * from movies_keywords where id = " . $v['id'];
				$res = mysqli_query($data_con, $query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
				$row = mysqli_fetch_assoc($res);
				if( !$row ){
					$query = "insert into movies_keywords set
					id = '".$v['id']."',
					keyword = '".mysqli_escape_string($data_con,$v['name'])."'";
					mysqli_query($data_con,$query);
					if( mysqli_error($data_con) ){
						echo $query . "<BR>";
						echo mysqli_error($data_con);
						exit;
					}
				}
				$query = "insert into movies_keywords_data set 
				movie_id = " . $id . ", 
				keyword_id = " . $v['id'] . " ";
				mysqli_query($data_con,$query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
			}
		}
		}
	}
	}
	if( $data[9] ){
	$companies = json_decode($data[9],true);
	if( json_last_error() ){
		echo "<div>Record: " . $cnt . ": companies json decode erorr: " . json_last_error_msg() . "</div>";
	}else{	
		if( !is_array($companies) ){
			echo "<div>companies var is not an array!</div>";
			print_r( $companies );
		}else{
		foreach( $companies as $k => $v) {
			if( $v['id'] && $v['name'] ){
			$query = "select * from movies_prod_comp where id = " . $v['id'];
			$res = mysqli_query($data_con, $query);
			if( mysqli_error($data_con) ){
				echo $query . "<BR>";
				echo mysqli_error($data_con);
				exit;
			}
			$row = mysqli_fetch_assoc($res);
			if( !$row ){
				$query = "insert into movies_prod_comp set
				id = '".$v['id']."',
				production_companies = '".mysqli_escape_string($data_con,$v['name'])."'";
				mysqli_query($data_con,$query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
			}
			}
		}
		}
	}
	}
	if( $data[10] ){
	$countries = json_decode($data[10],true);
	if( json_last_error() ){
		echo "<div>Record: " . $cnt . ": countries json decode erorr: " . json_last_error_msg() . "</div>";
	}else{	
		if( !is_array($countries) ){
			echo "<div>countries var is not an array!</div>";
			print_r( $countries );
		}else{
		foreach( $countries as $k => $v) {
			if( $id && $v['name'] ){
			$query = "select * from movies_prod_countries where id = " . $id;
			$res = mysqli_query($data_con, $query);
			if( mysqli_error($data_con) ){
				echo $query . "<BR>";
				echo mysqli_error($data_con);
				exit;
			}
			$row = mysqli_fetch_assoc($res);
			if( !$row ){
				$query = "insert into movies_prod_countries set
				id = '".$id."',
				production_countries = '".mysqli_escape_string($data_con,$v['name'])."'";
				mysqli_query($data_con,$query);
				if( mysqli_error($data_con) ){
					echo $query . "<BR>";
					echo mysqli_error($data_con);
					exit;
				}
			}
			}
		}
		}
	}
	}
	//exit;
}
