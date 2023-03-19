<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>
		Tes
		<small>Menambah tes, mengubah tes, dan menghapus tes</small>
	</h1>
	<ol class="breadcrumb">
		<li><a href="<?php echo site_url(); ?>/"><i class="fa fa-dashboard"></i> Home</a></li>
		<li class="active">Tambah tes</li>
	</ol>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
        <div class="col-xs-12">
            <div class="box">
                <?php echo form_open($url.'/tambah_tes','id="form-tambah-tes"  class="form-horizontal"'); ?>
                <div class="box-header with-border">
                    <div class="box-title">Mengelola Tes</div>
                </div><!-- /.box-header -->

                <div class="box-body">
                    <div class="col-xs-6">
                        <div id="form-pesan-tes"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nama</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="tambah-id" id="tambah-id" />
                                <input type="hidden" name="tambah-nama-lama" id="tambah-nama-lama" />
                                <input type="text" name="tambah-nama" id="tambah-nama" class="form-control input-sm" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Deskripsi</label>
                            <div class="col-sm-9">
                                <textarea name="tambah-deskripsi" id="tambah-deskripsi" class="form-control input-sm" ></textarea>
                            </div>
                        </div>
						<div class="form-group">
                            <label class="col-sm-3 control-label">Rentang Waktu</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-clock-o"></i>
                                    </div>
                                    <input type="text" name="tambah-rentang-waktu" id="tambah-rentang-waktu" class="form-control input-sm" value="<?php if(!empty($rentang_waktu)){ echo $rentang_waktu; } ?>" readonly />
                                </div>
                                <p class="help-block">Rentang waktu tes dilaksanakan</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Group</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="tambah-group" name="tambah-group[]" size="22" multiple>
                                    <?php if(!empty($select_group)){ echo $select_group; } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Waktu Tes</label>
                            <div class="col-sm-9">
                                <input type="text" name="tambah-waktu" id="tambah-waktu" class="form-control input-sm" value="30" />
                                <p class="help-block">Waktu tes dalam satuan menit</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Poin Dasar</label>
                            <div class="col-sm-9">
                                <input type="text" name="tambah-poin" id="tambah-poin" class="form-control input-sm" value="1.00" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jawaban Salah</label>
                            <div class="col-sm-9">
                                <input type="text" name="tambah-poin-salah" id="tambah-poin-salah" class="form-control input-sm" value="0.00" />
                                <p class="help-block">Poin untuk jawaban salah</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jawaban Kosong</label>
                            <div class="col-sm-9">
                                <input type="text" name="tambah-poin-kosong" id="tambah-poin-kosong" class="form-control input-sm" value="0.00" />
                                <p class="help-block">Poin untuk jawaban kosong</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tunjukkan Hasil</label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="tambah-tunjukkan-hasil" id="tambah-tunjukkan-hasil" value="1" checked>
                                <p class="help-block">Menunjukkan hasil ke user saat tes sudah selesai</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Detail Hasil</label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="tambah-detail-hasil" id="tambah-detail-hasil" value="1" >
                                <p class="help-block">Menunjukkan detail jawaban ke user saat tes sudah selesai</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Token</label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="tambah-token" id="tambah-token" value="1" >
                                <p class="help-block">Saat awal tes, user memasukkan Token dari operator</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" id="btn-tambah-simpan" class="btn btn-primary pull-right">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row hide" id="kolom-soal">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">Tambah Soal <div id="judul-tambah-soal"></div></div>
                </div><!-- /.box-header -->
                <div class="box-body">
                    <div class="col-xs-6">
                        <?php echo form_open($url.'/tambah_soal','id="form-tambah-soal"  class="form-horizontal"'); ?>
                        <div id="form-pesan-soal"></div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Modul</label>
                            <div class="col-sm-9">
                                <input type="hidden" name="soal-tes-id" id="soal-tes-id">
                                <select class="form-control input-sm" id="soal-modul" name="soal-modul" >
                                    <?php if(!empty($select_modul)){ echo $select_modul; } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Topik</label>
                            <div class="col-sm-9">
                                <select style="width: 100%" class="form-control input-sm" id="soal-topik" name="soal-topik" >
                                    <div id="soal-topik-option">
                                    <option value="kosong">Pilih Topik</option>
                                    </div>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tipe Soal</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="soal-tipe" name="soal-tipe" >
                                    <option value="0">Semua</option>
                                    <option value="1">Pilihan Ganda</option>
                                    <option value="2">Essay</option>
                                    <option value="3">Jawaban Singkat</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Tingkat Kesulitan</label>
                            <div class="col-sm-9">
                                <select class="form-control input-sm" id="soal-kesulitan" name="soal-kesulitan" >
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jml Soal</label>
                            <div class="col-sm-9">
                                <input type="text" name="soal-jml" id="soal-jml" class="form-control input-sm" value="2" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Jml Jawaban</label>
                            <div class="col-sm-9">
                                <input type="text" name="soal-jml-jawaban" id="soal-jml-jawaban" class="form-control input-sm" value="3" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Acak Soal</label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="soal-acak-soal" id="soal-acak-soal" class="input-sm" value="1" checked>
                                <p class="help-block">Mengacak Soal Tes</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Acak Jawaban</label>
                            <div class="col-sm-9">
                                <input type="checkbox" name="soal-acak-jawaban" id="soal-acak-jawaban" class="input-sm" value="1" checked>
                                <p class="help-block">Mengacak Jawaban Tes</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-9">
                                <button type="submit" id="btn-tambah-soal" class="btn btn-primary pull-right">Tambah Soal</button>
                            </div>
                        </div>
                        </form>
                    </div>
                    <div class="col-xs-6">
                        <table id="table-soal" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Topik</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> </td>
                                </tr>
                            </tbody>
                        </table>                          
                    </div>
                </div>
                <div class="box-footer">
                    <button type="button" id="btn-tambah-daftar" class="btn btn-default">Daftar Tes</button>
                    <button type="button" id="btn-tambah-selesai" class="btn btn-primary pull-right">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</section><!-- /.content -->



