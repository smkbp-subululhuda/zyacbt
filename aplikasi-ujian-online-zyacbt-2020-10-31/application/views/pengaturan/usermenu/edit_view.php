<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Edit Menu
		<small> Form edit menu</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/manager"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Edit User</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
<?php echo form_open('manager/usermenu/edit','id="form-edit-menu" class="form-horizontal"')?>
	<div class="row">
    <div class="col-xs-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Edit Menu</h3>
            </div><!-- /.box-header -->
            
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="form-pesan"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipe Menu</label>
                            <div class="col-sm-5">
                                <input type="hidden" class="form-control input-sm" value="<?php if(!empty($id)){ echo $id; } ?>" id="id" name="id" readonly>
                                <input type="hidden" class="form-control input-sm" id="aksi" name="aksi" readonly>
                                <input type="text" class="form-control input-sm" value="<?php if(!empty($tipe)){ echo $tipe; } ?>" id="tipe" name="tipe" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Parent Menu</label>
                            <div class="col-sm-5">
                                <input type="text" class="form-control input-sm" value="<?php if(!empty($parent)){ echo $parent; } ?>" id="parent" name="parent" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Kode Menu</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control input-sm" value="<?php if(!empty($kode)){ echo $kode; } ?>" id="kode" name="kode" placeholder="Kode Menu" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nama Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" value="<?php if(!empty($nama_menu)){ echo $nama_menu; } ?>" id="nama" name="nama" placeholder="Nama Menu">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">URL Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="url" name="url" value="<?php if(!empty($url)){ echo $url; } ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Icon Menu</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control input-sm" id="icon" name="icon" value="<?php if(!empty($icon)){ echo $icon; } ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Urutan Menu</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control input-sm" id="urutan" name="urutan" value="<?php if(!empty($urutan)){ echo $urutan; } ?>">
                            </div>
                        </div>
                    </div>
                </div>
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
            $('#form-edit-menu').submit();
        }); 
        $('#hapus').click(function(){
            $("#modal-proses").modal('show');
            $('#aksi').val('0');
            $('#form-edit-menu').submit();
        });
             
        $('#form-edit-menu').submit(function(){
                $.ajax({
                    url:"<?php echo site_url(); ?>/manager/usermenu/edit",
     			    type:"POST",
     			    data:$('#form-edit-menu').serialize(),
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