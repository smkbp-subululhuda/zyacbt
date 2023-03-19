<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Hasil Tes
		<small>Menampilkan Hasil Tes dari User sesuai dengan token yang di generate oleh Operator</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Hasil Tes</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Filter Hasil</div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">
                    <div class="col-xs-2">
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Token Operator</label>
                            <div class="col-sm-9">
                                <input type="text" name="pilih-token" id="pilih-token" class="form-control" value="<?php if(!empty($token)){ echo $token; }else{ echo '\'Kosong\''; } ?>" readonly />
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-2">
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" id="btn-pilih" class="btn btn-primary pull-right"><span>Refresh</span></button>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="box-title">Daftar Hasil Tes</div>
				</div><!-- /.box-header -->

                <div class="box-body">
                    <input type="hidden" name="edit-pilihan" id="edit-pilihan">
					<table id="table-hasil" class="table table-bordered table-hover">
						<thead>
                            <tr>
                                <th class="all">No.</th>
                                <th>Waktu Mulai</th>
                                <th>Waktu</th>
                                <th>Nama Tes</th>
                                <th>Group</th>
                                <th class="all">Nama User</th>
                                <th>Poin</th>
                                <th class="all">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
                                <td> </td>
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
        $('#table-hasil').dataTable().fnReloadAjax();
    }

    $(function(){
        $('#btn-pilih').click(function(){
            $("#modal-proses").modal('show');
            refresh_table();
            $("#modal-proses").modal('hide');
        });


        $('#table-hasil').DataTable({
                  "paging": true,
                  "iDisplayLength":25,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": false,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
    					{"bSearchable": false, "bSortable": false, "sWidth":"150px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "responsive": true,
                  "aLengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "token", "value": $('#pilih-token').val()} );
                  }
         });          
    });
</script>