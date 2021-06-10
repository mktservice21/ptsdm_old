<?PHP
$pidinput="";
$pidbr=$_GET['id'];
$pidbr_d=$_GET['xid'];

$pidgroup=$_SESSION['GROUP'];
$pidjbtpl=$_SESSION['JABATANID'];
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 

$_SESSION['PCHSSIVIDPR']=$pidbr;
$_SESSION['PCHSSIVIDPD']=$pidbr_d;



$query = "select b.idtipe, a.idpr, a.idpr_d, 
    a.idbarang, a.namabarang, 
    a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
    a.uraian, a.keterangan, 
    a.jumlah as jml, a.harga as rp_pr, a.satuan, b.idtipe, tgl_validate1  
    from dbpurchasing.t_pr_transaksi_d as a JOIN dbpurchasing.t_pr_transaksi as b on a.idpr=b.idpr WHERE 
    a.idpr_d='$pidbr_d' ";
$tampil= mysqli_query($cnmy, $query);
$nrw= mysqli_fetch_array($tampil);

$ptipepengajuan=$nrw['idtipe'];
$pidbrg=$nrw['idbarang'];
$pnmbrg=$nrw['namabarang'];
$pidbrg2=$nrw['idbarang_d'];
$pspesifikasi=$nrw['spesifikasi1'];
$psatuan=$nrw['satuan'];
$pketerangan="";//$nrw['keterangan'];
$ptipeminta=$nrw['idtipe'];
$ptglval1=$nrw['tgl_validate1'];

$pjumlah=$nrw['jml'];
$pharga=$nrw['rp_pr'];

if ($ptglval1=="0000-00-00" OR $ptglval1=="0000-00-00 00:00:00") $ptglval1="";

$pstsapvoleh="";
$pbolehsimpan=true;
if ($ptipeminta=="102") {
    if (empty($ptglval1)) {
        $pstsapvoleh="Belum Proses IT";
        $pbolehsimpan=false;
    }
}
                        
                        
$ppilih1="selected";
$ppilih2="";

$pudahpernah=false;
$query = "select a.idpr, a.idpr_d, 
    a.idbarang, a.namabarang, 
    a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
    a.uraian, a.keterangan, 
    a.jumlah as jml, a.harga as rp_pr, a.satuan 
    from dbpurchasing.t_pr_transaksi_po as a WHERE 
    a.idpr_d='$pidbr_d' AND IFNULL(aktif,'')='Y' ORDER BY a.idpr_d DESC LIMIT 1";
$tampiln= mysqli_query($cnmy, $query);
$pketemu= mysqli_num_rows($tampiln);
if ((DOUBLE)$pketemu>0) {
    $prw= mysqli_fetch_array($tampiln);
    $pudahpernah=true;
    
    $pidbrg=$prw['idbarang'];
    $pnmbrg=$prw['namabarang'];
    $pidbrg2=$prw['idbarang_d'];
    $pspesifikasi=$prw['spesifikasi1'];
    $psatuan=$prw['satuan'];
}


$pidvendor="";
$pnmvendor="";
$ptlpvendor="";


$_SESSION['PCHSSIVNMBG']=$pnmbrg;

$pact="";
if (isset($_GET['act'])) $pact=$_GET['act'];

$act="inputvendor";


if ($pact=="editisivendor") {
    $pidinput=$_GET['nid'];
    $act="updatevendor";
    
    $query = "select a.idpr, a.idpr_d, a.idpr_po, 
            a.kdsupp, b.NAMA_SUP as nama_sup, b.ALAMAT as alamat, b.TELP as telp, 
            a.idbarang, a.namabarang, 
            a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
            a.uraian, a.keterangan, 
            a.jumlah, a.harga, a.satuan, a.aktif, a.userid 
            from dbpurchasing.t_pr_transaksi_po as a 
            LEFT JOIN dbmaster.t_supplier as b on a.kdsupp=b.KDSUPP WHERE 
            a.idpr_po='$pidinput' order by a.aktif, b.NAMA_SUP";
    $tampil= mysqli_query($cnmy, $query);
    $nrw= mysqli_fetch_array($tampil);  
    
    $pidvendor=$nrw['kdsupp'];
    $pnmvendor=$nrw['nama_sup'];
    $ptlpvendor=$nrw['telp'];
    $pidbrg=$nrw['idbarang'];
    $pnmbrg=$nrw['namabarang'];
    $pidbrg2=$nrw['idbarang_d'];
    $pspesifikasi=$nrw['spesifikasi1'];
    $pketerangan=$nrw['keterangan'];
    $psatuan=$nrw['satuan'];
    $pstspil=$nrw['aktif'];
    
    $pjumlah=$nrw['jumlah'];
    $pharga=$nrw['harga'];
    
    if ($pstspil=="Y") {
        $ppilih1="selected";
        $ppilih2="";
    }else{
        $ppilih1="";
        $ppilih2="selected";
    }
    
    $query = "select * from dbpurchasing.t_pr_transaksi_po WHERE idpr_d='$pidbr_d' AND idpr_po<>'$pidinput' AND IFNULL(aktif,'')='Y' LIMIT 1";
    $tampiln= mysqli_query($cnmy, $query);
    $pketemu= mysqli_num_rows($tampiln);
    if ((DOUBLE)$pketemu==0) {
        $pudahpernah=false;
    }


}
?>


