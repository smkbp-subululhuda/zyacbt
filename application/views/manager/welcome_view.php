<div class="container">
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Selamat Datang
        <small> di Halaman Login Administrator <?php if(!empty($site_name)){ echo $site_name; } ?></small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="callout callout-info">
        <h4>Informasi</h4>
        <p>
            Selamat datang di Halaman Login Aplikasi Computer Based-Test. Untuk memulai silahkan melakukan 
            proses Login dengan menggunakan username dan password yang sudah dimiliki.
        </p>
    </div>
    
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <!-- Horizontal Form -->
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">Login Operator</h3>
                </div><!-- /.box-header -->
                <!-- form start -->
                <?php echo form_open('welcome/login','id="form-login" class="form-horizontal"')?>
                    <div class="box-body">
						<div id="form-pesan">
						</div>
						
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Username</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="username" name="username" placeholder="Username" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password"/>
                            </div>
                        </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                        <button type="submit" id="btn-login" class="btn btn-info pull-right" >Login</button>
                    </div><!-- /.box-footer -->
                </form>
            </div><!-- /.box -->
        <div class="col-md-3"></div>
    </div>
</section><!-- /.content -->
</div>

    <div class="modal" id="modal-proses" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="basicModal" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-body">
                    Data Sedang diproses...
				</div>
			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div><!-- /.modal -->

<script type="text/javascript">
    $(function () {
        $('#username').focus();   
        
        $('#btn-login').click(function(){
            $('#form-login').submit();
        });
        
        $('#form-login').submit(function(){
            $("#modal-proses").modal('show');
                $.ajax({
                    url:"<?php echo site_url(); ?>/manager/welcome/login",
     			    type:"POST",
     			    data:$('#form-login').serialize(),
     			    cache: false,
      		        success:function(respon){
         		    	var obj = $.parseJSON(respon);
      		            if(obj.status==1){
      		                window.open("<?php echo site_url(); ?>/manager/dashboard","_self");
          		        }else{
                            $('#form-pesan').html(pesan_err(obj.error));
                            $("#modal-proses").modal('hide');
          		        }
         			}
      		});
            
      		return false;
        });    
    });
</script>