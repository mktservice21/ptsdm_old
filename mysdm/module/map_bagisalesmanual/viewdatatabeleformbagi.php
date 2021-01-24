<?php
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
    session_start();
    
    
    $puserid="";
    if (isset($_SESSION['USERID'])) $puserid=$_SESSION['USERID'];

    if (empty($puserid)) {
        echo "ANDA HARUS LOGIN ULANG...";
        exit;
    }
    
    $fkaryawan=$_SESSION['IDCARD'];
    $fjbtid=$_SESSION['JABATANID'];
    
    
    $pidmodule=$_GET['module'];
    $pidmenu=$_GET['idmenu'];
    
    $pnmtabelsales=$_POST['unmsales'];
    $pecustid=$_POST['uecustid'];
    $pnmecust=$_POST['unmecust'];
    $pcabidmap=$_POST['ucabmap'];
    $pareaidmap=$_POST['uareamap'];
    $picustid=$_POST['uicustmap'];
    $picustnm=$_POST['uicustnmmap'];
    
    $piddist=$_POST['udistid'];
    $pidecab=$_POST['ucabid'];
    $pnmfilter=$_POST['unamafilter'];
    $pqtyfaktur=$_POST['uqtyfaktur'];
    $psdhsplitqty=$_POST['uqtysplit'];
    $pidbrg=$_POST['ubrg'];
    $pidproduk=$_POST['uproduk'];
    
    if (empty($psdhsplitqty)) $psdhsplitqty=0;
    $pqtysplit="";
    
    $pqtysisa=$_POST['uqtysisa'];
    $ptgljual=$_POST['utgljual'];
    $pbln=$_POST['ubln'];
    if (strlen($pbln)==7) $pbln=$pbln."-01";
    
    $pbulan = date('Y-m', strtotime($pbln));
    
    if (empty($ptgljual)) $ptgljual=$pbln;
    
    include "../../config/koneksimysqli_ms.php";
    
    $pidcabang="";
    $pidarea="";
    
    $aksi="map_bagisalesmanual/aksi_bagisalesmanual.php";
    
    $query = "select * from MKT.eproduk WHERE eprodid='$pidbrg' AND distid='$piddist'";
    $tampil= mysqli_query($cnms, $query);
    $row=mysqli_fetch_array($tampil);
    $pnamaproduk=TRIM($row['nama']);
?>

