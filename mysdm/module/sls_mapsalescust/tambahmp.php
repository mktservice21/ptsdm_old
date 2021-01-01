<?php
include "config/koneksimysqli_ms.php";

$pfakturid="";
$pcabid="";
$pareaid="";
$pcustid="";
$hari_ini = date("Y-m-d");
$tgl1 = date('Y-m-01', strtotime($hari_ini));
$tgl2 = date('Y-m-t', strtotime($hari_ini));

$pidcardpl=$_SESSION['IDCARD'];


$pidcabang="";
$pnamacabang="";
$pidarea="";
$pnamaarea="";
$pcustnama="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pact=$_GET['act'];
$act="input";
if ($pact=="editdata"){
    $act="update";
    
    $pfakturid=$_GET['ixn'];
    $pcabid=$_GET['cbb'];
    $pareaid=$_GET['iax'];
    $pcustid=$_GET['ics'];
    $tgl1=$_GET['yd1'];
    $tgl2=$_GET['yd2'];

    $query = "SELECT
        DISTINCT 
        s.icabangid as icabangid,
        c.nama as namacabang,
        s.areaid as areaid,
        ar.nama as namaarea,
        s.fakturid as fakturid,
        s.icustid as icustid, 
        ic.nama AS namacustomer
        FROM
        sls.mr_sales2 s
        LEFT JOIN sls.icust ic
        ON s.icabangid = ic.iCabangId
        AND s.areaid = ic.areaId
        AND s.icustid = ic.iCustId
        LEFT JOIN sls.iproduk ip
        ON s.iprodid = ip.iprodid
        LEFT JOIN sls.icabang c
        ON s.icabangid = c.icabangid
        LEFT JOIN sls.iarea ar
        ON s.icabangid = ar.icabangid
        AND s.areaid = ar.areaid
        WHERE s.tgljual BETWEEN '$tgl1' AND '$tgl2'
        AND s.icabangid NOT IN (30,31) 
        AND s.fakturid='$pfakturid' AND s.icabangid='$pcabid' AND s.areaid='$pareaid' AND s.icustid='$pcustid' 
        HAVING IFNULL(namacustomer,'')=''";
    
    $edit = mysqli_query($cnms, $query);
    $r    = mysqli_fetch_array($edit);
    
    $pidcabang=$r['icabangid'];
    $pnamacabang=$r['namacabang'];
    $pidarea=$r['areaid'];
    $pnamaarea=$r['namaarea'];
    $pcustnama=$r['namacustomer'];
}

