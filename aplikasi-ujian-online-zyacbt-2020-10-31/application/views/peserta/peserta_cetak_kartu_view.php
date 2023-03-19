<html>
<head>
	<title>ZYACBT | Cetak Kartu</title>
	<!-- jQuery 2.1.4 -->
    <script src="<?php echo base_url(); ?>public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
</head>
<style>
	table {
		width: 100%;
		border: 0px;
		padding: 2px;
		font-size: 0.75em; 
		color: #000 !important; 
		font-family: Verdana, Arial, sans-serif; 
	}
	
	td {
		vertical-align: top;		
	}
	
	hr {
		border: 0.5px solid black;
	}
	
	.header {
		text-align: center;
		font-weight: bold;
		font-size: 1.1em;
	}
	
	.kartu {
		width: 310px;
		border: 2px solid black;
		border-radius: 8px;
		padding: 3px;
		margin: 10px;
		display: inline-block;
	}
</style>

<body>
	<?php
		if(!empty($kartu)){
			echo $kartu;			
		}
	?>
	
	<script lang="javascript">
		$(function(){
			window.print();
		});
	</script>
</body>
</html>