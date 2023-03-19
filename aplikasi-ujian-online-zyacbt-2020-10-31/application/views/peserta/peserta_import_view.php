<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Import Data Peserta
		<small>Penambahan data melalui Import data dari file Excel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Import Peserta</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <h4>Informasi</h4>
                <p>Data siswa yang di import adalah data yang akan digunakan siswa untuk memulai tes atau ujian. Form data siswa dapat di Download di menu Download yang ada di pojok kanan kotak dialog.</p>
                <p>Pastikan terlebih dahulu data Group sudah terdaftar sebelum melakukan Import Data</p>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-md-12">
			<?php echo form_open_multipart($url.'/import','id="form-importsiswa"'); ?>
                <div class="box">
                    <div class="box-header with-border">
    					<div class="box-title">Import Peserta</div>
    					<div class="box-tools pull-right">
							<div class="dropdown pull-right">
								<a href="<?php echo base_url(); ?>public/form/form-data-siswa.xls">Download Form Import Siswa</a>
    						</div>
    					</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
						<label>
                            <?php if(!empty($error_upload)){ echo $error_upload; } ?>
                            <?php if(!empty($filename)){ echo $filename; } ?>
                        </label>
                        <span id="form-pesan">
                            <?php if(!empty($error)){ echo $error; } ?>
                        </span>
                        <div class="form-group">
                            <input type="file" id="userfile" name="userfile">
                            <p class="help-block">File Excel yang didukung adalah Microsoft Excel 2003 dan Microsoft Excel 2007</p>
                            <p class="help-block">SAVE AS ke Office 2007 jika gagal mengupload data dalam format Office 2003</p>
                        </div>
                        
                        <?php if(!empty($hasil)){ echo $hasil; } ?>
                    </div>
					
					<div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="import">Import</button>
                    </div>
                </div>
			<?php echo form_close(); ?> 
        </div>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-peserta').dataTable().fnReloadAjax();
    }

    function tambah(){
        $('#form-pesan').html('');
        $('#tambah-username').val('');
        $('#tambah-password').val('');
        $('#tambah-nama').val('');
        $('#tambah-email').val('');

        $("#modal-tambah").modal("show");
        $('#tambah-username').focus();
    }

    function edit(id){
        $("#modal-proses").modal('show');
        $.getJSON('<?php echo site_url().'/'.$url; ?>/get_by_id/'+id+'', function(data){
            if(data.data==1){
                $('#edit-id').val(data.id);
                $('#edit-username').val(data.username);
                $('#edit-password').val(data.password);
                $('#edit-nama').val(data.nama);
                $('#edit-email').val(data.email);
                $('#edit-group').val(data.group);
                
                $("#modal-edit").modal("show");
            }
            $("#modal-proses").modal('hide');
        });
    }

    $(function(){
        $("#group").change(function(){
            refresh_table();
        });

        $('#edit-simpan').click(function(){
            $('#edit-pilihan').val('simpan');
            $('#form-edit').submit();
        });
        $('#edit-hapus').click(function(){
            $('#edit-pilihan').val('hapus');
            $('#form-edit').submit();
        });

        $('#form-tambah').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/tambah",
                    type:"POST",
                    data:$('#form-tambah').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            refresh_table();
                            $("#modal-proses").modal('hide');
                            $("#modal-tambah").modal('hide');
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });      
    });
</script>