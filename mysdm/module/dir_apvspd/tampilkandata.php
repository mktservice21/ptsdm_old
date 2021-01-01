<?php
    $aksi="module/dir_apvspd/aksi_apvspddir.php";
    $hari_ini = date("Y-m-d");
    $tgl_pertama = date('F Y', strtotime('-1 month', strtotime($hari_ini)));
    //$tgl_pertama = date('F Y', strtotime($hari_ini));
	$tgl_akhir = date('F Y', strtotime($hari_ini));
    $apvpilih="approve";
    $userid=$_SESSION['IDCARD'];
    
    if (!empty($_SESSION['DIRSPDAPVTGL1'])) $tgl_pertama = $_SESSION['DIRSPDAPVTGL1'];
    if (!empty($_SESSION['DIRSPDAPVTGL2'])) $tgl_akhir = $_SESSION['DIRSPDAPVTGL2'];
    if (!empty($_SESSION['DIRSPDAPVKET'])) $apvpilih = $_SESSION['DIRSPDAPVKET'];
  
    
?>

<input type='hidden' class='form-control input-sm' id='e_apvpilih' name='e_apvpilih' value='<?PHP echo $apvpilih; ?>' Readonly>
<input type='hidden' class='form-control' id='e_idkaryawan' name='e_idkaryawan' value='<?PHP echo $userid; ?>' Readonly>
<input type='hidden' id='tgl1' name='e_periode01' required='required' class='form-control input-sm' placeholder='tgl awal' value='<?PHP echo $tgl_pertama; ?>' placeholder='dd mmm yyyy' Readonly>
<input type='hidden' id='tgl2' name='e_periode02' required='required' class='form-control input-sm' placeholder='tgl akhir' value='<?PHP echo $tgl_akhir; ?>' placeholder='dd mmm yyyy' Readonly>


<div>
    <input onclick="pilihData('approve')" class='btn btn-warning btn-sm' type='button' name='buttonview1' value='Lihat Belum Approve'>
    <input onclick="pilihData('unapprove')" class='btn btn-success btn-sm' type='button' name='buttonview2' value='Lihat Sudah Approve'>
    <a class='btn btn-default btn-sm' href="<?PHP echo "media.php?module=home"; ?>">Home</a>
</div>



<div>
    <div id='loading'></div>
    <div id='c-data'>
        <div class='x_content'>

            <table id='dtablecadir' width='100%'>
                <thead>
                    <tr>
                        <th>No</th>
                        <th></th>
                        <th>No BR/Divisi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

        </div>
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
            url:"module/dir_apvspd/viewdatatable_ttd_dir.php?module="+ket,
            data:"eket="+ket+"&uperiode1="+etgl1+"&uperiode2="+etgl2+"&ukaryawan="+ekaryawan,
            success:function(data){
                $("#c-data").html(data);
                $("#loading").html("");
                if (ket=="approve"){
                    n_ttd_1.style.display = 'block';
                    n_ttd_2.style.display = 'none';
                }else{
                    n_ttd_1.style.display = 'none';
                    n_ttd_2.style.display = 'block';
                }
            }
        });
        
    }
</script>