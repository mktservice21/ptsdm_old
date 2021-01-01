<?PHP
    date_default_timezone_set('Asia/Jakarta');
    $aksi="module/mod_fin_cekprosbrcab/aksi_cekprosbrcab.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('01 F Y', strtotime('-1 month', strtotime($hari_ini)));
    $tgl_akhir = date('t F Y', strtotime($hari_ini));
    
    $userid = trim($_SESSION['IDCARD']);
    $namauser = trim($_SESSION['NAMALENGKAP']);
    
    $apvpilih="approve";
    
    if (!empty($_SESSION['PSFBRC_TGL1'])) $tgl_pertama = $_SESSION['PSFBRC_TGL1'];
    if (!empty($_SESSION['PSFBRC_TGL2'])) $tgl_akhir = $_SESSION['PSFBRC_TGL2'];
    if (!empty($_SESSION['PSFBRC_KET'])) $apvpilih = $_SESSION['PSFBRC_KET'];
    
?>

<div class='modal fade' id='myModal' role='dialog'></div>
<div class="">

    <div class="page-title">
        <div class="title_left">
            <h3>
                Cek Proses BR Tiket dan Hotel
            </h3>
        </div></div><div class="clearfix">
    </div>
    
    <!--row-->
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                        <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Belum Booking'>
                        <input onclick="pilihData('unapprove')" class='btn btn-success btn-sm' type='button' name='buttonview2' value='Lihat Sudah Booking'>
                        <input onclick="pilihData('isiissued')" class='btn btn-dark btn-sm' type='button' name='buttonview6' value='Issued'>
                        <input onclick="pilihData('pending')" class='btn btn-default btn-sm' type='hidden' name='buttonview3' value='Data Pending'>
                        <input onclick="pilihData('reject')" class='btn btn-danger btn-sm' type='button' name='buttonview4' value='Data Reject'>
                        <input onclick="pilihData('semua')" class='btn btn-info btn-sm' type='button' name='buttonview4' value='All Data'>
                        <input onclick="pilihData('belumapvsm')" class='btn btn-default btn-sm' type='hidden' name='buttonview5' value='Belum Approve SM'>
                    </div>
                    
                    <div hidden class='col-sm-3'>
                        <small>Approve Employee :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class='col-sm-3'>
                        Periode BR
                        <div class="form-group">
                            <div class='input-group date' id='tgl01'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                     <div class='col-sm-3'>
                        <small>s/d.</small>
                        <div class="form-group">
                            <div class='input-group date' id='tgl02'>
                                <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                     <div hidden class='col-sm-3'>
                        <small>Approve Employee :</small>
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_lvlposisi' name='e_lvlposisi' value='<?PHP echo $lvlposisi; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
                                <input type='text' class='form-control input-sm' id='e_karyawan' name='e_karyawan' value='<?PHP echo $namauser; ?>' Readonly>
                                <input type='hidden' class='form-control input-sm' id='e_ketapv' name='e_ketapv' value='<?PHP echo $stsapv; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div id='loading'></div>
                    <div id='c-data'>
                        <div class='x_content'>
                            
                            <table id='datatable' class='table table-striped table-bordered' width='100%'>
                                <thead>
                                    <tr>
                                        <th width='7px'>No</th>
                                        <th width='20px'>
                                            <input type="checkbox" id="chkbtnbr" value="select" 
                                            onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" />
                                        </th>
                                        <th width='60px'>Tanggal</th>
                                        <th>Yg Membuat</th>
                                        <th width='80px'>Cabang</th>
                                        <th width='100px'>Dokter</th>
                                        <th width='50px'>Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
            
        </form>
        
    </div>
</div>

<script>
    $(document).ready(function() {
        var eapvpilih=document.getElementById('e_apvpilih').value;
        pilihData(eapvpilih);
    } );
    
    function pilihData(ket){
        var etgl1=document.getElementById('tgl1').value;
        var etgl2=document.getElementById('tgl2').value;
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/mod_fin_cekprosbrcab/viewdatatable.php?module="+module+"&idmenu="+idmenu,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
    }
</script>