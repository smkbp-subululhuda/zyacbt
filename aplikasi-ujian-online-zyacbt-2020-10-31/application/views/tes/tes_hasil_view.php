<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Hasil Tes
		<small>Hasil tes, menghapus hasil tes, mengunci tes, membuka kunci, dan menambah waktu tes</small>
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
                    <div class="col-sm-6">
						<div class="form-group">
                            <label class="col-sm-3 control-label">Tes</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="check" id="check" value="0">
                                <select name="pilih-tes" id="pilih-tes" class="form-control input-sm">
                                    <?php if(!empty($select_tes)){ echo $select_tes; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Group</label>
                            <div class="col-sm-9">
                                <select name="pilih-group" id="pilih-group" class="form-control input-sm">
                                    <?php if(!empty($select_group)){ echo $select_group; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Waktu Tes</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" name="pilih-rentang-waktu" id="pilih-rentang-waktu" class="form-control input-sm" value="<?php if(!empty($rentang_waktu)){ echo $rentang_waktu; } ?>" readonly />
                                </div>
								<span class="help-block" id="info-waktu">Rentang waktu peserta saat memulai Tes</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
						<div class="form-group">
                            <label class="col-sm-4 control-label">Status Peserta</label>
                            <div class="col-sm-8">
                                <select name="pilih-status" id="pilih-status" class="form-control input-sm">
                                    <option value="mengerjakan">Peserta Mengerjakan Tes</option>
									<option value="tidak">Peserta Belum Mengerjakan Tes</option>
                                </select>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="col-sm-4 control-label">Keterangan Peserta</label>
                            <div class="col-sm-8">
                                <input type="text" name="pilih-keterangan" id="pilih-keterangan" placeholder="Keterangan Peserta Tes" class="form-control input-sm" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Urutkan</label>
                            <div class="col-sm-8">
                                <select name="pilih-urutkan" id="pilih-urutkan" class="form-control input-sm">
                                    <option value="tertinggi">Nilai Tertinggi</option>  
                                    <option value="terendah">Nilai Terendah</option>
                                    <option value="waktu">Waktu Tes</option>
                                    <option value="nama">Nama User</option>
                                    <option value="tes">Tes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" id="btn-pilih" class="btn btn-primary pull-right"><span>Pilih</span></button>
                </div>
            </div>
        </div>
    </div>
	<div class="row">
        <?php echo form_open($url.'/edit_tes','id="form-edit"'); ?>
        <div class="col-xs-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="box-title">Daftar Hasil Tes</div>
                    <div class="box-tools pull-right">
                        <div class="dropdown pull-right">
                            <a  style="cursor: pointer;" onclick="export_excel()">Export ke Excel</a>
                        </div>
                    </div>
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
                                <th>Status</th>
                                <th class="all"></th>
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
                                <td> </td>
                            </tr>
                        </tbody>
					</table>                        
				</div>
                <div class="box-footer">
                    <button type="button" id="btn-edit-pilih" title="Pilih Hasil Tes" class="btn btn-default pull-right">Pilih Semua</button>
                    <button type="button" id="btn-edit-hapus" title="Hapus Hasil" class="btn btn-primary">Hapus</button>
                    <button type="button" id="btn-edit-hentikan" class="btn btn-primary">Hentikan</button>
                    <button type="button" id="btn-edit-buka-tes" class="btn btn-primary">Buka Tes</button>
                    <button type="button" id="btn-edit-waktu" class="btn btn-primary">Tambah Waktu</button>
                </div>
			</div>
        </div>

        <div class="modal" id="modal-waktu" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button class="close" type="button" data-dismiss="modal">&times;</button>
                        <div id="trx-judul">Tambah Waktu</div>
                    </div>
                    <div class="modal-body">
                        <div class="row-fluid">
                            <div class="box-body">
                                <div id="form-pesan-waktu"></div>
                                <div class="form-group">
                                    <label>Jumlah Waktu</label>
                                    <input type="text" class="form-control" id="waktu-menit" name="waktu-menit" value="10">
                                    <p class="help-block">Waktu dalam satuan MENIT</p>
                                </div>
                                <p class="">Menambah Waktu Tes melalui Penambahan "Waktu Mulai" pada user tes yang sudah dicentang sebelumnya.</p>
                                <p class="">Waktu Mulai pengerjaan Tes hasil penambahan tidak boleh melebihi waktu saat ini.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="btn-edit-waktu-simpan" class="btn btn-primary">Simpan</button>
                        <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>

        </form>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-hasil').dataTable().fnReloadAjax();
    }

    function detail_tes(tesuser_id){
        window.open("<?php echo site_url().'/manager/tes_hasil_detail'; ?>/index/"+tesuser_id);
    }

    function export_excel(){
        var tes = $('#pilih-tes').val();
        var group = $('#pilih-group').val();
        var waktu = $('#pilih-rentang-waktu').val();
        var urutkan = $('#pilih-urutkan').val();
		var status = $('#pilih-status').val();
		var keterangan = $('#pilih-keterangan').val();

        window.open("<?php echo site_url().'/'.$url; ?>/export/"+tes+"/"+group+"/"+waktu+"/"+urutkan+"/"+status+"/"+keterangan, "_self");
    }

    $(function(){
        $('#pilih-rentang-waktu').daterangepicker({timePicker: true, timePickerIncrement: 10, format: 'YYYY-MM-DD H:mm'});

		$("#pilih-status").change(function(){
            if($('#pilih-status').val()=='mengerjakan'){
				$('#info-waktu').html('Rentang waktu peserta saat memulai Tes');
			}else{
				$('#info-waktu').html('Rentang waktu Tes dilaksanakan sesuai di Daftar Tes');
			}
        });
		
        $('#btn-pilih').click(function(){
            $("#modal-proses").modal('show');
            $('#check').val('0');
            refresh_table();
            $("#modal-proses").modal('hide');
        });

        $('#btn-edit-hapus').click(function(){
            $('#edit-pilihan').val('hapus');
            $('#form-edit').submit();
        });

        $('#btn-edit-hentikan').click(function(){
           $('#edit-pilihan').val('hentikan');
            $('#form-edit').submit(); 
        });

        $('#btn-edit-buka-tes').click(function(){
            $('#edit-pilihan').val('buka');
            $('#form-edit').submit();
        });

        $('#btn-edit-waktu').click(function(){
            $('#edit-pilihan').val('waktu');
            $('#waktu-menit').val('10');
            $("#modal-waktu").modal('show');
        });

        $('#btn-edit-waktu-simpan').click(function(){
            $('#form-edit').submit();
        });

        $('#btn-edit-pilih').click(function(event) {
            if($('#check').val()==0) {
                $(':checkbox').each(function() {
                    this.checked = true;
                });
                $('#check').val('1');
            }else{
                $(':checkbox').each(function() {
                    this.checked = false;
                });
                $('#check').val('0');
            }
        });

        $('#form-edit').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/edit_tes",
                    type:"POST",
                    data:$('#form-edit').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#modal-waktu").modal('hide');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(obj.pesan);
                        }
                    }
            });
            return false;
        });

        $('#table-hasil').DataTable({
                  "paging": true,
                  "iDisplayLength":50,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": true,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
    					{"bSearchable": false, "bSortable": false, "sWidth":"150px"},
                        {"bSearchable": false, "bSortable": false, "sWidth":"20px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "responsive": true,
                  "aLengthMenu": [[10, 25, 50, 100, 200, 500], [10, 25, 50, 100, 200, 500]],
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "tes", "value": $('#pilih-tes').val()} );
                    aoData.push( { "name": "group", "value": $('#pilih-group').val()} );
                    aoData.push( { "name": "waktu", "value": $('#pilih-rentang-waktu').val()} );
                    aoData.push( { "name": "urutkan", "value": $('#pilih-urutkan').val()} );
					aoData.push( { "name": "status", "value": $('#pilih-status').val()} );
					aoData.push( { "name": "keterangan", "value": $('#pilih-keterangan').val()} );
                  }
         });          
    });
</script>