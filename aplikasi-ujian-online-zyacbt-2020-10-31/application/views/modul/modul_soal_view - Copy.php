<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Mengelola Soal
		<small>Mengelola soal berdasarkan modul dan topik</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Soal</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-xs-3">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">Pilih Modul / Topik</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group">
                            <label>Modul</label>
                            <div id="data-kelas">
                                <select name="modul" id="modul" class="form-control input-sm">
                                    <?php if(!empty($select_modul)){ echo $select_modul; } ?>
                                </select>
                            </div>
                        </div>
						<div class="form-group">
                            <label>Topik</label>
                            <div id="data-kelas">
                                <select name="topik" id="topik" class="form-control input-sm">
                                    <option value="kosong">Pilih Topik</option>
                                </select>
                            </div>
                            <small>Pilih modul dan topik terlebih dahulu sebelum menambah atau mengubah soal</small>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="button" id="btn-pilih" class="btn btn-primary pull-right">Pilih</button>
                    </div>
                </div>
        </div>

        <div class="col-xs-9">
                <div class="box">
                    <?php echo form_open_multipart($url.'/tambah','id="form-tambah" class="form-horizontal"'); ?>
                        <div class="box-header with-border">
                            <div class="box-title">Mengelola Soal <span id="judul-tambah-soal"></span></div>
                        </div><!-- /.box-header -->

                        <div class="box-body">
                            <div id="form-pesan"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Soal</label>
                                <div class="col-sm-10">
                                    <input type="hidden" name="tambah-topik-id" id="tambah-topik-id" >
                                    <input type="hidden" name="tambah-soal-id" id="tambah-soal-id" >
                                    <textarea class="textarea" id="tambah-soal" name="tambah-soal" style="width: 100%; height: 300px; font-size: 14px; line-height: 25px; border: 1px solid #dddddd; padding: 10px;"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">File Audio</label>
                                <div class="col-sm-10">
                                    <input type="file" id="tambah-audio" name="tambah-audio" >
                                    <p class="help-block">File audio yang akan ditambah pada bagian bawah soal. ( hanya file mp3)</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Putar Sekali</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="tambah-putar" name="tambah-putar">
                                        <option value="1">Ya</option>
                                        <option value="0">Tidak</option>
                                    </select>
                                    <p class="help-block">Memutar Audio sebanyak satu kali dalam satu Tes</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tipe Soal</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="tambah-tipe" name="tambah-tipe">
                                        <option value="1">Pilihan Ganda</option>
                                        <option value="2">Esai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tingkat Kesulitan</label>
                                <div class="col-sm-10">
                                    <select class="form-control" id="tambah-kesulitan" name="tambah-kesulitan">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" id="tambah-simpan" class="btn btn-primary pull-right">Simpan</button>
                        </div>
                    </form>
                </div>


                <div class="box">
                    <div class="box-header with-border">
    						<div class="box-title">Daftar Soal <span id="judul-daftar-soal"></span></div>
    						<div class="box-tools pull-right">
    							<div class="dropdown pull-right">
    								<a class="dropdown-toggle" data-toggle="dropdown" href="#">Menu <span class="caret"></span></a>
    								<ul class="dropdown-menu">
    									<li role="presentation"><a role="menuitem" style="cursor: pointer;" onclick="tambah()">Tambah Soal</a></li>
    								</ul>
    							</div>
    						</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <table id="table-soal" class="table table-bordered table-hover">
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
    </div>


    <div class="modal" id="modal-image" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
    <?php echo form_open($url.'/edit','id="form-edit"'); ?>
        <div class="modal-dialog" style="width: 1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <button class="close" type="button" data-dismiss="modal">&times;</button>
                    <div id="trx-judul">Insert Image</div>
                </div>
                <div class="modal-body">
                    <div class="row-fluid">
                        <div class="box-body">
                            <div id="form-pesan-image"></div>
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
                                        <div class="box">
                                            <div class="box-header with-border">
                                                <div class="box-title">File Preview</div>
                                            </div><!-- /.box-header -->

                                            <div class="box-body">
                                                <div class="row-fluid">
                                                    <div class="box-body">
                                                        <div class="form-group">
                                                            <label class="col-sm-2 control-label">File</label>
                                                            <div class="col-sm-10">
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-image" class="btn btn-primary">Masukkan</button>
                    <a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>
                </div>
            </div>
        </div>

    </form>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-soal').dataTable().fnReloadAjax();
    }
	
	function refresh_topik(){
        var modul_id = $('#modul').val();
		$.getJSON('<?php echo site_url().'/'.$url; ?>/get_topik_by_modul/'+modul_id, function(data){
            var $topik = $('#topik');
            $topik.empty();
            if(data.data==1){
                $.each(data.topik, function(key, val){
                    $topik.append('<option value="' + val.id + '">' + val.topik +'</option>');
                });
            }else{
				$topik.append('<option value="kosong">Tidak Ada Topik</option>');
			}
        });
	}

    function edit(id){
        $("#modal-proses").modal('show');
        $.getJSON('<?php echo site_url().'/'.$url; ?>/get_by_id/'+id+'', function(data){
            if(data.data==1){
                $('#edit-id').val(data.id);
                $('#edit-topik').val(data.topik);
                $('#edit-topik-asli').val(data.topik);
                $('#edit-deskripsi').val(data.deskripsi);
                
                $("#modal-edit").modal("show");
            }
            $("#modal-proses").modal('hide');
        });
    }

    /**
     * Fungsi untuk upload image dari summernote
     */
    function imageUpload(){
        $("#modal-image").modal("show");
    }

    $(function(){
        refresh_topik();

        $("#modul").change(function(){
            refresh_topik();
        });

        $("#btn-pilih").click(function(){
            $("#modal-proses").modal('show');
            $('#judul-tambah-soal').html($('#topik option:selected').text());
            $('#judul-daftar-soal').html($('#topik option:selected').text());
            $('#tambah-topik-id').val($('#topik').val());
            refresh_table();
            $("#modal-proses").modal('hide');
        });

        $('#btn-image').click(function(){
            $("#tambah-soal").summernote('pasteHTML', '<img src="http://localhost/cbt/uploads//boss.jpg" style="max-width:300px;" />');
            $("#modal-image").modal("hide");
        });

        $('#form-tambah').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/tambah",
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
                            $("#form-pesan").html('');
                            $("#tambah-soal").summernote('code', '');
                            $('#tambah-tipe').val('1');
                            $('#tambah-putar').val('1');
                            $('#tambah-audio').val('');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });
		 
		$( document ).ready(function() {
            $('#table-soal').DataTable({
                  "paging": true,
                  "iDisplayLength":10,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": true,
                  "aoColumns": [
                        {"bSearchable": false, "bSortable": false, "sWidth":"20px"},
                        {"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false, "sWidth":"50px"},
                        {"bSearchable": false, "bSortable": false, "sWidth":"50px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable/",
                  "autoWidth": false,
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "topik", "value": $('#topik').val()} );
                  }
            });

            $('#tambah-soal').summernote({
                height: 200,
                toolbar: [
                    // [groupName, [list of button]]
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['picture']],
                    ['misc', ['codeview']]
                  ],

                oninit: function() {
                    
                }
            });
		});
    });
</script>