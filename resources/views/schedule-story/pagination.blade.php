
<?php 
// echo count($data); 
// echo $pagination->count();
?>

	<?php 
	if ($page=="") {
		$currentPage = 1;
	} else {
		$currentPage = $page;
	}
	$totalPage = floor(count($data) / 10) +1;
	
	// dd($pagination);
	$startPage = $currentPage - 4;
	$endPage = $currentPage + 4;

	if ($startPage <= 0) {
			$endPage -= ($startPage - 1);
			$startPage = 1;
	}

	if ($endPage > $totalPage)
			$endPage = $totalPage;

	if ($startPage > 1) { 
	?>
		<li <?php if ($currentPage==1) { echo 'class="active"'; } ?>>
			<a href="#">1</a>
		</li>
		<li>
			<a href="#" style="pointer-events: none;cursor: default;">..</a>
		</li>
	<?php
	}
	
	for($ii=$startPage; $ii<=$endPage; $ii++) {
	?>
		<li <?php if ($currentPage==$ii) { echo 'class="active"'; } ?>>
			<a href="#">{{$ii}}</a>
		</li>
	<?php 
	} 
	
	
	if ($endPage < $totalPage) { 
	?>
		<li>
			<a href="#" style="pointer-events: none;cursor: default;">..</a>
		</li>
		<li <?php if ($currentPage==$totalPage) { echo 'class="active"'; } ?>>
			<a href="#">{{$totalPage}}</a>
		</li>
		
	<?php
	}
	
	
	// for($i=1;$i<= floor(count($data) / 10) +1;$i++) { 
	?>
		
	
	