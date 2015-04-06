<?php 

error_reporting(E_ALL);

function fix_html($html) {
		
		$html=str_replace('<script class="xnet_script" src="/xnet_js/xnet.js.yaws" language="JavaScript"></script> 
<script class="xnet_script" type="text/javascript" language="JavaScript"> 
   <!-- // Script added by Nortel Networks SSL-VPN
xnet.setportal("vpn.minsal.tempresas.cl");
xnet.setbend("10.6.75.33");
xnet.setprot("https");
xnet.setbprot("http");
xnet.setdepth(3);
xnet.setwhitelist(null);
xnet.setblacklist(null);
xnet.setcookie(null);
xnet.seturi("");
// END -->
</script>',"",$html);
		
		$html=str_replace("xnet.callMethodX(window,'open',","window.open(",$html);
		
		return $html;
	}
	
//phpinfo();

$data=fix_html(file_get_contents('https://vpn.minsal.tempresas.cl/http/10.6.75.33/sgh/prestaciones/consultar_web/consulta.php?rut=7171622-8&clave=123123123'));
print($data);



?>
