<?php
session_start();


    if (!isset($_SESSION['USERID'])) {
        echo "ANDA HARUS LOGIN ULANG....";
        exit;
    }
    
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","10G");
    ini_set('max_execution_time', 0);
    
    $pmodule=$_GET['module'];
    $pact=$_GET['act'];
    $pidmenu=$_GET['idmenu'];
    
    include "../../config/koneksimysqli.php";
    
    $mytgl1 = $_POST['ubulan'];
    $mytgl2 = $_POST['ubulan2'];
    $pdivisi = $_POST['udivprod'];
    $pcabangid = $_POST['ucabang'];
    $pwewenang = $_POST['uwwnpilihan'];
    $pkeypilih = $_POST['ukey'];
    
    
    $_SESSION['BRGSJBTGL1']=$mytgl1;
    $_SESSION['BRGSJBTGL2']=$mytgl2;
    $_SESSION['BRGSJBDIVI']=$pdivisi;
    $_SESSION['BRGSJBCABA']=$pcabangid;
    $_SESSION['BRGSJBKEYS']=$pkeypilih;
    
    $phidden_ekse="";
    if ($pkeypilih=="2") $phidden_ekse="hidden";
    
    $pbulan1= date("Ym", strtotime($mytgl1));
    $pbulan2= date("Ym", strtotime($mytgl2));
    
    
    $fkaryawan=$_SESSION['IDCARD'];
    $pidusergroup=$_SESSION['GROUP'];
    
    $filtercabang="";
    if (!empty($pcabangid)) {
        if ($pdivisi=="OT") {
            $filtercabang=" AND a.ICABANGID_O='$pcabangid' ";
        }elseif ($pdivisi=="ET") {
            $filtercabang=" AND a.ICABANGID='$pcabangid' ";
        }
    }else{
        if ($pdivisi=="OT") {
            $filtercabang=" AND a.ICABANGID_O IN ";
        }elseif ($pdivisi=="ET") {
            $filtercabang=" AND a.ICABANGID IN ";
        }
        $filtercabang .=" (select ifnull(icabangid,'') from hrd.rsm_auth where karyawanid='$fkaryawan') ";
        
    }
    
    if ($pidusergroup=="1") {
        if (empty($pcabangid)) {
            $filtercabang="";
        }
    }
    
    
    $userid=$_SESSION['USERID'];
    $now=date("mdYhis");
    $tmp01 =" dbtemp.TMPGMCTPSKB01_".$userid."_$now ";
    $tmp02 =" dbtemp.TMPGMCTPSKB02_".$userid."_$now ";
    $tmp03 =" dbtemp.TMPGMCTPSKB03_".$userid."_$now ";
    
    $query = "select DISTINCT 
        b.PILIHAN,a.IDKELUAR,a.TGLINPUT,a.TANGGAL, a.KARYAWANID,e.nama NAMA_KARYAWAN,a.DIVISIID,
        b.DIVISINM,a.ICABANGID,c.nama NAMA_CABANGETH,a.ICABANGID_O,d.nama NAMA_CABANGOTC, a.AREAID, a.AREAID_O, 
        a.NOTES,a.USERID,a.STSNONAKTIF,a.SYS_NOW,a.PM_APV,a.PM_TGL,a.APV1,a.APV1_TGL,f.PRINT,
        f.NORESI, f.KETKIRIM, f.TGLKIRIM,f.TGLTERIMA, f.NAMA_KARYAWAN NAMA_KARYAWANTERIMA,
        f.GRPPRINT, f.IGROUP, f.IDPENERIMA, f.NAMA_PENERIMA, f.ALAMAT1, f.ALAMAT2, f.KOTA, f.PROVINSI, f.KODEPOS, f.HP, 
        j.nama as NAMAAREAETH, k.nama as NAMAAREAOTC
        from dbmaster.t_barang_keluar a JOIN dbmaster.t_divisi_gimick b on a.DIVISIID=b.DIVISIID LEFT JOIN 
        mkt.icabang c on a.ICABANGID=c.iCabangId
        LEFT JOIN mkt.icabang_o d on a.ICABANGID_O=d.icabangid_o 
        LEFT JOIN hrd.karyawan e on a.KARYAWANID=e.karyawanId 
        LEFT JOIN dbmaster.t_barang_keluar_kirim f on a.IDKELUAR=f.IDKELUAR 
        LEFT JOIN (select * FROM dbmaster.t_barang_penerima WHERE IFNULL(AKTIF,'')='Y' AND UNTUK='ET') as h on a.ICABANGID=h.ICABANGID AND c.iCabangId=h.ICABANGID AND IFNULL(a.AREAID,'')=IFNULL(h.AREAID,'') 
        LEFT JOIN (select * FROM dbmaster.t_barang_penerima WHERE IFNULL(AKTIF,'')='Y' AND UNTUK='OT') as i on a.ICABANGID_O=i.ICABANGID AND d.iCabangId_o=i.ICABANGID_O AND IFNULL(a.AREAID_O,'')=IFNULL(i.AREAID_O,'') 
        LEFT JOIN MKT.iarea as j on a.AREAID=j.areaid AND a.ICABANGID=j.icabangid 
        LEFT JOIN MKT.iarea_o as k on a.AREAID_O=k.areaid_o AND a.ICABANGID_O=k.icabangid_o 
        WHERE IFNULL(a.STSNONAKTIF,'')<>'Y' 
        AND DATE_FORMAT(a.TANGGAL,'%Y%m') between '$pbulan1' and '$pbulan2'  ";//$filtercabang
    //$query .=" AND ( IFNULL(f.TGLKIRIM,'')<>'' AND IFNULL(f.TGLKIRIM,'0000-00-00')<>'0000-00-00' AND IFNULL(f.TGLKIRIM,'0000-00-00 00:00:00')<>'0000-00-00 00:00:00') ";
    if ($pdivisi=="AL" OR empty($pdivisi)) {
    }else{
        $query .= " AND b.PILIHAN='$pdivisi' ";
    }
    if ($pkeypilih=="1"){
        $query .= " AND IFNULL(f.GRPPRINT,'')='' ";
    }elseif ($pkeypilih=="2"){
        $query .= " AND IFNULL(f.GRPPRINT,'')<>'' ";
    }
    
    $query = "create TEMPORARY table $tmp01 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    $query = "select a.IDKELUAR, b.IDKATEGORI, c.NAMA_KATEGORI, a.IDBARANG, b.NAMABARANG, a.STOCK, a.JUMLAH from dbmaster.t_barang_keluar_d a 
        JOIN dbmaster.t_barang b on a.IDBARANG=b.IDBARANG LEFT JOIN dbmaster.t_barang_kategori c on b.IDKATEGORI=c.IDKATEGORI WHERE 
        a.IDKELUAR IN (select IFNULL(IDKELUAR,'') FROM $tmp01)";
    $query = "create TEMPORARY table $tmp02 ($query)"; 
    mysqli_query($cnmy, $query);
    $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    
    if ($pkeypilih=="1"){
        $query = "UPDATE $tmp01 a JOIN (select * FROM dbmaster.t_barang_penerima WHERE IFNULL(AKTIF,'')='Y' AND UNTUK='ET') b "
                . " on a.ICABANGID=b.ICABANGID AND IFNULL(a.AREAID,'')=IFNULL(b.AREAID,'') "
                . " SET a.IDPENERIMA=b.IDPENERIMA, a.NAMA_PENERIMA=b.NAMA_PENERIMA, "
                . " a.ALAMAT1=b.ALAMAT1, a.ALAMAT2=b.ALAMAT2, a.KOTA=b.KOTA, a.PROVINSI=b.PROVINSI, "
                . " a.KODEPOS=b.KODEPOS, a.HP=b.HP, a.IGROUP=b.IGROUP WHERE a.PILIHAN='ET'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
        
        $query = "UPDATE $tmp01 a JOIN (select * FROM dbmaster.t_barang_penerima WHERE IFNULL(AKTIF,'')='Y' AND UNTUK='OT') b "
                . " on a.ICABANGID_O=b.ICABANGID_O AND IFNULL(a.AREAID_O,'')=IFNULL(b.AREAID_O,'') "
                . " SET a.IDPENERIMA=b.IDPENERIMA, a.NAMA_PENERIMA=b.NAMA_PENERIMA, "
                . " a.ALAMAT1=b.ALAMAT1, a.ALAMAT2=b.ALAMAT2, a.KOTA=b.KOTA, a.PROVINSI=b.PROVINSI, "
                . " a.KODEPOS=b.KODEPOS, a.HP=b.HP, a.IGROUP=b.IGROUP WHERE a.PILIHAN='OT'";
        mysqli_query($cnmy, $query);
        $erropesan = mysqli_error($cnmy); if (!empty($erropesan)) { echo $erropesan; exit; }
    }
    
