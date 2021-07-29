<?PHP
    date_default_timezone_set('Asia/Jakarta');
    ini_set("memory_limit","512M");
    ini_set('max_execution_time', 0);
    
$pidbr="";
$pidkodeinput="";
$hari_ini = date("Y-m-d");
$tgl1 = date('d/m/Y', strtotime($hari_ini));
$tgl2 = date('t/m/Y', strtotime($hari_ini));
$tglberlku = date('m/Y', strtotime($hari_ini));
$ptglajukan = date('d/m/Y', strtotime($hari_ini));

$tgl_pertama = date('01 F Y', strtotime($hari_ini));
$tgl_terakhir = date('t F Y', strtotime($hari_ini));

$pbulanpilih = date('F Y', strtotime($hari_ini));


$pidgroup=$_SESSION['GROUP'];
$pidjbtpl=$_SESSION['JABATANID'];
$pidcardpl=$_SESSION['IDCARD'];
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP']; 

    
$pidtipe="101";
$untukpil1="selected";
$untukpil2="";

$pketerangan="";
$psudahtampil="";
$ptotjml="";

$pjmlrec=0;

$sudahapv="";

$pmodule=$_GET['module'];
$pidmenu=$_GET['idmenu'];
$pmyact=$_GET['act'];
$pact=$_GET['act'];

    $act="update";
    
    $pidbr=$_GET['id'];
    $pidbr_d=$_GET['xd'];
    
    $edit = mysqli_query($cnmy, "SELECT * FROM dbpurchasing.t_pr_transaksi WHERE idpr='$pidbr'");
    $r    = mysqli_fetch_array($edit);
    $ptglajukan = date('d/m/Y', strtotime($r['tanggal']));
    $pidtipe=$r['idtipe'];
    $idajukan=$r['karyawanid'];
    $pcabangid=$r['icabangid'];
    $pketerangan=$r['aktivitas'];
    



?>

<!-- Modal -->
<div class='modal fade' id='myModal' role='dialog'></div>

<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>


