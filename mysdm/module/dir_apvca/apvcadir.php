<?php
    $aksi="module/dir_apvca/aksi_apvcadir.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-2 month', strtotime($hari_ini)));
    $tgl_akhir = date('F Y', strtotime($hari_ini));
    $apvpilih="approve";
    $userid=$_SESSION['IDCARD'];
    
    if (!empty($_SESSION['DIRCAAPVTGL1'])) $tgl_pertama = $_SESSION['DIRCAAPVTGL1'];
    if (!empty($_SESSION['DIRCAAPVTGL2'])) $tgl_akhir = $_SESSION['DIRCAAPVTGL2'];
    if (!empty($_SESSION['DIRCAAPVKET'])) $apvpilih = $_SESSION['DIRCAAPVKET'];
    
?>
<div class="">
    
    <div class="col-md-12 col-sm-12 col-xs-12">
        <div class="title_left">
            <h3>Approve Cash Advance</h3>
        </div>
    </div>
    <div class="clearfix"></div>
    
    <div class="row">
        
        <form name='form1' id='form1' method='POST' action='' enctype='multipart/form-data'>
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div class='x_panel'>
                    
                    
                    <div class="well" style="overflow: auto; margin-top: -5px; margin-bottom: 5px; padding-top: 10px; padding-bottom: 6px;">
                        <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Belum Approve'>
                        <input onclick="pilihData('unapprove')" class='btn btn-success btn-sm' type='button' name='buttonview2' value='Lihat Sudah Approve'>
                        <input onclick="pilihData('reject')" class='btn btn-danger btn-sm' type='button' name='buttonview4' value='Data Reject'>
                        <a class='btn btn-default btn-sm' href="<?PHP echo "?module=home"; ?>">Home</a>
                    </div>
                    
                     <div hidden class='col-sm-2'>
                        &nbsp;
                        <div class="form-group">
                            <div class='input-group date'>
                                <input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
                                <input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                    
                    <div class='col-sm-2'>
                        Periode Input
                        <div class="form-group">
                            <div class='input-group date' id='cbln01'>
                                <input type='text' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                     <div class='col-sm-2'>
                        <small>s/d.</small>
                        <div class="form-group">
                            <div class='input-group date' id='cbln02'>
                                <input type='text' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>
                                <span class="input-group-addon">
                                   <span class="glyphicon glyphicon-calendar"></span>
                                </span>
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
                                        <th>Yg Membuat</th>
                                        <th width='80px'>Jumlah</th>
                                        <th width='100px'>Tgl. Input</th>
                                        <th width='50px'>Periode</th>
                                        <th width='50px'>Bukti</th>
                                        <th width='50px'>Keterangan</th>
                                        <th width='50px'>ID</th>
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
        var ekaryawan=document.getElementById('e_idkaryawan').value;
        
        $("#loading").html("<center><img src='images/loading.gif' width='50px'/></center>");
        $.ajax({
            type:"post",
            url:"module/dir_apvca/viewdatatable.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukaryawan="+ekaryawan,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
            }
        });
        
    }
</script>