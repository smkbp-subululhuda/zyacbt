<div class="container">
	<!-- Content Header (Page header) -->
    <section class="content-header">
    	<h1>
    		Lanjutkan Tes
            <small>Silahkan periksa kembali data tes yang akan dilanjutkan</small>
        </h1>
	</section>

	<!-- Main content -->
    <section class="content">
        <?php echo form_open($url.'/lanjutkan_tes','id="form-lanjutkan-tes"  class="form-horizontal"'); ?>
        <div class="box box-success box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">Konfirmasi Data Tes</h3>
            </div><!-- /.box-header -->
            <div class="box-body">
                <div class="box-body no-padding">
                    <div id="form-pesan"></div>
                    <input type="hidden" name="tes-id" id="tes-id" value="<?php if(!empty($tes_id)){ echo $tes_id; } ?>">
                    <table class="table table-striped">
                        <tr style="height: 45px;">
                            <td style="vertical-align: middle;"></td>
                            <td style="vertical-align: middle;text-align: right;">Nama Peserta : </td>
                            <td style="vertical-align: middle;"><b><?php if(!empty($nama)){ echo $nama; } ?></b></td>
                            <td></td>
                        </tr>
                        <tr style="height: 45px;">
                            <td style="vertical-align: middle;"></td>
                            <td style="vertical-align: middle;text-align: right;">Tes : </td>
                            <td style="vertical-align: middle;"><b><?php if(!empty($tes_nama)){ echo $tes_nama; } ?></b></td>
                            <td></td>
                        </tr>
						<tr style="height: 45px;">
                            <td style="vertical-align: middle;"></td>
                            <td style="vertical-align: middle;text-align: right;">Deskripsi : </td>
                            <td style="vertical-align: middle;"><b><?php if(!empty($tes_detail)){ echo $tes_detail; } ?></b></td>
                            <td></td>
                        </tr>
                        <tr style="height: 45px;">
							<td></td>
		                    <td style="vertical-align: middle;text-align: right;">Token : </td>
		                    <td style="vertical-align: middle;"><input type="text" name="token" id="token" autocomplete="off"></td>
		                    <td></td>
						</tr>
                  </table>
            </div><!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" id="btn-tambah-simpan" class="btn btn-primary pull-right">Kerjakan</button>
            </div>
        </div><!-- /.box -->
        </form>
    </section><!-- /.content -->
</div><!-- /.container -->

<script type="text/javascript">
    $(function () {
        $('#form-lanjutkan-tes').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/lanjutkan_tes",
                    type:"POST",
                    data:$('#form-lanjutkan-tes').serialize(),
                    cache: false,
                    timeout: 10000,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html('');
                            window.location.reload();
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err(obj.pesan));
                        }
                    },
                    statusCode: {
                        500: function(respon) {
                            $("#modal-proses").modal('hide');
                            $('#form-pesan').html(pesan_err('Terjadi kesalahan pada Tes. Silahkan hubungi petugas.'));
                        }
                    },
                    error: function(xmlhttprequest, textstatus, message) {
                        if(textstatus==="timeout") {
                            $("#modal-proses").modal('hide');
                            notify_error("Gagal melanjutkan Tes, Halaman akan di Refresh !");
                            setInterval(function() {
                                window.location.reload();
                            }, 4000);
                        }else{
                            $("#modal-proses").modal('hide');
                            notify_error(textstatus);
							setInterval(function() {
                                window.location.reload();
                            }, 1000);
                        }
                    }
            });
            return false;
        });
    });
</script>