<script>
function disp_confirm(pText_,ket)  {
    
    var eid =document.getElementById('e_id').value;
    var enama =document.getElementById('e_nmcoa').value;
    
    if (eid==""){
        alert("coa kode masih kosong....");
        document.getElementById('e_id').focus();
        return 0;
    }
    if (enama==""){
        alert("coa nama masih kosong....");
        document.getElementById('e_nmcoa').focus();
        return 0;
    }
  
    
    ok_ = 1;
    if (ok_) {
        var r=confirm(pText_)
        if (r==true) {
            
            var emodule =document.getElementById('u_module').value;
            var eact =document.getElementById('u_act').value;
            var eidmenu =document.getElementById('u_idmenu').value;
            
            if (ket=="update") {
                //document.write("You pressed OK!")
                document.getElementById("demo-form2").action = "module/mod_coa_wewenang/aksi_wewenang.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                document.getElementById("demo-form2").submit();
                return 1;
            }else{
                $.ajax({
                    type:"post",
                    url:"module/mod_coa_wewenang/aksi_wewenang.php?module=carikodesama",
                    data:"ukode="+eid,
                    success:function(data){
                        var edata =data;
                        
                        if (edata=="") {
                            //document.write("You pressed OK!")
                            document.getElementById("demo-form2").action = "module/mod_coa_wewenang/aksi_wewenang.php?module="+emodule+"&act="+eact+"&idmenu="+eidmenu;
                            document.getElementById("demo-form2").submit();
                            return 1;
                        }else{
                            alert("kode sudah ada...");
                        }
                    }
                });
            }
            

        }
    } else {
        //document.write("You pressed Cancel!")
        return 0;
    }
}
</script>
<div class="">

    <div class="page-title"><div class="title_left"><h3>Data Wewenang COA</h3></div></div><div class="clearfix"></div>
    <!--row-->
    <div class="row">

        <?php
        include "config/koneksimysqli_it.php";
        $aksi="module/mod_coa_wewenang/aksi_wewenang.php";
        switch($_GET['act']){
            default:

                echo "<div class='col-md-12 col-sm-12 col-xs-12'>";

                    //panel
                    echo "<div class='x_panel'>";
                        //title
                        echo "<div class='x_title'>
                            <h2><input class='btn btn-default' type=button value='Tambah Baru'
                            onclick=\"window.location.href='?module=$_GET[module]&idmenu=$_GET[idmenu]&act=tambahbaru';\">
                            <small></small>
                            </h2>
                            <div class='clearfix'></div>
                            </div>";

                        //isi content
                        echo "<div class='x_content'>";

                            echo "<table id='datatable' class='table table-striped table-bordered'>";
                            echo "<thead><tr><th width='10px'>No</th><th width='100px'>Nama</th>"
                            . "<th width='200px'>COA</th><th width='30px'>Aksi</th></tr></thead>";
                            echo "<tbody>";
                            $no=1;
                            $tampil = mysqli_query($cnit, "SELECT distinct karyawanId, nama FROM dbmaster.v_coa_wewenang order by nama");
                            while ($r=mysqli_fetch_array($tampil)){
                                echo "<tr scope='row'>";
                                echo "<td>$no</td>";
                                echo "<td>$r[nama]</td>";
                                $coa=""; $nurut=1;
                                $tampil2 = mysqli_query($cnit, "SELECT distinct COA4, NAMA4 FROM dbmaster.v_coa_wewenang where karyawanId='$r[karyawanId]' order by NAMA4");
                                while ($a=mysqli_fetch_array($tampil2)){
                                    $coa .="<small><b>(".$a['COA4'].")</b></small> ".$a['NAMA4'].", ";
                                    if ($nurut==2) {
                                        $coa .="<br/>";
                                        $nurut=0;
                                    }
                                    $nurut++;
                                }
                                echo "<td>$coa</td>";
                                echo "<td><a class='btn btn-success btn-xs' href='?module=$_GET[module]&act=editdata&idmenu=$_GET[idmenu]&id=$r[karyawanId]'>Edit</a>"
                                        . "";
                                
                                if ($_SESSION['LEVELUSER']=="admin"){
                                    echo "<a class='btn btn-danger btn-xs' href=\"$aksi?module=$_GET[module]&act=hapus&id=$r[karyawanId]&idmenu=$_GET[idmenu]\"
                                        onClick=\"return confirm('Apakah akan menghapus data...?')\">Hapus</a>";
                                }
                                echo "</td></tr>";
                                $no++;
                            }
                            echo "</tbody>";
                            echo "</table>";
                        echo "</div>";//end x_content

                    echo "</div>";//end panel

                echo "</div>";

            break;

            case "tambahbaru":
                include "tambah.php";
            break;

            case "editdata":
                include "tambah.php";
            break;

        }
        ?>

    </div>
    <!--end row-->
</div>
