<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Edit Level
		<small>Form edit level</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/manager"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Edit Level</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php echo form_open('manager/userlevel/edit','id="form-edit-level" class="form-horizontal"')?>
	<div class="row">
		<div class="col-xs-4">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Level</h3>
				</div><!-- /.box-header -->
				
				<div class="box-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-sm-3 control-label">Level</label>
								<div class="col-sm-9">
                                    <input type="hidden" class="form-control input-sm" id="aksi" name="aksi">
									<input type="text" class="form-control input-sm" value="<?php if(!empty($level)){ echo $level; } ?>" id="level" name="level" placeholder="Level" readonly >
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Keterangan</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm" value="<?php if(!empty($keterangan)){ echo $keterangan; } ?>" id="keterangan" name="keterangan" placeholder="Keterangan" readonly>
								</div>
							</div>
						</div>
					</div>
					
					
				</div>
			</div>
		</div>
		
		<div class="col-xs-8">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title">Hak Akses</h3>
				</div><!-- /.box-header -->
				
				<div class="box-body">
                    <div id="form-pesan"></div>
				    <?php if(!empty($data_menu)){ echo $data_menu; } ?>
				</div>
				
				<div class="box-footer">
                    <div class="pull-right">
                        <button type="button" id="hapus" class="btn btn-default">Hapus</button> 
    					<button type="button" id="simpan" class="btn btn-info">Simpan</button>
                    </div>
				</div>
			</div>
		</div>
    </div>
</form>
</section><!-- /.content -->

<script lang="javascript">
    $(function(){
        $('#simpan').click(function(){
            $("#modal-proses").modal('show');
            $('#aksi').val('1');
            $('#form-edit-level').submit();
        }); 
        $('#hapus').click(function(){
            $("#modal-proses").modal('show');
            $('#aksi').val('0');
            $('#form-edit-level').submit();
        });
        
        $('#form-edit-level').submit(function(){
                $.ajax({
                    url:"<?php echo site_url(); ?>/manager/userlevel/edit",
     			    type:"POST",
     			    data:$('#form-edit-level').serialize(),
     			    cache: false,
      		        success:function(respon){
         		    	var obj = $.parseJSON(respon);
      		            if(obj.status==1){
      		                $("#modal-proses").modal('hide');
      		                $('#form-pesan').html(pesan_succ(obj.pesan));
                            setTimeout(function(){ window.open("<?php echo site_url(); ?>/manager/userlevel","_self"); }, 1000);
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