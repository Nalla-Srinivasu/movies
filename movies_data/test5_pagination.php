<?php
error_reporting(E_ALL & ~E_NOTICE);
?>
<htm>
<head>	<link rel="stylesheet" type="text/css" href="/database/bootstrap/css/bootstrap.min.css">
</head>
<body>

	<?Php
	$data_con = mysqli_connect("localhost","root","","movies");
	if(mysqli_connect_error()){
		echo "Database Error:".mysqli_connect_error();
		exit();
	}

	$where = " where 1=1 " ;
	if( $_GET['keyword'] ){
		$where .= " and (
		movie_name like '%" . $_GET['keyword'] . "%' 
		or
		language like '%" . $_GET['keyword'] . "%'
	)";
	}
	if($_GET['genre_id']){
		$query = "select * from movies_genres_data where genre_id = " . $_GET['genre_id'];
		echo $query . "<BR>";
		$res = mysqli_query($data_con, $query );
		echo mysqli_error($data_con);
		$movie_ids = [];
		echo "<div>genre table count: " .  mysqli_num_rows($res) . "</div>";
		while( $row = mysqli_fetch_assoc($res) ){
			$movie_ids[] = $row['movie_id'];
		}
		$where .= " and id in ( " . implode(",", $movie_ids ) . " ) ";
	}
	if ( $_GET['rating1'] && ['rating2']){
		$where .= " and rating between '".$_GET['rating1']."'  and '".$_GET['rating2']."'";
	}

	$query = "select count(*) as cnt from movies_data" . $where;
	echo '<p>'.$query.'</p>';
	$res = mysqli_query($data_con, $query);
	$row = mysqli_fetch_assoc($res);
	$total = $row['cnt'];
	$perpage =50;

	$pages = ceil($total/$perpage);
	if( $_GET['page'] ){
		$current_page = $_GET['page'];
	}else{
		$current_page = 1;	
	}
	$current_page = $_GET['page']?$_GET['page']:1;
	$start = ($current_page-1)*$perpage;

	$sort = $_GET['sort']?$_GET['sort']:'movie_name';
	$order = $_GET['order']?$_GET['order']:'asc';

	$query = "select * from movies_data".$where." order by ".$sort." ". $order ."  limit ".$start.",".$perpage;
	echo '<div>'.$query."</div>";
	$res = mysqli_query($data_con, $query);
	if( mysqli_error($data_con) ){
		echo mysqli_error($data_con);
		exit();
	}

	$query_string = "?";
	foreach( $_GET as $i=>$j ){if( $i != "page" ){
		$query_string .= $i . "=" . $j . "&";
	}}

	echo "<div>QuerySTring: "  . $query_string . "</div>";
	
	echo "<a class='btn btn-primary btn-sm' href='".$query_string."page=1' >First</a>";
	if( $current_page > 1 ){
		echo "&nbsp;<a class='btn btn-primary btn-sm' href='".$query_string."page=".($current_page-1)."' >Prev</a>";
	}
	if( $current_page < $pages ){
		echo "&nbsp;<a class='btn btn-primary btn-sm' href='".$query_string."page=".($current_page+1)."' >Next</a>";
	}
	if( $current_page != $pages ){
	echo "&nbsp;<a class='btn btn-primary btn-sm' href='".$query_string."page=".$pages."' >Last</a>";
	}if($total == 0){
		echo "<div>Result not found</div>";
	}
	elseif( $current_page == $pages ){
		echo "<div>Displaying: " . ($start+1) . " to " . ($total) . " of " . $total . "</div>";
	}else{
		echo "<div>Displaying: " . ($start+1) . " to " . ($start+$perpage) . " of " . $total . "</div>";
		echo "<div>";
	}
		

	?>
	<form>
		Keyword: <input type="text" placeholder="Keyword" name="keyword" value="<?=$_GET['keyword'] ?>" >
		Genres: <select name="genre_id">
			<?php
			$res2 = mysqli_query($data_con, "select * from movies_genres order by genre" );
			if( mysqli_error($data_con) ){
				echo mysqli_error($data_con);exit;
			}
			while( $row2 = mysqli_fetch_assoc($res2) ){ 
			?><option <?=$_GET['genre_id']==$row2['id']?"selected":"" ?> value="<?=$row2['id'] ?>"><?=htmlspecialchars( $row2['genre'] ) ?></option><?php 
			} ?>
		</select>
		Sort: <select name="sort" >
			<option <?=$_GET['sort']=="movie_name"?"selected":"" ?> value="movie_name">Movie Name</option>
			<option <?=$_GET['sort']=="release_date"?"selected":"" ?> value="release_date">Year</option>
			<option <?=$_GET['sort']=="rating"?"selected":"" ?> value="rating">Rating</option>
		</select>
		<select name="order" >
			<option <?=$_GET['order']=="asc"?"selected":"" ?> value="asc">Ascending</option>
			<option <?=$_GET['order']=="desc"?"selected":"" ?> value="desc">Descending</option>
		</select>
		Rating From : <select type="number" placeholder="from" name="rating1">
			<?php for($i=0;$i<=9;$i++){ ?>
				<option <?=$_GET['rating1']==$i?"selected":"" ?>  ><?=$i ?></option>
			<?php } ?>
		</select>
		To : <select type="number" placeholder="to" name="rating2">
			<?php for($i=0;$i<=10;$i++){ ?>
				<option <?=$_GET['rating2']==$i?"selected":"" ?>  ><?=$i ?></option>
			<?php } ?>
		</select>
		<input type="submit" name="action" value="Search" class="btn btn-info">
		<a href="test5_pagination.php?" class="btn btn-dark">Back</a>
	</form>
	<?php

 	echo "<table class='table table-bordered table-striped table-sm table-hover'>";
	echo "<tr>
	<td>S.No</td>
	<td>movie_name</td>
	<td>Release Date</td>
	<td>runtime</td>
	<td>Language</td>
	<td>Budget</td>
	<td>Gross</td>
	<td>Rating</td>
	<td>Genres</td>
	<td>Keywords</td>
	<td>Prod_Companies</td>
	<td>Prod_Countries</td></tr>";
	while ($row = mysqli_fetch_assoc( $res )) {
		echo "<tr><td>";
		echo htmlspecialchars($row['id']);
		echo "</td>
		<td><a href='" . $row['link'] ."' target='_blank'>" . htmlspecialchars($row['movie_name']) . "</a></td>
		<td>" . htmlspecialchars($row['release_date']) . "</td>
		<td>" . htmlspecialchars($row['runtime']) . "</td>
		<td>" . htmlspecialchars($row['language']) . "</td>
		<td>" . htmlspecialchars($row['budget']) . "</td>
		<td>" . htmlspecialchars($row['gross']) . "</td>
		<td>" . htmlspecialchars($row['rating']) . "</td>
		<td>" . htmlspecialchars($row['genres']) . "</td>
		<td>" . htmlspecialchars($row['keyword']) . "</td>
		<td>" . htmlspecialchars($row['prod_companies']) . "</td>
		<td>" . htmlspecialchars($row['prod_countries']) . "</td>
		</tr>";
	}
	echo "</table>";
?>
</body>