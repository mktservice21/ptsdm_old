<?PHP

    include "config/koneksimysqli_it.php";
    include "config/koneksimysqli.php";
    $cnmy=$cnmy;
    $print = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_br0_all WHERE brId='$_GET[brid]'");
    $p    = mysqli_fetch_array($print);
    $rpjumlah=$p['ccyId']." ".number_format($p['jumlah'],0,",",".");
    //$rprelalisasi=$p['ccyId']." ".number_format($p['realisasi1'],0,",",".");
    $rprelalisasi=$p['realisasi1'];
    $rpcn=$p['ccyId']." ".number_format($p['cn'],0,",",".");
    $tglinput = date('d F Y', strtotime($p['tgl']));
    $tgltrans = date('d F Y', strtotime($p['tgltrans']));

?>

 <script type="text/javascript">     
    function PrintDiv() {    
       var divToPrint = document.getElementById('divToPrint');
       var popupWin = window.open('', '_blank', 'width=700,height=500');
       popupWin.document.open();
       popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
 </script>
 
<div id="divToPrint">
    <!--row-->
    <div class="row">
        <h2 align='center'>Budget Request</h2><div class="clearfix"></div>
            
        <table width='100%' border='0'>
            <tr valign='top'>
                <td><b>MR/SPV/AM/DM : <?PHP echo $p['nama'];?></b></td>
                <td align='right'>NO.: ............. / .............</td>
            </tr>
        </table>

        <hr/>
        <!--kiri-->
        <div id='p-kiri'>
            <table border='0' cellpadding='5'>
                <tr valign='top'>
                    <td class=''>KODE</td><td>:</td><td><?PHP echo $p['nama_kode'];?></td>
                </tr>
                <tr valign='top'>
                    <td class='height-ket' valign='top'>AKTIVITAS</td><td>:</td><td><?PHP echo $p['aktivitas1'];?></td>
                <tr valign='top'>
                    <td class=''>JUMLAH</td><td>:</td><td><?PHP echo $rpjumlah; ?></td>
                </tr>
                <tr valign='top'>
                    <td class='height-ket'>KETERANGAN</td><td>:</td><td><?PHP //echo $p['aktivitas2'];?></td>
                </tr>
                <tr valign='top'>
                    <td class=''>REALISASI</td><td>:</td><td><?PHP echo $rprelalisasi;?></td>
                </tr>
            </table>
        </div>

        <!--kanan-->
        <div id='p-kanan'>
            YANG MEMBUAT : <P/>&nbsp;<P/>&nbsp;
            <hr/>
            MENGETAHUI : <P/>&nbsp;<P/>&nbsp;
            <hr/>
            DISETUJUI : <P/>&nbsp;<P/>&nbsp;
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<input type="button" value="print" onclick="PrintDiv();" />
<style>
#p-kiri {
    margin-top  : 10px;
    float   : left;
    width   : 65%;
    padding-right   : 10px;
}
#p-kanan {
    margin-top  : 10px;
    padding-left: 5px;
    float   : left;
    width   : 29%;
    border-left: 1px solid #cccccc;
}
tr {
    padding-bottom: 10px;
}
.height-ket {
    height: 80px;
}
.clearfix {
    clear: both;
}
</style>