<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">
    
    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='demo-form2' name='form1' data-parsley-validate 
              class='form-horizontal form-label-left'>
            
            
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $pmodule; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $pidmenu; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
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
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidinput; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                        <input type='hidden' id='e_idtipepengajuan' name='e_idtipepengajuan' class='form-control col-md-7 col-xs-12' value='<?PHP echo $ptipepengajuan; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID PR <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_idpr' name='e_idpr' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID Detail <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_idpr_d' name='e_idpr_d' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr_d; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Vendor <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataVendor('e_idvendor', 'e_nmvendor', 'e_tlpvendor')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idvendor' name='e_idvendor' value='<?PHP echo $pidvendor; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Vendor <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_nmvendor' name='e_nmvendor' class='form-control col-md-7 col-xs-12'  value='<?PHP echo $pnmvendor; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Telp. <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_tlpvendor' name='e_tlpvendor' class='form-control col-md-7 col-xs-12'  value='<?PHP echo $ptlpvendor; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID Barang <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <?PHP
                                            if ($pudahpernah==true){
                                                
                                            }else{
                                            ?>
                                                <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBarang('e_idbrg', 'e_nmbrg')">Pilih!</button>
                                            <?PHP } ?>
                                        </span>
                                        <input type='text' class='form-control' id='e_idbrg' name='e_idbrg' value='<?PHP echo $pidbrg; ?>' Readonly>
                                        <input type='hidden' class='form-control' id='e_idbrg2' name='e_idbrg2' value='<?PHP echo $pidbrg; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Barang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nmbrg' name='e_nmbrg' class='form-control col-md-7 col-xs-12' oninput="this.value = this.value.toUpperCase()" maxlength="150" onblur='' value='<?PHP echo $pnmbrg; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID Barang Detail<span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_brg2' name='e_brg2' class='form-control col-md-7 col-xs-12'  value='<?PHP echo $pidbrg2; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesifikasi / Uraian <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea readonly class='form-control' id="e_spek_asli" name='e_spek_asli' maxlength="450"><?PHP echo $pspesifikasi; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesifikasi / Uraian <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea rows='4' class='form-control' id="e_spek" name='e_spek' maxlength="450"><?PHP echo $pspesifikasi; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmlqty' name='e_jmlqty' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $pjumlah; ?>" >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Satuan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_satuan' name='e_satuan' class='form-control col-md-7 col-xs-12'  value='<?PHP echo $psatuan; ?>' oninput="this.value = this.value.toUpperCase()">
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_hrgbrg' name='e_hrgbrg' class='form-control col-md-7 col-xs-12 inputmaskrp2' value="<?PHP echo $pharga; ?>" >
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea rows='4' class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300' ><?PHP echo $pketerangan; ?></textarea>
                                    </div>
                                </div>
                                
                                
                                <div  class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pilih <span class='required'></span></label>
                                    <div class='col-xs-2'>
                                        <select class='form-control input-sm' id='cb_pilih' name='cb_pilih' onchange="" data-live-search="true">
                                            <?PHP
                                                echo "<option value='Y' $ppilih1>Ya</option>";
                                                echo "<option value='N' $ppilih2>Tidak</option>";
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''> <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <div class="checkbox">
                                            
                                            <?PHP if ($pbolehsimpan==true) { ?>
                                                <?PHP if ($pact=="editisivendor") { ?>
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
                                                <?PHP }else{ ?>
                                                    <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Save</button>
                                                <?PHP } ?>
                                            <?PHP 
                                                }else{
                                                    echo "$pstsapvoleh";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
                
                
            </div>
            
            
        </form>
        
        
        <div class='col-md-12 col-sm-12 col-xs-12'>
            <div class='x_content'>
                <div class='x_panel'>
                    <b>Data yang terakhir diinput </b>
                    <table id='dttblisivendor' class='table table-striped table-bordered' width='100%'>
                        <thead>
                            <tr>
                                <th width='7px'>No</th>
                                <th width='20px'>
                                    
                                </th>
                                <th width='30px'>Vendor</th>
                                <th width='30px'>Nama Barang</th>
                                <th width='50px'>Spesifikasi</th>
                                <th width='50px'>Jumlah</th>
                                <th width='20px'>Satuan</th>
                                <th width='50px'>Harga</th>
                                <th width='50px'>Pilih</th>
                                <th width='50px'>Keterangan</th>
                            </tr>
                        </thead>
                        <body>
                        <?PHP
                        $no=1;
                        $query = "select a.idpr, a.idpr_d, a.idpr_po, 
                                a.kdsupp, b.NAMA_SUP as nama_sup, b.ALAMAT as alamat, b.TELP as telp, 
                                a.idbarang, a.namabarang, 
                                a.idbarang_d, a.spesifikasi1, a.spesifikasi2, 
                                a.uraian, a.keterangan, 
                                a.jumlah, a.harga, a.satuan, a.aktif, a.userid 
                                from dbpurchasing.t_pr_transaksi_po as a 
                                LEFT JOIN dbmaster.t_supplier as b on a.kdsupp=b.KDSUPP WHERE 
                                a.idpr_d='$pidbr_d' order by a.aktif, b.NAMA_SUP";
                        $tampil= mysqli_query($cnmy, $query);
                        while ($row= mysqli_fetch_array($tampil)) {
                            $pidprpo=$row['idpr_po'];
                            $pidpr=$row['idpr'];
                            $pidpr_d=$row['idpr_d'];
                            $pkdsup=$row['kdsupp'];
                            $pnmsup=$row['nama_sup'];
                            $palamatsup=$row['alamat'];
                            $ptlpsup=$row['telp'];
                            $psts=$row['aktif'];
                            $psatuan=$row['satuan'];

                            $pstsaktif="Ya";
                            if ($psts=="N") $pstsaktif="Tidak";

                            $pnmbarang=$row['namabarang'];
                            $pspesifikasi=$row['spesifikasi1'];
                            $pketerangan=$row['keterangan'];

                            $pjml=$row['jumlah'];
                            $pharga=$row['harga'];

                            $pjml=number_format($pjml,0,",",",");
                            $pharga=number_format($pharga,0,",",",");
                            
                            $pedit="<a class='btn btn-warning btn-xs' href='?module=$pmodule&act=editisivendor&idmenu=$pidmenu&nmun=$pidmenu&id=$pidpr&xid=$pidpr_d&nid=$pidprpo'>Edit</a>";
                            
                            echo "<tr>";

                            echo "<td nowrap>$no</td>";
                            echo "<td nowrap>$pedit</td>";
                            echo "<td nowrap>$pnmsup</td>";;
                            echo "<td nowrap>$pnmbarang</td>";
                            echo "<td >$pspesifikasi</td>";
                            echo "<td nowrap align='right'>$pjml</td>";
                            echo "<td nowrap>$psatuan</td>";
                            echo "<td nowrap align='right'>$pharga</td>";
                            echo "<td nowrap>$pstsaktif</td>";
                            echo "<td >$pketerangan</td>";

                            echo "</tr>";


                            $no++;
                        }
                        ?>
                        </body>
                    </table>

                </div>
            </div>
        </div>
        
        
        
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
    #dttblisivendor th {
        font-size: 13px;
    }
    #dttblisivendor td { 
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
    //getDataVendor('e_idvendor', 'e_nmvendor', 'e_tlpvendor')
    function getDataVendor(data1, data2, data3){
        var eidinput =document.getElementById('e_id').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_vendorpr/viewdata_vendor.php?module=viewdatavendor",
            data:"udata1="+data1+"&udata2="+data2+"&udata3="+data3+"&uidinput="+eidinput,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalVendor(fildnya1, fildnya2, fildnya3, d1, d2, d3){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
        document.getElementById(fildnya3).value=d3;
    }
    
    
    function getDataBarang(data1, data2){
        var eidinput =document.getElementById('e_id').value;
        var eidtp =document.getElementById('e_idtipepengajuan').value;
        
        $.ajax({
            type:"post",
            url:"module/purchasing/pch_vendorpr/viewdata_barangprvr.php?module=viewdatabarang",
            data:"udata1="+data1+"&udata2="+data2+"&uidinput="+eidinput+"&uidtp="+eidtp,
            success:function(data){
                $("#myModal").html(data);
            }
        });
    }
    
    function getDataModalBarang(fildnya1, fildnya2, d1, d2){
        document.getElementById(fildnya1).value=d1;
        document.getElementById(fildnya2).value=d2;
    }
    
    
    function disp_confirm(pText_,ket)  {
        //e_idpr, e_idpr_d, e_idvendor, e_nmvendor, e_idbrg, e_nmbrg, e_jmlqty, e_hrgbrg
        
        var eidpr =document.getElementById('e_idpr').value;
        var eidprd =document.getElementById('e_idpr_d').value;
        var eidvendor =document.getElementById('e_idvendor').value;
        var eidbrg =document.getElementById('e_idbrg').value;
        var ejml =document.getElementById('e_jmlqty').value;
        var eharga =document.getElementById('e_hrgbrg').value;
        
        if (eidpr=="") {
            alert("ID PR Kosong...");
            return false;
        }
        
        if (eidprd=="") {
            alert("ID PR Detail Kosong...");
            return false;
        }
        
        if (eidvendor=="") {
            alert("vendor masih kosong...");
            return false;
        }
        
        if (eidbrg=="") {
            alert("barang masih kosong...");
            return false;
        }
        
        if (ejml=="" || ejml=="0") {
            alert("jumlah harus diisi...");
            return false;
        }
        
        if (eharga=="" || eharga=="0") {
            alert("harga harus diisi...");
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
                document.getElementById("demo-form2").action = "module/purchasing/pch_vendorpr/aksi_isivendor.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
    
    }
</script>