?>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<div class="">
    
    <!--row-->
    <div class="row">
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='target-form2' name='form1' data-parsley-validate class='form-horizontal form-label-left'>
        
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                    
                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <h2>
                            <a class='btn btn-default' href="<?PHP echo "?module=$pmodule&idmenu=$pidmenu&act=$pidmenu"; ?>">Back</a>
                        </h2>
                        <div class='clearfix'></div>
                    </div>
                    
                    
                    <div class='x_panel'>
                        <div class='x_content'>
                            <div class='col-md-12 col-sm-12 col-xs-12'>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Faktur ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pfakturid; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                        <input type='hidden' id='e_tgl01' name='e_tgl01' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl1; ?>' Readonly>
                                        <input type='hidden' id='e_tgl02' name='e_tgl02' class='form-control col-md-7 col-xs-12' value='<?PHP echo $tgl2; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Cabang <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nmcabang' name='e_nmcabang' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamacabang; ?>' Readonly>
                                        <input type='hidden' id='e_idcabang' name='e_idcabang' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcabang; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Area <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_nmarea' name='e_nmarea' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pnamaarea; ?>' Readonly>
                                        <input type='hidden' id='e_idarea' name='e_idarea' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidarea; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Customer <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataCustomer('e_idcust', 'e_nmcust')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idcust' name='e_idcust' value='<?PHP echo $pcustid; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_idcustlama' name='e_idcustlama' value='<?PHP echo $pcustid; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Customer <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_nmcust' name='e_nmcust' class='form-control col-md-7 col-xs-12' onblur='' value='<?PHP echo $pcustnama; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    
                </div>
            </div>
            
            <div id='loading3'></div>
            <div id="s_div">
                
                <div class='x_content'>
                    <table id='datatablestockopn' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='5px' nowrap>No</th>
                                <th width='10px' nowrap>Initial</th>
                                <th width='10px' nowrap>Divisi</th>
                                <th width='10px' nowrap>Id Produk</th>
                                <th width='50px' nowrap>Nama Produk</th>
                                <th width='20px' nowrap>Qty</th>
                                <th width='20px' nowrap>Hna</th>
                                <th width='30px' nowrap>Total</th>
                                
                            </tr>
                        </thead>
                        <tbody class='inputdata'>
                            <?PHP
                            $no=1;
                            $query = "select s.initial as initial, s.divprodid as divprodid, s.iprodid as iprodid, 
                                p.nama as nama_produk, s.qty, s.hna from sls.mr_sales2 s left join sls.iproduk as p on s.iprodid=p.iprodid 
                                WHERE s.tgljual BETWEEN '$tgl1' AND '$tgl2'
                                AND s.icabangid NOT IN (30,31) 
                                AND s.fakturid='$pfakturid' AND s.icabangid='$pcabid' AND s.areaid='$pareaid' AND s.icustid='$pcustid' ";
                            $tampild=mysqli_query($cnms, $query);
                            while ($nrd= mysqli_fetch_array($tampild)) {
                                $pinitial=$nrd['initial'];
                                $pdivprodid=$nrd['divprodid'];
                                $pidproduk=$nrd['iprodid'];
                                $pnmproduk=$nrd['nama_produk'];
                                $pqty=$nrd['qty'];
                                $phna=$nrd['hna'];
                                
                                $ptotal=(DOUBLE)$pqty*(DOUBLE)$phna;
                                
                                $pqty=number_format($pqty,0,",",",");
                                $phna=number_format($phna,0,",",",");
                                $ptotal=number_format($ptotal,0,",",",");
                                
                                echo "<tr>";
                                echo "<td nowrap>$no</td>";
                                echo "<td nowrap>$pinitial</td>";
                                echo "<td nowrap>$pdivprodid</td>";
                                echo "<td nowrap>$pidproduk</td>";
                                echo "<td nowrap>$pnmproduk</td>";
                                echo "<td nowrap align='right'>$pqty</td>";
                                echo "<td nowrap align='right'>$phna</td>";
                                echo "<td nowrap align='right'>$ptotal</td>";
                                echo "</tr>";
                                
                                $no++;
                                
                            }
                            ?>
                        </tbody>
                    </table>
                    
                </div>
                
            </div>
            
        </form>
        
    </div>
    
</div>

<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<style>
    .form-group, .input-group, .control-label {
        margin-bottom:3px;
    }
    .control-label {
        font-size:12px;
    }
    input[type=text] {
        box-sizing: border-box;
        color:#000;
        font-size:12px;
        height: 30px;
    }
    select.soflow {
        font-size:12px;
        height: 30px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
    .btn-primary {
        width:50px;
        height:30px;
        margin-right: 50px;
    }
    .disabledDiv {
        pointer-events: none;
        opacity: 0.4;
    }
</style>

<style>
    .ui-datepicker-calendar {
        display: none;
    }
</style>

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
}

.th2 {
    background: white;
    position: sticky;
    top: 23;
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    border-top: 1px solid #000;
}
</style>

<script>
    
    function getDataCustomer(data1, data2){
        var eidcab =document.getElementById('e_idcabang').value;
        var eidarea =document.getElementById('e_idarea').value;
        
        $.ajax({
            type:"post",
            url:"module/sls_mapsalescust/viewdatacustomermp.php?module=viewdatacustomer",
            data:"uidcab="+eidcab+"&uidarea="+eidarea+"&udata1="+data1+"&udata2="+data2,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalCustomer(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }
    
    
    
    function disp_confirm(pText_,ket)  {
        
        var icust =document.getElementById('e_idcust').value;
        var inmcust =document.getElementById('e_nmcust').value;
        
        if (icust=="") {
            alert("Customer harus dipilih");
            return false;
        }
        
        if (inmcust=="") {
            alert("Nama Customer Masih Kosong...");
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
                document.getElementById("target-form2").action = "module/sls_mapsalescust/aksi_mapsalescust.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("target-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
    }
</script>