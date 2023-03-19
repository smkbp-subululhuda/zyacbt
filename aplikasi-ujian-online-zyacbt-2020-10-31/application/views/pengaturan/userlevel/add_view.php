<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Tambah Level
		<small>Form penambahan level</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/manager"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Tambah Level</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php echo form_open('manager/userlevel/add','id="form-tambah-level" class="form-horizontal"')?>
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
									<input type="text" class="form-control input-sm" id="level" name="level" placeholder="Level">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-3 control-label">Keterangan</label>
								<div class="col-sm-9">
									<input type="text" class="form-control input-sm" id="keterangan" name="keterangan" placeholder="Keterangan Level">
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
					<button type="submit" id="btn-simpan" class="btn btn-info pull-right">Simpan</button>
				</div>
			</div>
		</div>
    </div>
</form>
</section><!-- /.content -->

<script lang="javascript">
    $(function(){
        $('#form-tambah-level').submit(function(){
            $("#modal-proses").modal('show');
                $.ajax({
                    url:"<?php echo site_url(); ?>/manager/userlevel/add",
     			    type:"POST",
     			    data:$('#form-tambah-level').serialize(),
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