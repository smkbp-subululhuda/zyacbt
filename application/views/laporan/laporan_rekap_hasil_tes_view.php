<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Rekapitulasi Hasil Tes
		<small>Export Rekapitulasi Hasil Tes ke Excel</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Rekap Hasil Tes</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <h4>Informasi</h4>
                <p>Pilih Grup peserta yang akan di export Hasil tes nya pada bagian samping halaman ini, dan pilih rentang waktu Tes tersebut dikerjakan.</p>
                <p>Siswa yang tidak mengikuti Tes, maka Hasilnya akan bernilai N/A pada file hasil Export.</p>
            </div>
        </div>
    </div>
	<?php echo form_open($url.'/export','id="form-export"'); ?>
	<div class="row">
        <div class="col-md-4">
            <div class="box">
				<div class="box-header with-border">
					<div class="box-title">Pilih Grup</div>
    			</div><!-- /.box-header -->
				
				<div class="box-body">
					<div class="form-group">
						<label>Nama Grup</label>
						<input type="hidden" id="nama-grup" name="nama-grup">
                        <select name="pilih-grup" id="pilih-grup" class="form-control input-sm">
							<?php if(!empty($select_group)){ echo $select_group; } ?>
						</select>
						<span class="help-block">Pilih Grup yang akan di Export</span>
					</div>
                </div>
			</div>
        </div>
		
		<div class="col-md-8">
            <div class="box">
				<div class="box-header with-border">
					<div class="box-title">Rekapitulasi Hasil Tes</div>
    			</div><!-- /.box-header -->
				
				<div class="box-body form-horizontal">
						<div class="form-group">
                            <label class="col-sm-3 control-label">Waktu Tes</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" name="pilih-rentang-waktu" id="pilih-rentang-waktu" class="form-control input-sm" value="<?php if(!empty($rentang_waktu)){ echo $rentang_waktu; } ?>" readonly />
                                </div>
								<span class="help-block">Pilih Rentang Tanggal dimana Tes dilakukan. Sesuai Waktu Mulai pada Daftar Tes.</span>
                            </div>
                        </div>
                </div>
				<div class="box-footer">
					<button type="button" class="btn btn-primary pull-right" id="btn-export">Export</button>
                </div>
			</div>
        </div>
    </div>
	</form>
</section><!-- /.content -->



<script lang="javascript">
    $(function(){
		$('#pilih-rentang-waktu').daterangepicker({timePicker: false, timePickerIncrement: 30, format: 'YYYY-MM-DD'});  
    });

    $('#btn-export').click(function(){
    	$('#nama-grup').val($('#pilih-grup option:selected').text());
    	$('#form-export').submit();
    });
</script>