<div class="">

    <!--row-->
    <div class="row">
        
        
        <form method='POST' action='<?PHP echo "$aksi?module=$pmodule&act=input&idmenu=$pidmenu"; ?>' 
              id='form_aksi' name='form1' data-parsley-validate 
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
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID <span class='required'></span></label>
                                    <div class='col-md-4'>
                                        <input type='text' id='e_id' name='e_id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr; ?>' Readonly>
                                        <input type='text' id='e_did' name='e_did' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidbr_d; ?>' Readonly>
                                        <input type='hidden' id='e_idcardlogin' name='e_idcardlogin' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pidcardpl; ?>' Readonly>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Tanggal </label>
                                    <div class='col-md-3'>
                                        <div class='input-group date' id=''>
                                            <input type="text" class="form-control" id='e_tglberlaku_' name='e_tglberlaku_' autocomplete='off' required='required' placeholder='dd/MM/yyyy' data-inputmask="'mask': '99/99/9999'" value='<?PHP echo $ptglajukan; ?>' Readonly>
                                            <span class='input-group-addon'>
                                                <span class='glyphicon glyphicon-calendar'></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Pembuat <span class='required'></span></label>
                                    <div class='col-xs-5'>
                                          <select class='form-control input-sm' id='cb_karyawan' name='cb_karyawan' onchange="ShowDataKaryawan();" data-live-search="true">
                                              
                                              <?PHP 
                                                    $query = "select karyawanId, nama From hrd.karyawan
                                                        WHERE 1=1 ";
                                                        $query .= " AND karyawanid ='$pidcardpl' ";
                                                    $query .= " ORDER BY nama";
                                                    $tampil = mysqli_query($cnmy, $query);
                                                    $ketemu= mysqli_num_rows($tampil);
                                                    
                                                    if ((DOUBLE)$ketemu<=0) echo "<option value='' selected>-- Pilihan --</option>";
                                                    
                                                    while ($z= mysqli_fetch_array($tampil)) {
                                                        $pkaryid=$z['karyawanId'];
                                                        $pkarynm=$z['nama'];
                                                        $pkryid=(INT)$pkaryid;

                                                        echo "<option value='$pkaryid' selected>$pkarynm ($pkryid)</option>";

                                                    }
                                                
                                              ?>
                                          </select>
                                    </div>
                                </div>
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Notes <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_notes' name='e_notes' class='form-control col-md-7 col-xs-12' value='<?PHP echo $pketerangan; ?>' Readonly>
                                    </div>
                                </div>
                                

                                
                                <div hidden class='form-group'>
                                    <div id='loading2'></div>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>
                                        <div id="div_sdh_tmpil">
                                            &nbsp;
                                        </div>
                                    </label>
                                    <div class='col-md-3'>
                                        <input type='hidden' id='e_sdhtmpl' name='e_sdhtmpl' class='form-control col-md-7 col-xs-12' value='<?PHP echo $psudahtampil; ?>' Readonly>
                                        <input type='hidden' id='e_totjml' name='e_totjml' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $ptotjml; ?>' Readonly>
                                    </div>
                                </div>
                                
                                
                                
                                
                                
                            </div>
                        </div>
                    </div>
                    
                    
                    <div class='col-md-12 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_content form-horizontal form-label-left'>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID JML <span class='required'></span></label>
                                    <div class='col-xs-6'>
                                        <input type='text' id='e_idjmlrec' name='e_idjmlrec' class='form-control col-md-7 col-xs-12 inputmaskrp2' value='<?PHP echo $pjmlrec; ?>' Readonly>
                                        <input type='text' class='form-control' id='e_idbrg2' name='e_idbrg2' readonly>
                                        <input type='text' class='form-control' id='e_nmbrg2' name='e_nmbrg2' readonly>
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>ID Barang <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <div class='input-group '>
                                        <span class='input-group-btn'>
                                            <button type='button' class='btn btn-primary' data-toggle='modal' data-target='#myModal' onClick="getDataBarang('e_idbrg', 'e_nmbrg', 'e_spek', 'e_hrgbrg')">Pilih!</button>
                                        </span>
                                        <input type='text' class='form-control' id='e_idbrg' name='e_idbrg' value='<?PHP //echo $pbrnoid; ?>' Readonly>
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Nama Barang <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <input type='text' id='e_nmbrg' name='e_nmbrg' class='form-control col-md-7 col-xs-12' style="text-transform: uppercase" maxlength="150" onblur='CekBarangKode()'>
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Spesifikasi / Uraian <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_spek" name='e_spek' maxlength="450"></textarea>
                                    </div>
                                </div>
                                
                                
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Jumlah <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_jmlqty' name='e_jmlqty' class='form-control col-md-7 col-xs-12 inputmaskrp2'  >
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Satuan <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_satuanbrg' name='e_satuanbrg' class='form-control col-md-7 col-xs-12' onkeypress="return event.charCode < 48 || event.charCode  >57" style="text-transform: uppercase" >
                                    </div>
                                </div>
                                
                                <div hidden class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Harga <span class='required'></span></label>
                                    <div class='col-xs-3'>
                                        <input type='text' id='e_hrgbrg' name='e_hrgbrg' class='form-control col-md-7 col-xs-12 inputmaskrp2'  >
                                        *) harga estimasi, bisa dikosongkan...
                                    </div>
                                </div>
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>Keterangan <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <textarea class='form-control' id="e_ketdetail" name='e_ketdetail' maxlength='300'></textarea>
                                    </div>
                                </div>
                                
                                
                                <div class='form-group'>
                                    <label class='control-label col-md-3 col-sm-3 col-xs-12' for=''>&nbsp; <span class='required'></span></label>
                                    <div class='col-xs-9'>
                                        <button type='button' class='btn btn-dark btn-xs add-row' onclick='TambahDataBarang("")'>&nbsp; &nbsp; &nbsp; Tambah &nbsp; &nbsp; &nbsp;</button>
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
                                <th width='5px' nowrap></th>
                                <th width='10px' align='center' class='divnone'></th><!--class='divnone' -->
                                <th width='5px' align='center'>&nbsp;</th>
                                <th width='20px' align='center'>Kode</th>
                                <th width='200px' align='center'>Nama Barang</th>
                                <th width='200px' align='center'>Spesifikasi / Uraian</th>
                                <th width='40px' align='center'>Jumlah</th>
                                <th width='20px' align='center'>Satuan</th>
                                <th width='40px' align='center'>Harga</th>
                                <th width='40px' align='center'>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class='inputdata'>
                            <?PHP
                            
                                $nnjmlrc=0;
                                $query ="SELECT idpr, idpr_d, idbarang, namabarang, idbarang_d, spesifikasi1, spesifikasi2, uraian, "
                                        . " keterangan, jumlah as jml, harga as rp_pr, satuan "
                                        . " FROM dbpurchasing.t_pr_transaksi_d WHERE idpr='$pidbr' AND idpr_d='$pidbr_d'";
                                $tampild=mysqli_query($cnmy, $query);
                                while ($nrd= mysqli_fetch_array($tampild)) {
                                    $pidbrg=$nrd['idbarang'];
                                    $pnmbrg=$nrd['namabarang'];
                                    $pspcbrg=$nrd['spesifikasi1'];
                                    $pketbrg=$nrd['keterangan'];
                                    $pstnbrg=$nrd['satuan'];
                                    $pjmldet=$nrd['jml'];
                                    $phargarp=$nrd['rp_pr'];
                                    
                                    if (empty($pjmldet)) $pjmldet=0;
                                    if (empty($phargarp)) $phargarp=0;
                                    $ijmlbrg=number_format($pjmldet,0,",",",");
                                    $ihargabrg=number_format($phargarp,0,",",",");
                                    
                                    echo "<tr>";
                                    echo "<td nowrap>&nbsp;</td>";
                                    echo "<td nowrap class='divnone'>&nbsp;</td>";
                                    
                                    echo "<td>&nbsp;</td>";
                                    
                                    echo "<td nowrap>$pidbrg</td>";
                                    echo "<td nowrap>$pnmbrg</td>";
                                    echo "<td >$pspcbrg</td>";
                                    echo "<td nowrap align='right'>$ijmlbrg</td>";
                                    echo "<td nowrap>$pstnbrg</td>";
                                    echo "<td nowrap align='right'>$ihargabrg</td>";
                                    echo "<td >$pketbrg</td>";
                                    
                                    echo "</tr>";
                                    
                                    
                                }
                            
                            ?>
                        </tbody>
                    </table>
                    <button type='button' class='btn btn-danger btn-xs delete-row' >&nbsp; &nbsp; Hapus &nbsp; &nbsp;</button>
                </div>
                
            </div>
            
            
            <div class='col-md-12 col-sm-12 col-xs-12'>
                <div class='x_panel'>
                <?PHP
                if (empty($sudahapv)) {
                    if ($pmyact=="editdata") {
                        ?>
                        <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?", "<?PHP echo $act; ?>")'>Update</button>
                        <?PHP
                    }else{
                        echo "<div class='col-sm-5'>";
                        include "module/purchasing/pch_prosesprit/ttd_pchreq.php";
                        echo "</div>";
                    }
                ?>
                <?PHP
                }elseif ($sudahapv=="reject") {
                    echo "data sudah hapus";
                }else{
                    echo "tidak bisa diedit, sudah approve";
                }
                ?>
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
    
    function disp_confirm(pText_,ket)  {

        var iid = document.getElementById('e_id').value;
        var idid = document.getElementById('e_did').value;
        var ikry = document.getElementById('cb_karyawan').value;
        var esudahada=document.getElementById('e_sdhtmpl').value;

        if (iid=="") {
            alert("ID kosong...");
            return false;
        }
        
        if (idid=="") {
            alert("ID kosong...");
            return false;
        }


        if (ikry=="") {
            alert("Pembuat masih kosong...");
            return false;
        }

        if (esudahada=="" || esudahada=="0") {
            alert("barang masih kosong...");
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
                document.getElementById("form_aksi").action = "module/purchasing/pch_prosesprit/aksi_prosesprit.php?module="+module+"&act="+ket+"&idmenu="+idmenu;
                document.getElementById("form_aksi").submit();
                return 1;
            }
        } else {
            //document.write("You pressed Cancel!")
            return 0;
        }
        
        
        
    }
