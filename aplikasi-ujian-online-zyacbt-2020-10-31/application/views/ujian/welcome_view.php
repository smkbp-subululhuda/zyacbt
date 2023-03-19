<div class="container">
	<!-- Content Header (Page header) -->
    <section class="content-header">
    	<h1>
    		<?php if(!empty($site_name)){ echo $site_name; } ?>
            <small><?php if(!empty($cbt_keterangan)){ echo $cbt_keterangan; }else{ echo 'Ujian Online Berbasis Komputer'; } ?></small>
        </h1>
        <ol class="breadcrumb">
        	<li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Selamat Datang</li>
        </ol>
	</section>

	<!-- Main content -->
    <section class="content">
    	<div class="row">
    	<?php echo form_open('welcome/login','id="form-login" class="form-horizontal"')?>
    	</div>
    	<div class="row">
    		<div class="login-box">
    			<div class="login-logo">
        			<b>User Login</b>
      			</div><!-- /.login-logo -->
      			<div class="login-box-body">
        			<p class="login-box-msg">Masukkan Username dan Password</p>
                <div id="form-pesan"></div>
          			<div class="form-group has-feedback">
            			<input type="text" id="username" autocomplete="off" name="username" class="form-control" placeholder="Username"/>
            			<span class="glyphicon glyphicon-user form-control-feedback"></span>
          			</div>
          		<div class="form-group has-feedback">
            		<input type="password" id="password" autocomplete="off" name="password" class="form-control" placeholder="Password"/>
            		<span class="glyphicon glyphicon-lock form-control-feedback"></span>
          		</div>
          		<div class="row">
		            <div class="col-xs-8">                          
                  <div class="checkbox icheck">
                    <label>
                      <input type="checkbox" id="show-password"> Show Password
                    </label>
                  </div>    
		            </div><!-- /.col -->
		            <div class="col-xs-4">
		              	<button type="submit" class="btn btn-primary btn-block btn-flat">Login</button>
		            </div><!-- /.col -->
	          	</div>
    		</div><!-- /.login-box -->
    	</div>
    </section><!-- /.content -->
</div><!-- /.container -->

<script type="text/javascript">
    function showpassword(){
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }
    $(function () {
        $('#username').focus(); 

        $('#show-password').iCheck({
          checkboxClass: 'icheckbox_square-blue',
          radioClass: 'iradio_square-blue',
          increaseArea: '20%' // optional
        });  

        $('#show-password').on('ifChanged', function(event){
          showpassword();
        });
        
        $('#form-login').submit(function(){
          $("#modal-proses").modal('show');
            $.ajax({
              url:"<?php echo site_url(); ?>/welcome/login",
     			    type:"POST",
     			    data:$('#form-login').serialize(),
     			    cache: false,
      		        success:function(respon){
         		    	var obj = $.parseJSON(respon);
      		            if(obj.status==1){
      		                window.open("<?php echo site_url(); ?>/tes_dashboard","_self");
          		        }else{
                            $('#form-pesan').html(pesan_err(obj.error));
                            $("#modal-proses").modal('hide');
                            $('#username').focus();   
          		        }
         			}
      		});
            
      		return false;
        });    
    });
</script>