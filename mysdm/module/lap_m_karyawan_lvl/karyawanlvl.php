<script>
function disp_confirm(pText_)  {
    var eid =document.getElementById('id').value;
    var ejbt =document.getElementById('cb_jabatan').value;
    var eatasan =document.getElementById('cb_atasan').value;
    
    if (eid==""){
        alert("karyawan masih kosong....");
        return 0;
    }
    if (ejbt==""){
        alert("jabatan masih kosong....");
        return 0;
    }
    if (eatasan==""){
        alert("atasan masih kosong....");
        return 0;
    }
    
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            //document.write("You pressed OK!")
            document.getElementById("demo-form2").action = "module/lap_m_karyawan_lvl/aksi_karyawanlvl.php";
            document.getElementById("demo-form2").submit();
            return 1;
        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}
</script>

<div class="">
    <!--page-title-->
    <div class="col-md-12 col-sm-12 col-xs-12"><div class="title_left"><h3>Level Posisi Karyawan</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_it.php";
        $aksi="module/lap_m_karyawan_lvl/karyawanlvl.php";
        switch($_GET['act']){
            default:
                ?>
                <script type="text/javascript" language="javascript" src="js/jquery.js"></script>
                <script type="text/javascript" language="javascript" >
                    $(document).ready(function() {
                        var aksi = "module/lap_m_karyawan_lvl/karyawanlvl.php";
                        var myurl = window.location;
                        var urlku = new URL(myurl);
                        var module = urlku.searchParams.get("module");
                        var idmenu = urlku.searchParams.get("idmenu");
                        var nmun = urlku.searchParams.get("nmun");
                        var dataTable = $('#datatable').DataTable( {
                            "processing": true,
                            "serverSide": true,
                            "lengthMenu": [[10, 50, 100], [10, 50, 100]],
                            "displayLength": 50,
                            "order": [[ 1, "ASC" ]],
                            "ajax":{
                                url :"module/lap_m_karyawan_lvl/mydata.php?module="+module+"&idmenu="+idmenu+"&nmun="+nmun+"&aksi="+aksi, // json datasource
                                type: "post",  // method  , by default get
                                error: function(){  // error handling
                                    $(".data-grid-error").html("");
                                    $("#datatable").append('<tbody class="data-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                                    $("#data-grid_processing").css("display","none");
                                    
                                }
                            }
                        } );
                    } );
                </script>
                <?PHP
                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        

                        //isi content
                        echo "<div class='x_content'>";
                        
                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th>MR - LEVEL1</th><th>SPV - LEVEL2</th>"
                                    . "<th>DM - LEVEL3</th><th>SM - LEVEL4</th>"
                                    . "<th>RSM - LEVEL5</th><th>PM - LEVEL6</th>"
                                    . "<th>NSM - LEVEL7</th>"
                                    . "<th>LEVEL8</th>"
                                    . "<th>LEVEL9</th>"
                                    . "</tr></thead>";
                            echo "</table>";
                          

                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                
            break;

            case "editdata":
                $edit=mysqli_query($cnit, "SELECT * FROM dbmaster.v_karyawan WHERE karyawanId='$_GET[id]'");
                $r=mysqli_fetch_array($edit);
                ?>
                <form method='POST' action='<?PHP echo "$aksi?module=$_GET[module]&act=update&idmenu=$_GET[idmenu]"; ?>' id='demo-form2' data-parsley-validate class='form-horizontal form-label-left'>
                    
                    <input type='hidden' id='u_module' name='u_module' value='<?PHP echo $_GET['module']; ?>' Readonly>
                    <input type='hidden' id='u_idmenu' name='u_idmenu' value='<?PHP echo $_GET['idmenu']; ?>' Readonly>
                    <input type='hidden' id='u_act' name='u_act' value='update' Readonly>

                    <div class='col-md-12 col-sm-12 col-xs-12'>
                        <div class='x_panel'>
                            <div class='x_title'>
                                <h2><input type='button' value='Back' onclick='self.history.back()' class='btn btn-default'>
                                    <button type='button' class='btn btn-success' onclick='disp_confirm("Edit Data ?")'>Save</button>
                                <small>Edit Data</small></h2>
                                <div class='clearfix'></div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='e_nama'>Nama <span class='required'>*</span></label>
                                <div class='col-md-6 col-sm-6 col-xs-12'>
                                    <input type='hidden' id='id' name='id' class='form-control col-md-7 col-xs-12' value='<?PHP echo $r['karyawanId']; ?>'>
                                    <input type='text' id='e_nmkategori' name='e_nama' required='required' class='form-control col-md-7 col-xs-12' readonly value='<?PHP echo $r['nama']; ?>'>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_jabatan'>Jabatan <span class='required'></span></label>
                                <div class='col-md-6 col-sm-6 col-xs-12'>
                                    <select class='form-control' id='cb_jabatan' name='cb_jabatan' onchange="">
                                        <?PHP
                                        $tampil=mysqli_query($cnit, "SELECT jabatanId, nama, LEVELPOSISI FROM dbmaster.v_level_jabatan order by nama");
                                        echo "<option value='' selected>-- Pilihan --</option>";
                                        while($a=mysqli_fetch_array($tampil)){ 
                                            $level="";
                                            if (!empty($a['LEVELPOSISI'])) $level=" (".$a['LEVELPOSISI'].")";
                                            if ($a['jabatanId']==$r['jabatanId'])
                                                echo "<option value='$a[jabatanId]' selected>$a[nama] $level</option>";
                                            else
                                                echo "<option value='$a[jabatanId]'>$a[nama] $level</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_atasan'>Atasan <span class='required'></span></label>
                                <div class='col-md-6 col-sm-6 col-xs-12'>
                                    <select class='form-control' id='cb_atasan' name='cb_atasan' onchange="">
                                        <?PHP
                                        $tampil=mysqli_query($cnit, "SELECT karyawanId, nama, LEVELPOSISI FROM dbmaster.v_karyawan order by nama");
                                        echo "<option value='' selected>-- Pilihan --</option>";
                                        while($a=mysqli_fetch_array($tampil)){
                                            if ($a['karyawanId']==$r['atasanId'])
                                                echo "<option value='$a[karyawanId]' selected>$a[nama] <small>($a[LEVELPOSISI])</small></option>";
                                            else
                                                echo "<option value='$a[karyawanId]'>$a[nama] <small>($a[LEVELPOSISI])</small></option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class='form-group'>
                                <label class='control-label col-md-3 col-sm-3 col-xs-12' for='cb_divisi'>Area <span class='required'></span></label>
                                <div class='col-md-6 col-sm-6 col-xs-12'>
                                    <select class='form-control' id='cb_divisi' name='cb_divisi' onchange="">
                                        <?PHP
                                        $tampil=mysqli_query($cnit, "SELECT distinct iCabangId, nama from dbmaster.icabang order by nama");
                                        echo "<option value='' selected>-- Pilihan --</option>";
                                        while($a=mysqli_fetch_array($tampil)){
                                            if ($a['iCabangId']==$r['iCabangId'])
                                                echo "<option value='$a[iCabangId]' selected>$a[nama]</option>";
                                            else
                                                echo "<option value='$a[iCabangId]'>$a[nama]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            

                        </div>
                    </div>
                </form>
                <?PHP
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
