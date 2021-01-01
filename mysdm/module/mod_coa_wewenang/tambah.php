<link href="css/inputselectbox.css" rel="stylesheet" type="text/css" />
<script> window.onload = function() { document.getElementById("e_id").focus(); } </script>
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

function disp_confirm(pText_)  {
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/mod_coa_wewenang/aksi_wewenang.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}
</script>
<style>
    table.example_2 {
        color: #333;
        font-family: Helvetica, Arial, sans-serif;
        width: 640px;
        border-collapse:
        collapse; border-spacing: 0;
    }

    td, th {
        border: 1px solid transparent; /* No more visible border */
        height: 30px;
        transition: all 0.3s;  /* Simple transition for hover effect */
    }

    th {
        background: #DFDFDF;  /* Darken header a bit */
        font-weight: bold;
    }

    td {
        background: #FAFAFA;
    }

    /* Cells in even rows (2,4,6...) are one color */
    tr:nth-child(even) td { background: #F1F1F1; }

    /* Cells in odd rows (1,3,5...) are another (excludes header cells)  */
    tr:nth-child(odd) td { background: #FEFEFE; }

    tr td:hover.biasa { background: #666; color: #FFF; }
    tr td:hover.left { background: #ccccff; color: #000; }

    tr td.center1, td.center2 { text-align: center; }

    tr td:hover.center1 { background: #666; color: #FFF; text-align: center; }
    tr td:hover.center2 { background: #ccccff; color: #000; text-align: center; }
    /* Hover cell effect! */
</style>
            
            
<?PHP
$idajukan=$_SESSION['IDCARD']; 
$nmajukan=$_SESSION['NAMALENGKAP'];
$act="input";
if ($_GET['act']=="editdata"){
    $act="update";
    
    
}
?>
<div class='col-md-12 col-sm-12 col-xs-12'>

    <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=input&idmenu=$_GET[idmenu]"; ?>' 
          id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
        
    <div class='x_panel'>

        <div class='x_title'>
            <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
            <button type='button' class='btn btn-success' onclick='disp_confirm("Simpan ?")'>Save</button>
            <small>Tambah Baru</small></h2>
            <div class='clearfix'></div>
        </div>


        <div class='x_content'><br/>

                
            <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
            <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
            
            <input type='hidden' id='u_act' name='u_act' value='<?PHP echo $act; ?>' Readonly>
            
            
                <div class='form-group'>
                      <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_idkaryawan'>Karyawan <span class='required'></span></label>
                      <div class='col-xs-9'>
                          <select class='soflow' id='e_idkaryawan' name='e_idkaryawan'>
                              <?PHP
                                if ($_GET["act"]=="editdata") {
                                    $tampil = mysqli_query($cnit, "SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan "
                                            . " where karyawanId = '$_GET[id]' order by nama, karyawanId");
                                }else{
                                    echo "<option value='' selected>-- Pilihan --</option>";
                                    $tampil = mysqli_query($cnit, "SELECT DISTINCT karyawanId, nama FROM dbmaster.karyawan "
                                            . " where karyawanId not in (select distinct karyawanId from dbmaster.coa_wewenang) order by nama, karyawanId");
                                }
                                while($a=mysqli_fetch_array($tampil)){ 
                                    if ($a['karyawanId']==$idajukan)
                                        echo "<option value='$a[karyawanId]' selected>$a[nama] - $a[karyawanId]</option>";
                                    else
                                        echo "<option value='$a[karyawanId]'>$a[nama] - $a[karyawanId]</option>";
                                }
                                ?>
                          </select>
                      </div>
                </div>
                <br/>&nbsp;
                <table id='datatable2' class='table table-striped table-bordered'>
                    <thead>
                    <tr><th width="20px">No</th>
                        <th width="50px"><input type="checkbox" id="chkbtall" value="select" onClick="SelAllCheckBox('chkbtall', 'tag_coa[]')" /></th>
                        <th>COA</th>
                        <th>Nama COA</th>
                        <th width="150px">Kode</th>
                        <th width="250px">Nama Kode</th>
                    </tr>
                    </thead>
                    <tbody>
                        <?PHP
                        $no=1;
                        $sql="select * from dbmaster.v_coa_all WHERE 1=1 ";
                        if ($_SESSION["ADMINKHUSUS"]=="Y") {
                            $sql .= " AND (DIVISI in $_SESSION[KHUSUSSEL] or ifnull(DIVISI,'') = '')";
                            $sql .= " AND (ifnull(kodeid,'') <> '' OR ifnull(subpost,'') <> '') ";
                        }
                        $sql .= " order by COA4";
                        $tampil=  mysqli_query($cnit, $sql);
                        while ($r=  mysqli_fetch_array($tampil)) {
                            $chk="";
                            if ($_GET["act"]=="editdata") {
                                $coa=  getfieldit("select COA4 as lcfields from dbmaster.coa_wewenang where karyawanId='$_GET[id]' and COA4='$r[COA4]'");
                                //if ($coa=="0") $coa="";
                                if ($coa!="") $chk="checked";
                            }
                            echo "<tr>";
                            echo "<td>$no</td>";
                            echo "<td><input type=checkbox value='$r[COA4]' name=tag_coa[] $chk></td>";
                            echo "<td>$r[COA4]</td>";
                            echo "<td>$r[NAMA4]</td>";
                            $kode=$r['kodeid'];
                            $namakode=$r['nama_kode'];
                            if ($r['DIVISI']=="OTC") {
                                if (!empty($r['subpost']) AND empty($r['nmsubpost'])) {
                                    $kode=$r['subpost'];
                                    $namakode=  getfieldit("select nmsubpost as lcfields from hrd.brkd_otc where subpost='$kode'");
                                }elseif (!empty($r['nmsubpost'])) {
                                    $kode=$r['subpost'];
                                    $namakode=$r['nmsubpost'];
                                }else{
                                    $kode=$r['kodeid'];
                                    $namakode=$r['nama_kodeotc'];
                                }
                            }
                            echo "<td>$kode</td>";
                            echo "<td>$namakode</td>";
                            
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