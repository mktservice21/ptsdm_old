
<div class='modal fade' id='myModalAbsen' role='dialog' class='no-print'></div>
<div class="animated flipInY col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="tile-stats">

        <div class="icon">
            <i class="fa fa-check-square-o"></i>

        </div>
        <div class="count">17:00</div>
        <h3>
            <!--<button type='button' class='btn btn-default' id='ibuttonsave' data-toggle='modal' data-target='#myModalAbsen' onclick='ShowFormAbsen("2")'>Absen Masuk</button>-->
            <a href='?module=hrdabsenmasuk&act=absenpulang&idmenu=522&kriteria=Y' class='btn btn-default' id='ibuttonsave'>Absen Pulang</a>
        </h3>
        <p>&nbsp;</p>
    </div>
</div>

<script>
    
    function ShowFormAbsen(sKey) {
        $.ajax({
            type:"post",
            url:"module/hrd/hrd_absen/upload_webcam.php?module=uploadwebcame",
            data:"ukey="+sKey,
            success:function(data){
                $("#myModalAbsen").html(data);
            }
        });
    }
</script>