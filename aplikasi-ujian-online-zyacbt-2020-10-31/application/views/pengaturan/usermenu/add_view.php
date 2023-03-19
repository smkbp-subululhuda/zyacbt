<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Tambah Menu
		<small>Form penambahan menu</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/manager"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Tambah Menu</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php echo form_open('manager/usermenu/add','id="form-tambah-menu" class="form-horizontal"')?>
	<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Tambah Menu</h3>
            </div><!-- /.box-header -->
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="form-pesan"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipe Menu</label>
                            <div class="col-sm-5">
                                <select name="tipe" id="tipe" class="form-control input-sm">
                                    <option value="1">Child</option>
                                    <option value="0">Parent</option>                                
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Parent Menu</label>
                            <div class="col-sm-5">
                                <?php 
                                    if(!empty($parent)){
                                        echo $parent;
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Kode Menu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control input-sm" id="kode" name="kode" placeholder="Kode Menu">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nama Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="nama" name="nama" placeholder="Nama Menu">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">URL Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="url" name="url" value="#">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Icon Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="icon" name="icon" value="fa fa-circle-o">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Urutan Menu</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-sm" id="urutan" name="urutan" placeholder="Urutan Menu">
                            </div>
                        </div>
                    </div>
                </div>
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
        $('#tipe').change(function(){
            if($('#tipe').val()==0){
                $('#parent').attr('disabled','disabled');
            }else{
                 $('#parent').removeAttr('disabled');
            }
        });
        
        $('#form-tambah-menu').submit(function(){
            $("#modal-proses").modal('show');
                $.ajax({
                    url:"<?php echo site_url(); ?>/manager/usermenu/add",
     			    type:"POST",
     			    data:$('#form-tambah-menu').serialize(),
     			    cache: false,
      		        success:function(respon){
         		    	var obj = $.parseJSON(respon);
      		            if(obj.status==1){
      		                $("#modal-proses").modal('hide');
      		                $('#form-pesan').html(pesan_succ(obj.pesan));
                            setTimeout(function(){ window.open("<?php echo site_url(); ?>/manager/usermenu","_self"); }, 1000);
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