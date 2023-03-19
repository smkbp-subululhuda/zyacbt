<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		File Manager
		<small>File Manager meliputi Upload file, membuat direktori, hapus file</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">File Manager</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-6">
            <?php echo form_open_multipart($url.'/upload_file','id="form-upload" class="form-horizontal"'); ?>
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">Upload File</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="row-fluid">
                            <div class="box-body">
                                <div id="form-pesan-upload"></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">File</label>
                                    <div class="col-sm-10">
                                        <input type="hidden" name="upload-posisi" id="upload-posisi">
                                        <input type="file" id="upload-file" name="upload-file" >
                                        <p class="help-block">File yang didukung adalah jpg, jpeg, png, mp3</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-upload" class="btn btn-primary">Upload File</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-xs-6">
            <?php echo form_open_multipart($url.'/tambah_dir','id="form-tambah" class="form-horizontal"'); ?>
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">Create Directory</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="row-fluid">
                            <div class="box-body">
                                <div id="form-pesan-tambah"></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Direktori</label>
                                    <div class="col-sm-10">
                                        <input type="hidden" name="tambah-posisi" id="tambah-posisi">
                                        <input type="text" class="form-control input-sm" id="tambah-dir" name="tambah-dir" >
                                        <p class="help-block">Membuat direktori pada direktori yang aktif.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-tambah" class="btn btn-primary">Buat Direktori</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

	<div class="row">
        <div class="col-xs-12">
                <div class="box">
                    <div class="box-header with-border">
    						<div class="box-title">Data File : <a style="cursor:pointer;" onclick="open_home_dir()">Uploads</a><span id="posisi-file-judul"></span></div>
    						<div class="box-tools pull-right">
    							<div class="dropdown pull-right">
    								<a style="cursor: pointer;" onclick="refresh_table()">Refresh Data</a>
    							</div>
    						</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <input type="hidden" name="posisi-file" id="posisi-file">
                        <table id="table-file" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama File</th>
                                    <th>Preview</th>
                                    <th>Tanggal</th>
                                    <th>Ukuran File</th>
                                    <th> </th>
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
                                </tr>
                            </tbody>
                        </table>                        
                    </div>
                </div>
        </div>
    </div>

    <div class="modal" id="modal-preview" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <div id="trx-judul">Preview</div>
                </div>
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="box-body">
                            <div id="preview-image" style="text-align: center;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="modal-hapus" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <?php echo form_open($url.'/hapus_file','id="form-hapus"'); ?>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <div id="trx-judul">Hapus File / Direktori</div>
                </div>
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="box-body">
                            <div id="form-pesan-hapus"></div>
                            <div class="form-group">
                                <label>Nama File / Direktori</label>
                                <input type="hidden" name="hapus-posisi" id="hapus-posisi">
                                <input type="text" class="form-control" id="hapus-file" name="hapus-file" readonly>
                            </div>
                            <p>Perhatian, file atau direktori yang dihapus dapat mempengaruhi Soal yang telah dibuat.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btn-hapus" class="btn btn-primary">Hapus</button>
                    <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>

    </form>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-file').dataTable().fnReloadAjax();
    }

    function open_home_dir(){
        $("#posisi-file").val('')
        $("#posisi-file-judul").html($("#posisi-file").val());
        refresh_table();
    }

    function open_dir(dir){
        var posisi = $("#posisi-file").val();
        $("#posisi-file").val(posisi+'/'+dir);
        $("#posisi-file-judul").html($("#posisi-file").val());
        refresh_table();
    }

    function open_image(image){
        var posisi = $('#posisi-file').val();
        var gambar = '<img style="max-width:500px;" src="<?php echo base_url().$upload_path; ?>/'+posisi+'/'+image+'" />'
        $('#preview-image').html(gambar);

        $("#modal-preview").modal("show");
    }

    function hapus_file(file){
        var posisi = $('#posisi-file').val();
        $('#hapus-posisi').val(posisi);
        $('#hapus-file').val(file);
        $('#form-pesan-hapus').html('');

        $("#modal-hapus").modal("show");
    }

    $(function(){
        $('#form-upload').submit(function(){
            var posisi = $('#posisi-file').val();
            $('#upload-posisi').val(posisi);
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/upload_file",
                    type:"POST",
                    data:new FormData(this),
                    mimeType: "multipart/form-data",
                    contentType:false,
                    cache: false,
                    processData: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#upload-file").val('');
                            $("#upload-posisi").val('');
                            $('#form-pesan-upload').html('');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-upload').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });

        $('#form-tambah').submit(function(){
            var posisi = $('#posisi-file').val();
            $('#tambah-posisi').val(posisi);
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/tambah_dir",
                    type:"POST",
                    data:$('#form-tambah').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#tambah-posisi").val('');
                            $("#tambah-dir").val('');
                            $("#tambah-dir").focus();
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-tambah').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });

        $('#form-hapus').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/hapus_file",
                    type:"POST",
                    data:$('#form-hapus').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#modal-hapus").modal('hide');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-hapus').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });

        $('#table-file').DataTable({
                  "bPaginate": false,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": false,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false},
    					{"bSearchable": false, "bSortable": false, "sWidth":"150px"},
                        {"bSearchable": false, "bSortable": false, "sWidth":"90px"},
                        {"bSearchable": false, "bSortable": false, "sWidth":"50px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "posisi", "value": $('#posisi-file').val()} );
                  }
         });          
    });
</script>