</script>


<script>
    $(document).ready(function(){
        $("#add_new").click(function(){
            $(".entry-form").fadeIn("fast");
        });

        $("#close").click(function(){
            $(".entry-form").fadeOut("fast");
        });

        $("#cancel").click(function(){
            $(".entry-form").fadeOut("fast");
        });
        
        $(".add-row").click(function(){
            
            var newchar = '';
            var i_idjmlrec = $("#e_idjmlrec").val();
            var i_idbrg2 = $("#e_idbrg2").val();
            var i_nmbrg2 = $("#e_nmbrg2").val();
            var i_idbrg = $("#e_idbrg").val();
            var i_nmbrg = $("#e_nmbrg").val();
            var i_specbrg = $("#e_spek").val();
            var i_ket = $("#e_ketdetail").val();
            var i_satuan = $("#e_satuanbrg").val();
            var i_jml = $("#e_jmlqty").val();
            var i_hrg = $("#e_hrgbrg").val();
            
            if (i_nmbrg=="" && i_specbrg=="" && i_jml=="") {
                alert("masih kosong...."); return false;
            }
            
            if (i_nmbrg=="") {
                alert("nama barang harus diisi...!!!"); return false;
            }
            
            if (i_jml=="") {
                alert("jumlah harus diisi...!!!"); return false;
            }
            
            i_nmbrg = i_nmbrg.toUpperCase();
            i_nmbrg2 = i_nmbrg2.toUpperCase();
            i_satuan = i_satuan.toUpperCase();
            
            var xtxnmbrg = i_nmbrg.replace(/\s/gm,"");
            var ntxtspc = i_specbrg.replace(/\s/gm,"");
            
            var arjmlrec = document.getElementsByName('m_idjmrec[]');
            for (var i = 0; i < arjmlrec.length; i++) {
                var ijmlrec = arjmlrec[i].value;
                
                var ikdbrg = document.getElementById('m_idbrg['+ijmlrec+']').value;
                var inmbrg = document.getElementById('m_nmbrg['+ijmlrec+']').value;
                var ispcbrg = document.getElementById('txt_specbr['+ijmlrec+']').value;
                
                
                var inmbrg = inmbrg.replace(/\s/gm,"");
                var xspcbrg = ispcbrg.replace(/\s/gm,"");
                
                if (ikdbrg==i_idbrg && inmbrg==xtxnmbrg && xspcbrg==ntxtspc) {
                    return false;
                }
            }
            
            
            var markup;
            markup = "<tr>";
            markup += "<td nowrap><input type='checkbox' name='record'>";
            markup += "<input type='hidden' id='m_idjmrec["+i_idjmlrec+"]' name='m_idjmrec[]' value='"+i_idjmlrec+"' Readonly>";
            markup += "<input type='hidden' id='m_idbrg2["+i_idjmlrec+"]' name='m_idbrg2["+i_idjmlrec+"]' value='"+i_idbrg2+"'>";
            markup += "<input type='hidden' id='m_nmbrg2["+i_idjmlrec+"]' name='m_nmbrg2["+i_idjmlrec+"]' value='"+i_nmbrg2+"'>";
            markup += "</td>";
            markup += "<td nowrap class='divnone'><input type='checkbox' name='chkbox_br[]' id='chkbox_br["+i_idjmlrec+"]' value='"+i_idjmlrec+"' checked></td>";
            
            markup += "<td><button type='button' class='btn btn-warning btn-xs' onclick=\"EditDataBarang('chkbox_br[]', '"+i_idjmlrec+"')\">Edit</button></td>";
            
            markup += "<td nowrap>" + i_idbrg + "<input type='hidden' id='m_idbrg["+i_idjmlrec+"]' name='m_idbrg["+i_idjmlrec+"]' value='"+i_idbrg+"'></td>";
            markup += "<td nowrap>" + i_nmbrg + "<input type='hidden' id='m_nmbrg["+i_idjmlrec+"]' name='m_nmbrg["+i_idjmlrec+"]' value='"+i_nmbrg+"'></td>";
            markup += "<td >" + i_specbrg + "<span hidden><textarea class='form-control' id='txt_specbr["+i_idjmlrec+"]' name='txt_specbr["+i_idjmlrec+"]'>"+i_specbrg+"</textarea></span></td>";
            markup += "<td nowrap align='right'>" + i_jml + "<input type='hidden' class='form-control inputmaskrp2' id='txt_njmlbrg["+i_idjmlrec+"]' name='txt_njmlbrg["+i_idjmlrec+"]' value='"+i_jml+"'></td>";
            markup += "<td nowrap>" + i_satuan + "<input type='hidden' id='m_satuan["+i_idjmlrec+"]' name='m_satuan["+i_idjmlrec+"]' value='"+i_satuan+"'></td>";
            markup += "<td nowrap align='right'>" + i_hrg + "<input type='hidden' class='form-control inputmaskrp2' id='txt_nhrgbrg["+i_idjmlrec+"]' name='txt_nhrgbrg["+i_idjmlrec+"]' value='"+i_hrg+"'></td>";
            markup += "<td >" + i_ket + "<span hidden><textarea class='form-control' id='txt_ketbrg["+i_idjmlrec+"]' name='txt_ketbrg["+i_idjmlrec+"]'>"+i_ket+"</textarea></span></td>";
            markup += "</tr>";
            $("table tbody.inputdata").append(markup);
            
            document.getElementById('e_sdhtmpl').value="1";
            
            if (i_idjmlrec=="") i_idjmlrec="0";
            i_idjmlrec = i_idjmlrec.split(',').join(newchar);
            i_idjmlrec=parseFloat(i_idjmlrec)+1;
            document.getElementById('e_idjmlrec').value=i_idjmlrec;
            
        });
        
        $(".delete-row").click(function(){
            
            var ilewat = false;
            $("table tbody.inputdata").find('input[name="record"]').each(function(){
                if($(this).is(":checked")){
                    $(this).parents("tr").remove();
                    ilewat = true;
                }
            });

            if (ilewat == true) {
                
            }
            
        });
        
        
    });
    

    function EditDataBarang(xchk, xidjmlrec) {
        var xkdbrg2 = document.getElementById('m_idbrg2['+xidjmlrec+']').value;
        var xnmbrg2 = document.getElementById('m_nmbrg2['+xidjmlrec+']').value;
        
        var xkdbrg = document.getElementById('m_idbrg['+xidjmlrec+']').value;
        var xnmbrg = document.getElementById('m_nmbrg['+xidjmlrec+']').value;
        var xspec = document.getElementById('txt_specbr['+xidjmlrec+']').value;
        var xjml = document.getElementById('txt_njmlbrg['+xidjmlrec+']').value;
        var xhrg = document.getElementById('txt_nhrgbrg['+xidjmlrec+']').value;
        var xket = document.getElementById('txt_ketbrg['+xidjmlrec+']').value;
        var xstn = document.getElementById('m_satuan['+xidjmlrec+']').value;
        
        
        document.getElementById('e_idbrg2').value=xkdbrg2;
        document.getElementById('e_nmbrg2').value=xnmbrg2;
        document.getElementById('e_idbrg').value=xkdbrg;
        document.getElementById('e_nmbrg').value=xnmbrg;
        document.getElementById('e_spek').value=xspec;
        document.getElementById('e_hrgbrg').value=xhrg;
        document.getElementById('e_jmlqty').value=xjml;
        document.getElementById('e_ketdetail').value=xket;
        document.getElementById('e_satuanbrg').value=xstn;
        
        $("table tbody.inputdata").find('input[id="chkbox_br['+xidjmlrec+']"]').each(function(){
            $(this).parents("tr").remove();
        });
        
    }
    
    function CekBarangKode() {
        var ikdbrg2=document.getElementById('e_idbrg2').value;
        var inmbrg2=document.getElementById('e_nmbrg2').value;
        var ikdbrg1=document.getElementById('e_idbrg').value;
        var inmbrg1=document.getElementById('e_nmbrg').value;
        
        var inmbrg2_ = inmbrg2.replace(/\s/gm,"");
        var inmbrg1_ = inmbrg1.replace(/\s/gm,"");
        
        if (inmbrg2_!=inmbrg1_) {
            document.getElementById('e_idbrg').value="";
        }else{
            document.getElementById('e_idbrg').value=ikdbrg2;
        }
        
    }
</script>