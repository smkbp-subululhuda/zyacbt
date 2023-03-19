<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Cetak Kartu Peserta
		<small>Mencetak Kartu Peserta berdasarkan data Peserta yang telah di inputkan.</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Kartu Peserta</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <h4>Informasi</h4>
                <p>Lakukan Pengaturan nama pelaksana ZYACBT sebelum mencetak kartu peserta agar kartu sesuai dengan Organisasi tempat anda melaksanakan Tes.</p>
                <p>Pastikan terlebih dahulu data Group dan data Peserta sudah ter-upload.</p>
            </div>
        </div>
    </div>
	<div class="row">
        <div class="col-md-12">
			<?php echo form_open($url.'/kartu','id="form-kartu"'); ?>
                <div class="box">
                    <div class="box-header with-border">
    					<div class="box-title">Cetak Kartu Peserta</div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="form-group col-sm-6">
                            <label>Pilih Group Peserta</label>
							<div id="data-group">
								<select name="group" id="group" class="form-control input-sm">
									<?php if(!empty($select_group)){ echo $select_group; } ?>
								</select>
							</div>
                            <p class="help-block">Pilih Data Group Peserta yang akan di cetak</p>
                        </div>
                        
                        <?php if(!empty($hasil)){ echo $hasil; } ?>
                    </div>
					
					<div class="box-footer">
                        <button type="button" class="btn btn-primary" id="kartu">Cetak kartu</button>
                    </div>
                </div>
			<?php echo form_close(); ?> 
        </div>
    </div>
</section><!-- /.content -->



<script lang="javascript">
	$(function(){
        $('#kartu').click(function(){
			var grup_id = $('#group').val();
			window.open("<?php echo site_url().'/'.$url; ?>/cetak_kartu/"+grup_id);
		});
    });
</script>