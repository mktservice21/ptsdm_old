<?PHP

    include "config/koneksimysqli_it.php";
    $cnmy=$cnit;
    $print = mysqli_query($cnmy, "SELECT * FROM dbmaster.v_br_otc_all WHERE brOtcId='$_GET[brid]'");
    $p    = mysqli_fetch_array($print);
    $rpjumlah="Rp. &nbsp;&nbsp; ".number_format($p['jumlah'],0,",",".");
    $cabang=$p['nama_cabang'];
    $keterangan1=$p['keterangan1'];
    $keterangan2=$p['keterangan2'];
    $relalisasi=$p['real1'];
    $bankreal=$p['bankreal1'];
    $cabangreal=$p['cbreal1'];
    $norekreal=$p['norekreal1'];
    
    
    //$rprelalisasi=$p['ccyId']." ".number_format($p['realisasi1'],0,",",".");
    
    //$rpcn=$p['ccyId']." ".number_format($p['cn'],0,",",".");
    //$tglinput = date('d F Y', strtotime($p['tgl']));
    //$tgltrans = date('d F Y', strtotime($p['tgltrans']));

?>

 <script type="text/javascript">     
    function PrintDiv() {    
       var divToPrint = document.getElementById('divToPrint');
       var popupWin = window.open('', '_blank', 'width=800,height=500');
       popupWin.document.open();
       popupWin.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</html>');
        popupWin.document.close();
            }
 </script>
 
<div id="divToPrint">
    <page size="A5" layout="portrait">
        <div id="kotakjudul">
            <div id="isikiri">
                &nbsp; &nbsp; &nbsp; &nbsp; 
            </div>
            <div id="isikanan">
                <br/>&nbsp;<br/>&nbsp;<br/>&nbsp;
                <?PHP echo $cabang; ?>
            </div>
            <div class="clearfix"></div>
                
        </div>
        <div class="clearfix"></div>
        
        <div id="kotakisi">
            <!--kiri-->
            <div id='p-kiri'>
                <div id="isikiri">
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                </div>
                <div id="isikanan">
                    <div class="kotak0">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    <div class="kotak1">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                    <div class="kotak2"><?PHP echo $keterangan2; ?></div>
                    <div class="kotak3"><?PHP echo "<b>".$rpjumlah."</b>"; ?></div>
                    <div class="kotak4"><?PHP echo $keterangan1; ?></div>
                    <div class="kotak5"><?PHP echo $relalisasi."<br/>".$bankreal."<br/>".$cabangreal."<br/>".$norekreal; ?></div>
                </div>
                <div class="clearfix"></div>
            </div>

            <!--kanan-->
            <div id='p-kanan'>
                
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="clearfix"></div>
    </page>
</div>
<!--<input type="button" value="print" onclick="PrintDiv();" />-->
<style>
body {
  /*background: rgb(204,204,204); */
}
page {
  background: white;
  display: block;
  margin: 0 auto;
  margin-bottom: 0.5cm;
  /*box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
}
page[size="A5"][layout="portrait"] {
  width: 21cm;
  height: 16.6cm;  
}
#kotakjudul {
    border: 0px solid #000;
    width:100%;
    height: 2.3cm;
}
#kotakisi {
    border: 0px solid #000;
    width:100%;
    height: 12.5cm;
}
#p-kiri {
    float   : left;
    width   : 14.25cm;
    border-left: 0px solid #000;
}
#p-kanan {
    float   : left;
    width   : 6cm;
    border-left: 0px solid #000;
}

#isikiri {
    float   : left;
    width   : 4.5cm;
    border-left: 0px solid #000;
}
#isikanan {
    float   : left;
    width   : 9cm;
    border-left: 0px solid #000;
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
.kotak0 {
    height: 0.5cm;
}
.kotak1 {
    height: 1.5cm;
}
.kotak2 {
    height: 2.7cm;
}
.kotak3 {
    height: 1.2cm;
}
.kotak4 {
    height: 3.6cm;
}
.kotak5 {
    height: 3.5cm;
}
</style>

 <script type="text/javascript">
      window.onload = function() { window.print(); }
 </script>