<script src="js/inputmask.js"></script>
<form method='POST' action='<?PHP echo "$aksi?module=$pidmodule&act=input&idmenu=$pidmenu"; ?>' 
      id='form_data_ex' name='formex' data-parsley-validate class='form-horizontal form-label-left'  enctype='multipart/form-data'>
            
    <div class='x_panel'>
        <div class='x_content'>

        <table>
            <tr><td nowrap colspan="3" style="font-weight:bold;">Pembagian Sales Manual : </td></tr>
        </table>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                
                <div hidden class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_nmtblsls' name='e_nmtblsls' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmtabelsales; ?>' Readonly>
                        
                        <input type='text' id='e_ecustid' name='e_ecustid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pecustid; ?>' Readonly>
                        <input type='text' id='e_cabmapid' name='e_cabmapid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pcabidmap; ?>' Readonly>
                        <input type='text' id='e_areamapid' name='e_areamapid' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pareaidmap; ?>' Readonly>
                        <input type='text' id='e_icustidmap' name='e_icustidmap' class='form-control col-md-7 col-xs-12' value='<?PHP echo $picustid; ?>' Readonly>
                        <input type='text' id='e_nmcustmap' name='e_nmcustmap' class='form-control col-md-7 col-xs-12' value='<?PHP echo $picustnm; ?>' Readonly>
                        
                        <input type='text' id='e_distidpil' name='e_distidpil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $piddist; ?>' Readonly>
                        <input type='text' id='e_idecabpil' name='e_idecabpil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidecab; ?>' Readonly>
                        <input type='text' id='e_idbrg' name='e_idbrg' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbrg; ?>' Readonly>
                        <input type='text' id='e_idprod' name='e_idprod' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidproduk; ?>' Readonly>
                        <input type='text' id='e_blnpil' name='e_blnpil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pbln; ?>' Readonly>
                        <input type='text' id='e_tgljualpil' name='e_tgljualpil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptgljual; ?>' Readonly>
                        <input type='text' id='e_qtysisapil' name='e_qtysisapil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pqtysisa; ?>' Readonly>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Faktur <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_fakturidpil' name='e_fakturidpil' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmfilter; ?>' Readonly>
                    </div>
                </div>
                
                <div  class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>E Customer <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_nmecust' name='e_nmecust' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnmecust; ?>' Readonly>
                    </div>
                </div>
                
                <div  class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Produk <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_nmproduk' name='e_nmproduk' class='form-control col-md-7 col-xs-12' value='<?PHP echo "$pnamaproduk ($pidbrg)"; ?>' Readonly>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang SDM <span class='required'></span></label>
                    <div class='col-xs-4'>
                        <select class='soflow' name='cb_cabangid' id='cb_cabangid' onchange="ShowDataCabangArea()">
                            <?php
                            echo "<option value='' selected>--Pilih--</option>";
                            
                                if ($fjbtid=="38") {
                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                            . " JOIN hrd.rsm_auth as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                    $query .=" order by a.nama";
                                }elseif ($fjbtid=="10" OR $fjbtid=="18") {
                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                            . " JOIN MKT.ispv0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                    $query .=" order by a.nama";
                                }elseif ($fjbtid=="15") {
                                    $query = "select DISTINCT a.icabangid as icabangid, a.nama as nama from MKT.icabang as a "
                                            . " JOIN MKT.imr0 as b on a.icabangid=b.icabangid WHERE b.karyawanid='$pidcard' ";
                                    $query .=" order by a.nama";
                                }else{
                                    $query = "select icabangid as icabangid, nama as nama from MKT.icabang WHERE 1=1 ";
                                    $query .=" AND LEFT(nama,5) NOT IN ('OTC -', 'PEA -') ";
                                    $query .=" AND IFNULL(aktif,'')<>'N' ";
                                    $query .=" order by nama";
                                }
                            
                            $tampiledu= mysqli_query($cnms, $query);
                            while ($du= mysqli_fetch_array($tampiledu)) {
                                $nidcab=$du['icabangid'];
                                $nnmcab=$du['nama'];

                                if ($nidcab==$pcabidmap) 
                                    echo "<option value='$nidcab' selected>$nnmcab ($nidcab)</option>";
                                else
                                    echo "<option value='$nidcab'>$nnmcab ($nidcab)</option>";

                            }
                            ?>
                        </select>
                    </div>
                </div>    
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area SDM <span class='required'></span></label>
                    <div class='col-xs-4'>
                        <select class='soflow' name='cb_areaid' id='cb_areaid' onchange="ShowDataCustomer()">
                            <?php
                            echo "<option value='' selected>--Pilih--</option>";
                            
                                $query = "select icabangid as icabangid, areaid as areaid, nama as nama from MKT.iarea WHERE icabangid='$pcabidmap' ";
                                $query .=" AND IFNULL(aktif,'')<>'N' ";
                                $query .=" order by nama";
                            

                            //if (!empty($pcabidmap)) {
                                $tampiledu= mysqli_query($cnms, $query);
                                while ($du= mysqli_fetch_array($tampiledu)) {
                                    $nidarea=$du['areaid'];
                                    $nnmarea=$du['nama'];

                                    if ($nidarea==$pareaidmap) 
                                        echo "<option value='$nidarea' selected>$nnmarea ($nidarea)</option>";
                                    else
                                        echo "<option value='$nidarea'>$nnmarea ($nidarea)</option>";

                                }
                            //}
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Customer SDM <span class='required'></span></label>
                    <div class='col-xs-4'>
                        <select class='soflow' name='cb_custid' id='cb_custid' onchange="">
                            <?php
                            echo "<option value='' selected>--Pilih--</option>";
                            $query = "select icustid, nama from MKT.icust WHERE ( (IFNULL(aktif,'')<>'N' AND icabangid='$pcabidmap' and areaid='$pareaidmap' AND IFNULL(nama,'')<>'') OR icustid='$picustid' )order by nama";
                            $tampila= mysqli_query($cnms, $query);
                            $ketemua= mysqli_num_rows($tampila);
                            if ((INT)$ketemua==0) echo "<option value='' selected>--Pilih--</option>";
                            while ($arow= mysqli_fetch_array($tampila)) {
                                $nidcust=$arow['icustid'];
                                $nnmcust=$arow['nama'];
                                if ($nidcust==$nidcust)
                                    echo "<option value='$nidcust' selected>$nnmcust ($nidcust)</option>";
                                else
                                    echo "<option value='$nidcust'>$nnmcust ($nidcust)</option>";

                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                
                <div  class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Qty Faktur <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_qtyfakturpil' name='e_qtyfakturpil' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pqtyfaktur; ?>' Readonly>
                    </div>
                </div>
                
                <div  class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Qty Sudah Splitted <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' id='e_sdhsplitqty' name='e_sdhsplitqty' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $psdhsplitqty; ?>' Readonly>
                    </div>
                </div>
                
                <div  class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>*) Qty Splitted <span class='required'></span></label>
                    <div class='col-md-4'>
                        <input type='text' onblur="SesuaikanQtySplit()" id='e_qtysplit' name='e_qtysplit' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pqtysplit; ?>' >
                    </div>
                </div>
                
                <div class='form-group'>
                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                    <div class='col-md-4'>
                        <button type='button' class='btn btn-success' onclick='disp_confirmsimpandata()'>Save</button>
                    </div>
                </div>
                
            </div>
            
            
        </div>
    </div>