<script lang="javascript">
    function refresh_table(){
        $('#table-soal').dataTable().fnReloadAjax();
    }

    function refresh_topik(){
        $("#modal-proses").modal('show');
        var modul = $('#soal-modul').val();
        $.getJSON('<?php echo site_url().'/'.$url; ?>/get_topik_by_modul/'+modul, function(data){
            if(data.data==1){
                $('#soal-topik').html(data.select_topik);
            }
            $("#modal-proses").modal('hide');
        });
    }

    function edit(id){
        $("#modal-proses").modal('show');
        $.getJSON('<?php echo site_url().'/'.$url; ?>/get_by_id/'+id+'', function(data){
            if(data.data==1){
                $('#tambah-id').val(data.id);
                $('#soal-tes-id').val(data.id);

                $('#tambah-nama').val(data.nama);
                $('#tambah-nama-lama').val(data.nama);
                $('#tambah-deskripsi').val(data.deskripsi);
                $('#tambah-waktu').val(data.waktu);
                $('#tambah-poin').val(data.poin);
                $('#tambah-poin-kosong').val(data.poin_kosong);
                $('#tambah-poin-salah').val(data.poin_salah);
                $('#tambah-rentang-waktu').val(data.rentang_waktu);
                if(data.tunjukkan_hasil==1){
                    $('#tambah-tunjukkan-hasil').prop("checked", true);
                }else{
                    $('#tambah-tunjukkan-hasil').prop("checked", false);
                }
                if(data.detail_hasil==1){
                    $('#tambah-detail-hasil').prop("checked", true);
                }else{
                    $('#tambah-detail-hasil').prop("checked", false);
                }
                if(data.token==1){
                    $('#tambah-token').prop("checked", true);
                }else{
                    $('#tambah-token').prop("checked", false);
                }

                refresh_topik();
                refresh_table();

                $('#kolom-soal').removeClass('hide');
            }
            $("#modal-proses").modal('hide');
        });
    }

    function hapus_soal(id){
        $("#modal-proses").modal('show');
        $.getJSON('<?php echo site_url().'/'.$url; ?>/hapus_soal_by_id/'+id+'', function(data){
            if(data.data==1){
                notify_success(data.pesan);

                refresh_table();
            }else{
                notify_error(data.pesan);                
            }
            $("#modal-proses").modal('hide');
        });
    }

    function selesai(){
        $('#tambah-id').val('');
        $('#tambah-nama').val('');
        $('#tambah-nama-lama').val('');
        $('#tambah-deskripsi').val('');
        $('#tambah-waktu').val('30');
        $('#tambah-poin').val('1.00');
        $('#tambah-poin-kosong').val('0.00');
        $('#tambah-poin-salah').val('0.00');
        $('#tambah-rentang-waktu').val('<?php if(!empty($rentang_waktu)){ echo $rentang_waktu; } ?>');
        $('#tambah-group option:selected').removeAttr('selected');
        $('#tambah-acak-jawaban').prop("checked", true);

        $('#soal-tes-id').val('');

        $('#kolom-soal').addClass('hide');
        $('#tambah-nama').focus();
    }

    $(function(){
        $('#tambah-rentang-waktu').daterangepicker({timePicker: true, timePickerIncrement: 10, format: 'YYYY-MM-DD H:mm'});
        $('#btn-tambah-selesai').click(function(){
            window.open("<?php echo site_url(); ?>/manager/tes_tambah", "_self");
        });

        $('#btn-tambah-daftar').click(function(){
            window.open("<?php echo site_url(); ?>/manager/tes_daftar", "_self");
        });

        $("#soal-modul").change(function(){
            refresh_topik();
        });

        $('#form-tambah-tes').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/tambah_tes",
                    type:"POST",
                    data:$('#form-tambah-tes').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            $('#form-pesan-tes').html('');
                            $("#tambah-id").val(obj.tes_id);
                            $("#tambah-nama-lama").val(obj.tes_nama);
                            // menampilkan tambah soal
                            refresh_topik()
                            $("#soal-tes-id").val(obj.tes_id);
                            $('#kolom-soal').removeClass('hide');
                            $("#modal-proses").modal('hide');
                            
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-tes').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });

        $('#form-tambah-soal').submit(function(){
            $("#modal-proses").modal('show');
            $.ajax({
                    url:"<?php echo site_url().'/'.$url; ?>/tambah_soal",
                    type:"POST",
                    data:$('#form-tambah-soal').serialize(),
                    cache: false,
                    success:function(respon){
                        var obj = $.parseJSON(respon);
                        if(obj.status==1){
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-soal').html('');
                            refresh_table();                            
                            notify_success(obj.pesan);
                        }else{
                            $("#modal-proses").modal('hide');
                            $('#form-pesan-soal').html(pesan_err(obj.pesan));
                        }
                    }
            });
            return false;
        });

        $('#table-soal').DataTable({
                  "paging": false,
                  "iDisplayLength":10,
                  "bProcessing": false,
                  "bServerSide": true, 
                  "searching": false,
                  "aoColumns": [
    					{"bSearchable": false, "bSortable": false, "sWidth":"20px"},
    					{"bSearchable": false, "bSortable": false},
                        {"bSearchable": false, "bSortable": false, "sWidth":"30px"}],
                  "sAjaxSource": "<?php echo site_url().'/'.$url; ?>/get_datatable_soal/",
                  "autoWidth": false,
                  "fnServerParams": function ( aoData ) {
                    aoData.push( { "name": "tes-id", "value": $('#soal-tes-id').val()} );
                  }
         });  

        <?php if(!empty($data_tes)){ echo $data_tes; } ?>
    });
</script>