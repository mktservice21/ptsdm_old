
            
            <link rel="shortcut icon" href="images/icon.ico" />

            <style type="text/css" title="currentStyle">
                    
                    @import "v2_DTable/css/jquery-ui-1.8.4.custom.css";
            </style>

            
            <script type='text/javascript' src='js/jquery.min.js'></script>
            <script type='text/javascript' src='js/jquery-ui.min.js'></script>

            <script type='text/javascript' src='js/plugins/fancybox/jquery.fancybox.pack.js'></script>

            <script type="text/javascript">
                $(function() {
                    $('#tgl01').datepicker({
                        numberOfMonths: 1, /* bisa dua month*/
                        firstDay: 1,
                        dateFormat: 'dd MM yy', /* bisa DD dd-mm-yy*/
                        minDate: '-2' /* bisa min bisa plus -1 0 2*/
                        /*maxDate: '+1d', /* bisa +2Y*/
                    });
                });

            </script>
<?PHP
echo "<input type=text id='tgl01' name='tgl01' value='01 November 2017' placeholder='dd mmm yyyy' Readonly>";
?>