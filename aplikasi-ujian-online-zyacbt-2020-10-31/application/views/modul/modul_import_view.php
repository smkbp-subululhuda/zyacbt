<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Mengimport Soal dari Spreadsheet
		<small>Melakukan Import Soal dari Spreadsheet berdasarkan modul dan topik</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Import Soal</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
		<?php echo form_open_multipart($url.'/import','id="form-importsoal"'); ?>
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Pilih Topik</div>
                </div><!-- /.box-header -->

                <div class="box-body">
					<div class="form-group">
                        <label>Pilih Topik</label>
						<select name="topik" id="topik" class="form-control input-sm">
							<?php if(!empty($select_topik)){ echo $select_topik; } ?>
                        </select>
					</div>
                </div>
                <div class="box-footer">
                    <p>Pilih terlebih dahulu Topik yang akan digunakan sebelum melakukan import soal</p>
                </div>
            </div>
        </div>
		<div class="col-md-8">
			<div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Import Soal dari Spreadsheet</div>
					<div class="box-tools pull-right">
						<div class="dropdown pull-right">
							<a href="<?php echo base_url(); ?>public/form/form-soal-ganda.xls">Form Excel Soal Pilihan Ganda</a>
    					</div>
    				</div>
                </div><!-- /.box-header -->

                <div class="box-body">
					<span id="form-pesan"></span>
                    <div class="form-group">
                        <label>Pilih File</label>
                        <input type="file" id="userfile" name="userfile">
						<p class="help-block">Soal yang dapat import adalah soal jenis Pilihan ganda. Tidak dapat melakukan import soal yang terdapat gambar atau audio.</p>
                        <p class="help-block">File Excel yang didukung adalah Microsoft Excel 2003 dan Microsoft Excel 2007</p>
                        <p class="help-block">SAVE AS ke Office 2007 jika gagal mengupload data dalam format Office 2003</p>
					</div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary pull-right" id="import">Import</button>
                </div>
            </div>
        </div>
		</form>
    </div>
</section><!-- /.content -->



<script lang="javascript">

    function batal_tambah(){
        $("#form-pesan").html('');
        $('#userfile').val('');
    }

    $(function(){
        $('#topik').select2();

        /**
         * Submit form tambah soal
         */
        $('#form-importsoal').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/import",
                    type:"POST",
                    timeout: 300000,
                    data:new FormData(this),
                    mimeType: "multipart/form-data",
                    contentType:false,
                    cache: false,
                    processData: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            $("#modal-proses").modal('hide');
                            batal_tambah();
                            $('#form-pesan').html(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err(obj.pesan));
                        }
                    },
                    statusCode: {
                        500: function(respon) {
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err('Terjadi kesalahan pada File yang di Upload. Silahkan cek terlebih dahulu file yang anda upload.'));
                        }
                    },
                    error: function(xmlhttprequest, textstatus, message) {
                        if(textstatus==="timeout") {
                            $("#modal-proses").modal('hide');
                            notify_error("Gagal mengimport Soal, Silahkan Refresh Halaman");
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(textstatus);
                        }
                    }
            });
            return false;
        });
		 
		$( document ).ready(function() {
            
		});
    });
</script>