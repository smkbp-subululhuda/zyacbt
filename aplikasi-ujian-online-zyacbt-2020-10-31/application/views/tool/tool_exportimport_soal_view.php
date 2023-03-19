<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Export / Import Data Soal
		<small>Fasilitas untuk melakukan Export dan Import data Soal yang sudah dimasukkan ke ZYA CBT</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Export / Import Soal</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-xs-12">
            <div class="callout callout-info">
                <h4>Informasi</h4>
                <p>Export / Import dapat digunakan untuk mendistribusikan Soal yang sudah di entry. Soal didistribusikan ke server lain yang sudah terinstall ZYA CBT.</p>
                <p>Pastikan sebelum melakukan Import Data Soal tidak ada Topik pada ZYA CBT.</p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">Informasi Pendukung</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <p>
                            <b>Informasi Konfigurasi Upload PHP </b>
                            <br />
                            POST_MAX_SIZE = <?php if(!empty($post_max_size)){ echo $post_max_size; } ?>
                            <br />
                            UPLOAD_MAX_FILESIZE = <?php if(!empty($upload_max_filesize)){ echo $upload_max_filesize; } ?>

                            <br /><br />
                            <b>Folder Upload </b>
                            <br />
                            Folder "uploads" = <?php if(!empty($dir_public_uploads)){ echo $dir_public_uploads; } ?>
                            <br />
                            Folder "public/uploads" = <?php if(!empty($dir_uploads)){ echo $dir_uploads; } ?>
                            <br />
                            Pastikan kedua folder diatas memiliki nilai Writeable. Jika tidak, maka Data Soal tidak bisa di import.
                        </p>
                    </div>
                </div>
        </div>
    </div>
	<div class="row">
		<div class="col-md-6">
                <div class="box">
                    <div class="box-header with-border">
    					<div class="box-title">Export Data Soal</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <span id="form-pesan-database"></span>
                        <p>Klik tombol <b>Export Data Soal</b> untuk melakukan Export Data Soal. Distribusikan data Soal untuk server lain.</p>
                        <p>Import Data Soal dengan menggunakan menu Import Soal.</p>
                    </div>
					
					<div class="box-footer">
                        <button type="button" class="btn btn-primary" id="export-soal">Export Data Soal</button>
                    </div>
                </div>
        </div>
        <div class="col-md-6">
            <?php echo form_open_multipart($url.'/importsoal','id="form-import"'); ?>
                <div class="box">
                    <div class="box-header with-border">
    					<div class="box-title">Import Data Soal</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <?php 
                            if(!empty($error_upload) OR !empty($filename)){ 
                                echo '<label>'; 
                                if(!empty($error_upload)){ echo $error_upload; }
                                if(!empty($filename)){ echo $filename; }
                                echo '</label>'; 
                                echo '<hr />';
                            } 

                        ?>
                        <span id="form-pesan">
                            <?php 
                                if(!empty($error)){ 
                                    echo $error; 
                                    echo '<hr />';
                                } 
                            ?>
                        </span>
                        <p>Pastikan Topik pada menu Data Modul Kosong sebelum melakukan Import Data Soal.</p>
						<p>Pilih Data Soal hasil Export untuk melakukan Import Data Soal.</p>
                        <div class="form-group">
                            <input type="file" id="userfile" name="userfile">
                            <p class="help-block">File Hasil Export Data Soal</p>
                        </div>
                        <p>Klik tombol <b>Import Data Soal</b> untuk melakukan proses Import Data Soal.</p>
                    </div>
					
					<div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="import-soal">Import Data Soal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    

    $(function(){
        $('#export-soal').click(function(){
            window.open("<?php echo site_url().'/'.$url; ?>/exportsoal");
        });

        $('#import-soal').click(function(){
            $("#modal-proses").modal('show');
        });
    });
</script>