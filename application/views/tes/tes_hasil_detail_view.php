<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Hasil Tes Detail
		<small>Hasil tes detail setiap user.</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Hasil Tes Detail</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Informasi</div>
                </div><!-- /.box-header -->

                <div class="box-body form-horizontal">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nama Tes</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="tes-user-id" id="tes-user-id" value="<?php if(!empty($tes_user_id)){ echo $tes_user_id; } ?>">
                                <input type="text" name="tes-nama" id="tes-nama" class="form-control input-sm" value="<?php if(!empty($tes_nama)){ echo $tes_nama; } ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nama User</label>
                            <div class="col-sm-9">
                                <input type="text" name="user-nama" id="user-nama" class="form-control input-sm" value="<?php if(!empty($user_nama)){ echo $user_nama; } ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Waktu Tes Mulai</label>
                            <div class="col-sm-9">
                                <input type="text" name="tes-mulai" id="tes-mulai" class="form-control input-sm" value="<?php if(!empty($tes_mulai)){ echo $tes_mulai; } ?>" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nilai</label>
                            <div class="col-sm-9">
                                <input type="text" name="tes-nilai" id="tes-nilai" class="form-control input-sm" value="<?php if(!empty($nilai)){ echo $nilai; } ?>" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Benar</label>
                            <div class="col-sm-9">
                                <input type="text" name="tes-benar" id="tes-benar" class="form-control input-sm" value="<?php if(!empty($benar)){ echo $benar; } ?>" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
    					<div class="box-title">Soal dan Jawaban</div>
                        <div class="box-tools pull-right">
                            <a href="#" onclick="refresh_table()">Refresh Detail Tes</span></a>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table id="table-soal" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Tipe Soal</th>
                                    <th>Soal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> </td>
									<td> </td>
                                    <td> </td>
                                </tr>
                            </tbody>
                        </table>                        
                    </div>
                </div>
        </div>
    </div>

</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-soal').dataTable().fnReloadAjax();
    }

    $(function(){
        $('#table-soal').DataTable({
                  "paging": true,
                  "iDisplayLength":10,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": true,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false, "sWidth":"80px"},
    					{"bSearchable": false, "bSortable": false}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "tes_user_id", "value": $('#tes-user-id').val()} );
                  }
         });
		 
		$( document ).ready(function() {
			
		});
    });
</script>