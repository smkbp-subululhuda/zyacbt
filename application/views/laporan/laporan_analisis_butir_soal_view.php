<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Laporan Analisis Butir Soal
		<small>Analisis Butir Soal seusia dengan grup dan tes yang dipilih</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Analisis Butir Soal</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <h4>Informasi</h4>
                <p>Pilih Grup peserta dan Tes untuk mendapatkan analisis butir soal.</p>
                <p>Urutan soal yang dianalisis sesuai dengan urutan soal saat dilakukan input soal seperti pada Daftar Soal pada menu Modul.</p>
            </div>
        </div>
    </div>
	<?php echo form_open($url.'/export','id="form-export"'); ?>
	<div class="row">
		<div class="col-sm-12">
			<div class="box">
				<div class="box-header with-border">
					<div class="box-title">Pilih Grup dan Tes</div>
				</div><!-- /.box-header -->
					
				<div class="box-body">
					<div class="col-md-6">
						<div class="form-group">
							<label>Nama Grup</label>
							<input type="hidden" id="nama-grup" name="nama-grup">
							<select name="pilih-grup" id="pilih-grup" class="form-control input-sm">
								<?php if(!empty($select_group)){ echo $select_group; } ?>
							</select>
							<span class="help-block">Pilih Grup yang akan dianalisis soalnya</span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label>Nama Tes</label>
							<input type="hidden" id="nama-tes" name="nama-tes">
							<select name="pilih-tes" id="pilih-tes" class="form-control input-sm">
								<?php if(!empty($select_tes)){ echo $select_tes; } ?>
							</select>
							<span class="help-block">Pilih Nama Tes yang akan dianalisis soalnya</span>
						</div>
					</div>
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-primary pull-right" id="btn-export">Export</button>
				</div>
			</div>
		</div>
    </div>
	</form>
</section><!-- /.content -->



<script lang="javascript">
    $(function(){
		
    });

    $('#btn-export').click(function(){
    	$('#nama-grup').val($('#pilih-grup option:selected').text());
		$('#nama-tes').val($('#pilih-tes option:selected').text());
    	$('#form-export').submit();
    });
</script>