?>

<script>
    function SelAllCheckBox(nmbuton, data){
        var checkboxes = document.getElementsByName(data);
        var button = document.getElementById(nmbuton);
        if(button.value == 'select'){
            for (var i in checkboxes){
                checkboxes[i].checked = 'FALSE';
            }
            button.value = 'deselect'
        }else{
            for (var i in checkboxes){
                checkboxes[i].checked = '';
            }
            button.value = 'select';
        }
    }
    
    
    function TambahDataInputResi(eidgroup, eidgrpprint, eidkeluar){
        $.ajax({
            type:"post",
            url:"module/mod_brg_printskb/isi_noresi.php?module=viewisinorsi",
            data:"uidgroup="+eidgroup+"&uidgrpprint="+eidgrpprint+"&uidkeluar="+eidkeluar,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
        
    function getDataBRPenerima(data1, data2, data3){
        $.ajax({
            type:"post",
            url:"config/viewdata_ms.php?module=viewdatagimicpenerima&data1="+data1+"&data2="+data2+"&udata3="+data3,
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalPenerima(fildnya1, fildnya2, fildnya3, d1, d2, d3){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
    }
    
    function BuatSuratTugas(pText_,ket)  {
        var allnobr = "";
        
        var chk_arr =  document.getElementsByName('chkbox_br[]');
        var chklength = chk_arr.length;
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang dipilih...!!!");
            return false;
        }
        

        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        //document.write("You pressed OK!")
        document.getElementById("d-form2").action = "module/mod_brg_printskb/buatsuratjalan.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
        document.getElementById("d-form2").submit();
        return 1;
    
    }
    
    
    function disp_confirm(pText_,ket)  {
        var eidpenerima =document.getElementById('e_idpenerima').value;
        var enmpenerima =document.getElementById('e_nmpenerima').value;
        
        if (eidpenerima=="") {
            //alert("penerima masih kosong...");
            //return false;
        }
        
        if (enmpenerima=="") {
            //alert("penerima masih kosong...");
            //return false;
        }
        
        
        if (pText_=="suratjalan") {
            ket="suratjalan";
            pText_="Apakah akan buat surat jalan...???";
        }
        
        var allnobr = "";
        
        var chk_arr =  document.getElementsByName('chkbox_br[]');
        var chklength = chk_arr.length;
        var allnobr="";
        for(k=0;k< chklength;k++)
        {
            if (chk_arr[k].checked == true) {
                allnobr =allnobr + "'"+chk_arr[k].value+"',";
            }
        }
        if (allnobr.length > 0) {
            var lastIndex = allnobr.lastIndexOf(",");
            allnobr = "("+allnobr.substring(0, lastIndex)+")";
        }else{
            alert("Tidak ada data yang dipilih...!!!");
            return false;
        }
        
        
        ok_ = 1;
        if (ok_) {
            var r=confirm(pText_)
            if (r==true) {
                var myurl = window.location;
                var urlku = new URL(myurl);
                var module = urlku.searchParams.get("module");
                var idmenu = urlku.searchParams.get("idmenu");
                //document.write("You pressed OK!")
                document.getElementById("d-form2").action = "module/mod_brg_printskb/aksi_buatsjb.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("d-form2").submit();
                location.reload();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    
    }
    
    
    function ProsesDataHapusSJB(ket, noid, nidg, nidp){
        if (ket=="tandaisudahprint") {
            var cmt = confirm('Apakah akan menandai sudah pernah print, idgroup '+nidg+' ...?');
        }else{
            var cmt = confirm('Apakah akan melakukan proses hapus surat jalan id '+noid+' ...?');
        }
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        
        
       $.ajax({
           type:"post",
           url:"module/mod_brg_printskb/aksi_buatsjb.php?module="+module+"&idmenu="+idmenu+"&act="+ket+"&kethapus="+"&ket="+ket+"&id="+noid+"&idg="+nidg+"&idp="+nidp,
           data:"uid="+noid,
           success:function(data){
               KlikDataTabel('2');
           }
       });
    }
                    
</script>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<form method='POST' action='<?PHP echo "?module='$pmodule'&act=$pact&idmenu=$pidmenu"; ?>' target="_blank" id='d-form2' name='form2' data-parsley-validate class='form-horizontal form-label-left'>

    <div class='x_content'>
        
        <div <?PHP //echo $phidden_ekse; ?> class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_panel'>
                
                <div hidden class='col-sm-3'>
                    <div>Penerima</div>
                    <div class="form-group">
                        <div class='input-group date' id=''>
                            <div class='input-group '>
                            <span class='input-group-btn'>
                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBRPenerima('e_idpenerima', 'e_nmpenerima', 'e_almtpenerima')">Pilih!</button>
                            </span>
                            <input type='hidden' class='form-control' id='e_wewenangdiv' name='e_wewenangdiv' value='<?PHP echo $pwewenang; ?>' Readonly>
                            <input type='hidden' class='form-control' id='e_idpenerima' name='e_idpenerima' value='<?PHP echo ""; ?>' Readonly>
                            <input type='text' class='form-control' id='e_nmpenerima' name='e_nmpenerima' value='<?PHP echo ""; ?>' Readonly>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div hidden class='col-sm-3'>
                    <div>Alamat</div>
                    <div class="form-group">
                        <input type='text' class='form-control' id='e_almtpenerima' name='e_almtpenerima' value='<?PHP echo ""; ?>' Readonly>
                    </div>
               </div>


                <div class='col-sm-3'>
                    <div>Centang data di bawah</div>
                    <div class="form-group">
                        <?PHP
                        if ($pkeypilih=="2") {
                            echo "<input type='button' class='btn btn-warning btn-sm' id='s-submit' value='Print Surat Tugas' onclick=\"BuatSuratTugas('surattugas', 'surattugas')\">";
                        }else{
                            echo "<input type='button' class='btn btn-success btn-sm' id='s-submit' value='Simpan Surat Jalan' onclick=\"disp_confirm('suratjalan', 'chkbox_br[]')\">";
                        }
                        ?>
                        
                    </div>
                </div>


            </div>
        </div>
        
        
        <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
            <thead>
                <tr>
                    <th width='7px'>No</th>
                    <th width='7px'><input type="checkbox" id="chkbtnbr" value="select" 
                        onClick="SelAllCheckBox('chkbtnbr', 'chkbox_br[]')" /></th>
                    <th width='5px'>ID</th>
                    <th width='50px'>Tanggal</th>
                    <th width='50px'>Grp. Produk</th>
                    <th width='50px'>Cabang</th>
					<th width='50px'>Area</th>
                    <th width='50px'>Dikirim Kpd.</th>
                    <th width='20px'>No. Resi</th>
                    <th width='20px'>Tgl. Terima</th>
                    <th width='20px'>Yg Menerima</th>
                </tr>
            </thead>
            <tbody>
                <?PHP
                $no=1;
                
                $query = "select DISTINCT IFNULL(GRPPRINT,0) as GRPPRINT from $tmp01 order by GRPPRINT";
                $tampilg= mysqli_query($cnmy, $query);
                while ($rgp= mysqli_fetch_array($tampilg)) {
                    $pgrppil=$rgp['GRPPRINT'];
                    
                    $psudahlewatprint=false;
                    $query = "select DISTINCT IFNULL(GRPPRINT,0) as GRPPRINT, IFNULL(IGROUP,0) as IGROUP from $tmp01 WHERE IFNULL(GRPPRINT,0)='$pgrppil' order by GRPPRINT, IGROUP";
                    $tampilig= mysqli_query($cnmy, $query);
                    while ($igp= mysqli_fetch_array($tampilig)) {
                        $pigprpil=$igp['IGROUP'];
                        
                        $psudahlewatresi=false;
                        $query = "select * from $tmp01 WHERE IFNULL(GRPPRINT,0)='$pgrppil' AND IFNULL(IGROUP,'0')='$pigprpil' order by GRPPRINT, IGROUP, IDKELUAR";
                        $tampil1= mysqli_query($cnmy, $query);
                        while ($row1= mysqli_fetch_array($tampil1)) {
                            $pidkeluar=$row1['IDKELUAR'];
                            $ptgl=$row1['TANGGAL'];
                            $ptglkirim=$row1['TGLKIRIM'];
                            $ppmtgl=$row1['PM_TGL'];
                            $ppurchtgl=$row1['APV1_TGL'];
                            $pnoresi=$row1['NORESI'];
                            $pketkirim=$row1['KETKIRIM'];
                            $ptgltrima=$row1['TGLTERIMA'];
                            $pkryterima=$row1['NAMA_KARYAWANTERIMA'];
                            $ppilihanid=$row1['PILIHAN'];
                            $pdivisinm=$row1['DIVISINM'];
                            $pnmkaryawan=$row1['NAMA_KARYAWAN'];

                            $pgrpprint=$row1['GRPPRINT'];
                            $psudhprint=$row1['PRINT'];

                            $ppnrigroup=$row1['IGROUP'];
                            $ppnridpenerima=$row1['IDPENERIMA'];
                            $pnmppnr=$row1['NAMA_PENERIMA'];
                            $palamat1ppnr=$row1['ALAMAT1'];
                            $palamat2ppnr=$row1['ALAMAT2'];
                            $pkotappnr=$row1['KOTA'];
                            $pprovinsippnr=$row1['PROVINSI'];
                            $pakdposppnr=$row1['KODEPOS'];
                            $phpppnr=$row1['HP'];


                            $pnmcabang=$row1['NAMA_CABANGETH'];
							$pnmarea=$row1['NAMAAREAETH'];;
                            if ($ppilihanid=="OT") {
								$pnmcabang=$row1['NAMA_CABANGOTC'];
								$pnmarea=$row1['NAMAAREAOTC'];;
							}

							$pidcabpl=$row1['ICABANGID_O'];
							if (empty($pnmcabang)) {
								$pnmcabang=$pidcabpl;
								if ($pnmcabang=="JKT_RETAIL") $pnmcabang="JAKARTA RETAIL";
								if ($pnmcabang=="JKT_MT") $pnmcabang="JAKARTA - MODERN TRADE";
							}
					
                            $pdikirimkpd="";

                            $pdikirimkpd="$pnmppnr ($palamat1ppnr - $pkotappnr)";

                            $ptgl= date("d/m/Y", strtotime($ptgl));

                            if ($ptglkirim=="0000-00-00" OR $ptglkirim=="0000-00-00 00:00:00") $ptglkirim="";
                            if ($ppmtgl=="0000-00-00" OR $ppmtgl=="0000-00-00 00:00:00") $ppmtgl="";
                            if ($ppurchtgl=="0000-00-00" OR $ppurchtgl=="0000-00-00 00:00:00") $ppurchtgl="";
                            if ($ptgltrima=="0000-00-00" OR $ptgltrima=="0000-00-00 00:00:00") $ptgltrima="";

                            if (!empty($ptgltrima)) $ptgltrima= date("d/m/Y", strtotime($ptgltrima));

                            $print="<a title='Detail Barang / Print' href='#' class='btn btn-dark btn-xs' data-toggle='modal' "
                                . "onClick=\"window.open('eksekusi3.php?module=gimickeluarbarang&nid=$pidkeluar&iprint=print',"
                                . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                . "&nbsp; $pidkeluar &nbsp;</a>";

                            $pbtnsjb="";
                            $pbtnhapussjb="";
                            $pbtntandaisjb="";
                            $pbtnisinoresi="";

                            if (!empty($pgrpprint) AND !empty($ppnridpenerima)) {

                                $pbtnwarnaprint="btn btn-success btn-xs";
                                if ($psudhprint=="Y") {
                                    $pbtnwarnaprint="btn btn-info btn-xs";
                                }

                                $pbtnsjb="<a title='Print Surat Jalan Barang Gimmick' href='#' class='$pbtnwarnaprint' data-toggle='modal' "
                                    . "onClick=\"window.open('eksekusi3.php?module=gimicprintskb&idmenu=307&act=sjb&igx=$pgrpprint&ip=$ppnridpenerima',"
                                    . "'Ratting','width=700,height=500,left=500,top=100,scrollbars=yes,toolbar=yes,status=1,pagescrool=yes')\"> "
                                    . "Print SJB ($pgrpprint)</a>";
                                $pbtnhapussjb = "<input type='button' value='Hapus' class='btn btn-danger btn-xs' onClick=\"ProsesDataHapusSJB('hapussjb', '$pidkeluar', '$ppnrigroup', '$ppnridpenerima')\">";

                                if ($psudhprint=="Y" AND $pidusergroup!="1") {
                                    $pbtnhapussjb="";
                                }

                                if ($psudhprint=="N") {
                                    $pbtntandaisjb = "<span hidden><input type='button' value='Tandai Sdh Pernah Print' class='btn btn-warning btn-xs' onClick=\"ProsesDataHapusSJB('tandaisudahprint', '$pidkeluar', '$ppnrigroup', '$ppnridpenerima')\"></span>";
                                }

                                $pbtnisinoresi="<button type='button' class='btn btn-default btn-xs' data-toggle='modal' data-target='#myModal' onClick=\"TambahDataInputResi('$ppnrigroup', '$pgrpprint', '$pidkeluar')\">Isi NoResi</button>";

                            }


                            $pbtnwarnaresi="btn btn-warning btn-xs";
                            if (!empty($ptgltrima)) $pbtnwarnaresi="btn btn-info btn-xs";
                            $pbtnisiterima = "<a class='$pbtnwarnaresi' href='?module=$pmodule&act=isiterima&idmenu=$pidmenu&nmun=$pidmenu&id=$pidkeluar'>Isi Terima</a>";

                            $ceklisnya = "<input type='checkbox' value='$pidkeluar' name='chkbox_br[]' id='chkbox_br[$pidkeluar]' class='cekbr'>";
                            if (empty($ppmtgl)) $ceklisnya="";
                            //if (empty($ppurchtgl)) $ceklisnya="";
                            if (empty($ppnridpenerima) OR empty($pnmppnr) OR empty($palamat1ppnr)) {
                                $ceklisnya="";
                                $pdikirimkpd="<span style='color:red;'>Kepada / Alamat Kosong</span>";
                            }


                            $ptxtigrouptrm="<input type='hidden' id='txt_igroup[$pidkeluar]' name='txt_igroup[$pidkeluar]' value='$ppnrigroup' Readonly>";
                            $ptxtidtrm="<input type='hidden' id='txt_idtrm[$pidkeluar]' name='txt_idtrm[$pidkeluar]' value='$ppnridpenerima' Readonly>";
                            $ptxtnmtrm="<input type='hidden' id='txt_nmtrm[$pidkeluar]' name='txt_nmtrm[$pidkeluar]' value='$pnmppnr' Readonly>";
                            $ptxtalmt1trm="<input type='hidden' id='txt_almt1trm[$pidkeluar]' name='txt_almt1trm[$pidkeluar]' value='$palamat1ppnr' Readonly>";
                            $ptxtalmt2trm="<input type='hidden' id='txt_almt2trm[$pidkeluar]' name='txt_almt2trm[$pidkeluar]' value='$palamat2ppnr' Readonly>";
                            $ptxtkotatrm="<input type='hidden' id='txt_kotatrm[$pidkeluar]' name='txt_kotatrm[$pidkeluar]' value='$pkotappnr' Readonly>";
                            $ptxtprovtrm="<input type='hidden' id='txt_provtrm[$pidkeluar]' name='txt_provtrm[$pidkeluar]' value='$pprovinsippnr' Readonly>";
                            $ptxtkdpostrm="<input type='hidden' id='txt_kdpostrm[$pidkeluar]' name='txt_kdpostrm[$pidkeluar]' value='$pakdposppnr' Readonly>";
                            $ptxthptrm="<input type='hidden' id='txt_hptrm[$pidkeluar]' name='txt_hptrm[$pidkeluar]' value='$phpppnr' Readonly>";
                            
                            if ($psudahlewatresi==true) {
                                $pbtnisinoresi="";
                            }
                            
                            if ($psudahlewatprint==true) {
                                $pbtnsjb="";
                            }
                            
                            
                            $psudahlewatresi=true;
                            $psudahlewatprint=true;
                            
                            if (!empty($pnoresi)) {
                                if (!empty($pketkirim)) $pnoresi .=" ($pketkirim)";
                                $pbtnhapussjb="";
                            }
                            
                            echo "<tr>";
                            echo "<td nowrap>$no</td>";
                            echo "<td >$ceklisnya $ptxtigrouptrm $ptxtidtrm $ptxtnmtrm $ptxtalmt1trm $ptxtalmt2trm $ptxtkotatrm $ptxtprovtrm $ptxtkdpostrm $ptxthptrm</td>";
                            echo "<td nowrap>$print $pbtnhapussjb <br/>$pbtnisinoresi"
                                    . "$pbtntandaisjb $pbtnsjb</td>";//
                            echo "<td nowrap>$ptgl</td>";
                            echo "<td nowrap>$pdivisinm</td>";
                            echo "<td nowrap>$pnmcabang</td>";
							echo "<td nowrap>$pnmarea</td>";
                            echo "<td>$pdikirimkpd</td>";
                            echo "<td >$pnoresi</td>";
                            echo "<td nowrap>$ptgltrima</td>";
                            echo "<td nowrap>$pkryterima</td>";
                            echo "</tr>";



                            $no++;
                        }
                        
                        
                        
                    }//i grp
                
                }//grp print
                ?>
            </tbody>
                
        </table>
        
    </div>
    
    
</form>


<style>
    .divnone {
        display: none;
    }
    #datatablestockopn th {
        font-size: 13px;
    }
    #datatablestockopn td { 
        font-size: 11px;
    }
</style>

<style>

table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    background-color:#FFFFFF;
}

th {
    background: white;
    position: sticky;
    top: 0;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    z-index:1;
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<?PHP
hapusdata:
    mysqli_query($cnmy, "drop TEMPORARY table $tmp01");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp02");
    mysqli_query($cnmy, "drop TEMPORARY table $tmp03");
    
    mysqli_close($cnmy);
?>