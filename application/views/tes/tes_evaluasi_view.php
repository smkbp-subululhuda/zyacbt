<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Evaluasi Jawaban Essay
		<small>Evaluasi jawaban essay dari user masukkan</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Evaluasi</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Filter Jawaban</div>
                </div><!-- /.box-header -->
                <div class="box-body form-horizontal">
                    <div class="col-xs-2">
                    </div>
                    <div class="col-xs-8">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tes</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="check" id="check" value="0">
                                <select name="pilih-tes" id="pilih-tes" onclick="refresh_table()" class="form-control input-sm">
                                    <?php if(!empty($select_tes)){ echo $select_tes; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Urutkan</label>
                            <div class="col-sm-5">
                                <select name="pilih-urutkan" id="pilih-urutkan" onclick="refresh_table()" class="form-control input-sm">
                                    <option value="soal">Soal</option>  
                                    <option value="user">User</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-2">
                    </div>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="box-title">Daftar Jawaban</div>
				</div><!-- /.box-header -->

                <div class="box-body">
                    <input type="hidden" name="edit-pilihan" id="edit-pilihan">
					<table id="table-jawaban" class="table table-bordered table-hover">
						<thead>
                            <tr>
                                <th>No.</th>
                                <th>Soal</th>
                                <th>Jawaban</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
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

        <div class="modal" id="modal-evaluasi" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <?php echo form_open($url.'/simpan_nilai','id="form-nilai"'); ?>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                        <div id="trx-judul">Evaluasi Jawaban</div>
                    </div>
                    <div class="modal-body">
                        <div class="row-fluid">
                            <div class="box-body">
                                <div id="form-pesan-evaluasi"></div>
                                <div class="form-group">
                                    <label>Nilai</label>
                                    <input type="hidden" id="evaluasi-testlog-id" name="evaluasi-testlog-id" >
                                    <input type="hidden" id="evaluasi-nilai-min" name="evaluasi-nilai-min" >
                                    <input type="hidden" id="evaluasi-nilai-max" name="evaluasi-nilai-max" >
                                    <input type="text" class="form-control" id="evaluasi-nilai" name="evaluasi-nilai" >
                                    <p class="help-block">Nilai dari jawaban yang diberikan</p>
                                </div>
                                <div class="form-group">
                                    <label>Nilai Minimal adalah <span id="nilai-min"></span></label>
                                    <br />
                                    <label>Nilai Maximal adalah <span id="nilai-max"></span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-nilai-simpan" class="btn btn-primary">Simpan</button>
                        <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-jawaban').dataTable().fnReloadAjax();
    }

    function evaluasi(id, nilai_min, nilai_max){
        $("#modal-proses").modal('show');

        $("#nilai-min").html(nilai_min);
        $("#nilai-max").html(nilai_max);

        $("#evaluasi-testlog-id").val(id);
        $("#evaluasi-nilai").val('');
        $("#evaluasi-nilai-min").val(nilai_min);
        $("#evaluasi-nilai-max").val(nilai_max);

        $("#modal-evaluasi").modal("show");
        $("#evaluasi-nilai").focus();
        
        $("#modal-proses").modal('hide');
    }

    $(function(){
        $('#pilih-rentang-waktu').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'YYYY-MM-DD H:mm'});

        $('#form-nilai').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/simpan_nilai",
                    type:"POST",
                    data:$('#form-nilai').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#modal-evaluasi").modal('hide');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(obj.pesan);
                        }
                    }
            });
            return false;
        });

        $('#table-jawaban').DataTable({
                  "paging": true,
                  "iDisplayLength":25,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": false,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false, "sWidth":"40%"},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false, "sWidth":"30px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "aLengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "tes", "value": $('#pilih-tes').val()} );
                    aoData.push( { "name": "urutkan", "value": $('#pilih-urutkan').val()} );
                  }
         });          
    });
</script>