</form>



<script>
    function ShowDataCabangArea() {
        var idcab=document.getElementById('cb_cabangid').value;
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatabagi.php?module=viewdataareacabang",
            data:"udcab="+idcab,
            success:function(data){
                $("#cb_areaid").html(data);
                ShowDataCustomer()
            }
        });
    }
    
    function ShowDataCustomer() {
        var idcab=document.getElementById('cb_cabangid').value;
        var idarea=document.getElementById('cb_areaid').value;
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/viewdatabagi.php?module=viewdatacustomer",
            data:"udcab="+idcab+"&udarea="+idarea,
            success:function(data){
                $("#cb_custid").html(data);
            }
        });
    }
    
    function SesuaikanQtySplit() {
        var eqtysisa=document.getElementById('e_qtysisapil').value;
        var eqtyfak=document.getElementById('e_qtyfakturpil').value;
        var eqtysdhsplt=document.getElementById('e_sdhsplitqty').value;
        var eqtysplt=document.getElementById('e_qtysplit').value;
        
        var newchar = '';
        
        if (eqtyfak=="") eqtyfak="0";
        eqtyfak = eqtyfak.split(',').join(newchar);
        
        if (eqtysisa=="") eqtysisa="0";
        eqtysisa = eqtysisa.split(',').join(newchar);
        
        if (eqtysdhsplt=="") eqtysdhsplt="0";
        eqtysdhsplt = eqtysdhsplt.split(',').join(newchar);
        
        if (eqtysplt=="") eqtysplt="0";
        eqtysplt = eqtysplt.split(',').join(newchar);
        
        if (parseFloat(eqtysplt)>parseFloat(eqtysisa)) {
            document.getElementById('e_qtysplit').value=eqtysisa;
        }
        
        
    }
    
    function disp_confirmsimpandata() {
        var edistnmtblsales=document.getElementById('e_nmtblsls').value;//faktur id
        var enamafilter=document.getElementById('e_fakturidpil').value;//faktur id
        var edistid=document.getElementById('e_distidpil').value;
        var eecab=document.getElementById('e_idecabpil').value;
        var ebrgid=document.getElementById('e_idbrg').value;
        var eidprod=document.getElementById('e_idprod').value;
        var ebln=document.getElementById('e_blnpil').value;
        var etgljual=document.getElementById('e_tgljualpil').value;
        var eqtysisa=document.getElementById('e_qtysisapil').value;
        var eqtyfak=document.getElementById('e_qtyfakturpil').value;
        var eqtysdhsplt=document.getElementById('e_sdhsplitqty').value;
        var eqtysplt=document.getElementById('e_qtysplit').value;
        var eidcabang=document.getElementById('cb_cabangid').value;
        var eidarea=document.getElementById('cb_areaid').value;
        var eidcust=document.getElementById('cb_custid').value;
        
        if (edistnmtblsales=="") {
            alert("Nama Tabel Sales Tidak ada...");
            return false;
        }
        
        if (edistid=="") {
            alert("distributor kosong...");
            return false;
        }
        
        if (eecab=="") {
            alert("ecabang kosong...");
            return false;
        }
        
        if (ebrgid=="" || eidprod=="") {
            alert("produk kosong...");
            return false;
        }
        
        if (ebln=="" || etgljual=="") {
            alert("tanggal kosong...");
            return false;
        }
        
        if (eidcabang=="") {
            alert("Cabang SDM belum diisi...");
            return false;
        }
        
        if (eidarea=="") {
            alert("Area SDM belum diisi...");
            return false;
        }
        
        if (eidcust=="") {
            //alert("Customer SDM belum diisi...");
            //return false;
        }
        
        if (eqtyfak=="" || eqtyfak=="0") {
            alert("QTY Faktur Kosong...");
            return false;
        }
        
        if (eqtysisa=="" || eqtysisa=="0") {
            alert("QTY Sisa Kosong...");
            return false;
        }
        
        if (eqtysplt=="" || eqtysplt=="0") {
            alert("QTY Splitted belum diisi...");
            document.getElementById('e_qtysplit').focus();
            return false;
        }
        
        var newchar = '';
        
        if (eqtyfak=="") eqtyfak="0";
        eqtyfak = eqtyfak.split(',').join(newchar);
        
        if (eqtysisa=="") eqtysisa="0";
        eqtysisa = eqtysisa.split(',').join(newchar);
        
        if (eqtysdhsplt=="") eqtysdhsplt="0";
        eqtysdhsplt = eqtysdhsplt.split(',').join(newchar);
        
        if (eqtysplt=="") eqtysplt="0";
        eqtysplt = eqtysplt.split(',').join(newchar);
        
        if (parseFloat(eqtysplt)>parseFloat(eqtysisa)) {
            eqtysplt=eqtysisa;
        }
        
        var cmt = confirm('pastikan data yang terisi sudah sesuai....!!!\n\
Jika sudah klik OK');
        if (cmt == false) {
            return false;
        }
        
        var myurl = window.location;
        var urlku = new URL(myurl);
        var module = urlku.searchParams.get("module");
        var idmenu = urlku.searchParams.get("idmenu");
        var act = urlku.searchParams.get("act");
        
        $.ajax({
            type:"post",
            url:"module/map_bagisalesmanual/simpandatasplit.php?module="+module+"&act=datasimpansplit",
            data:"udistid="+edistid+"&uecab="+eecab+"&ufakturid="+enamafilter+"&ubrgid="+ebrgid+"&uidprod="+eidprod+
                    "&ubln="+ebln+"&utgljual="+etgljual+"&uqtysisa="+eqtysisa+"&uqtyfak="+eqtyfak+"&uqtysdhsplt="+eqtysdhsplt+
                    "&uqtysplt="+eqtysplt+"&uidcabang="+eidcabang+"&uidarea="+eidarea+"&uidcust="+eidcust+"&udistnmtblsales="+edistnmtblsales,
            success:function(data){
                var istatus=data.trim();
                if (istatus=="berhasil") {
                    disp_datamapingbyfaktur("2", enamafilter);
                }else{
                    alert(data);
                }
                
            }
        });
    }
</script>
<?PHP
hapusdata:
    
    mysqli_close($cnms);
?>