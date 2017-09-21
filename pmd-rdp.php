<?php
/*
 Plugin name: Data Management Planning - RDP Brasil
 Plugin url: http://www.ufrgs.br/redd/dmp/
 Description: Gerenciamento de Plano de Dados de Pesquisa
 Version 0.17.09.20
 Author: Rene Faustino Gabriel Junior
 Author Uri: http://www.ufrgs.br/reed/rene
 License: GPLv2 or Later
 License URI: https://www.gnu.org/licenses/gpl-2.0.html
 Text Domain: wporg
 Domain Path: /languages
 */

/* definitions */ 
define("DMP_VERSION","v0.17.09.20");
define("DMP_PLUGIN","DMP-Wordpress");
define("DMP_DIR","/wp-content/plugins/DMP-Wordpress/");
define("DMP_BOOTSTRAP_VERSION","v3.3.7");
define("DMP_TABLE_TEMPLAT","dmp_templat");

// CSS
wp_enqueue_style( DMP_PLUGIN,
    get_site_url().DMP_DIR.( 'css/style.css' ),
    array(), DMP_VERSION, 'all' );
    
wp_enqueue_style( "bootstrap",
    get_site_url().DMP_DIR.( 'css/bootstrap.css' ),
    array(), DMP_BOOTSTRAP_VERSION, 'all' );    
	
wp_enqueue_script("bootstrap", 
    get_site_url().DMP_DIR.( 'js/bootstrap.js' ),
    array(), DMP_BOOTSTRAP_VERSION, 'all' );  
	

/**********************************************************************************/
/* active *************************************************************************/
/**********************************************************************************/
 register_activation_hook( __FILE__, 'dmp_activate');
 function dmp_activate()
    {
        $rdp = new dmp;
        $rdp->install();
    }
 
/**********************************************************************************/
/* desative ***********************************************************************/
/**********************************************************************************/
 register_deactivation_hook( __FILE__, 'dmp_desactivate');
 function dmp_desactivate()
    {
        
    }    
    
 
class dmp {
    function install() {
        global $wpdb;
        $sqlMembros = "CREATE TABLE IF NOT EXISTS ".DMP_TABLE_TEMPLAT." (
                id_tp serial NOT NULL,
                  tp_name char(200) NOT NULL,
                  tp_id char(20),
                  tp_description text,
                  tp_status int(11),
                  tp_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  tp_used int(11) NOT NULL
                )";
        $wpdb -> query($sqlMembros);
        return (1);
    }
    /*************************************** templat ***************************/
    function templat_list()
        {
        	global $data, $wpdb;
			$novo = '<span class="btn btn-default" id="templat_new" onclick="panel_click();">'.msg('add_new').'</span>';
            echo '<h3>'.msg('plans_templat').' '.$novo.'</h3>';
						
			/* form */
			$data = array();
			if ($_POST)
				{
					$data['tp_id'] = $_POST['tp_id'];
					$data['tp_name'] = $_POST['tp_name'];
					$data['tp_descr'] = $_POST['tp_descr'];
					$data['action'] = $_POST['action'];					
				}
			$data['save'] = msg('save').' >>>';
			$sx .= view("view/templat_form.php");
			if ((strlen($data['tp_name']) > 0) and (strlen($data['tp_descr']) > 0) and (strlen($data['action']) > 0))
				{
					$this->templat_data($data);
				}
		
            $sx .= '<table width="100%" class="table">'.cr();
            $sx .= '<tr>';
            $sx .= '<th width="2%">'.msg("#")."</th>".cr();
            $sx .= '<th width="36%">'.msg("name")."</th>".cr();
            $sx .= '<th width="50%">'.msg("desciption")."</th>".cr();
            $sx .= '<th width="10%">'.msg("created")."</th>".cr();
            $sx .= '<th width="2%">'.msg("plans")."</th>".cr();
            $sx .= '</tr>'.cr();
			
			$sql = "select * from ".DMP_TABLE_TEMPLAT." where tp_status >= 1 order by tp_id ";
			$rlt = $wpdb->get_results( 
                    $wpdb->prepare($sql, $some_parameter) 
                 );
		    foreach( $rlt as $rlt ) {
				$sx .= '<tr>';
				$sx .= '<td>'.$rlt->tp_id.'</td>';
				$sx .= '<td>'.$rlt->tp_name.'</td>';
				$sx .= '<td>'.$rlt->tp_description.'</td>';				
				$sx .= '<td>'.$rlt->tp_created.'</td>';
				$sx .= '<td align="center">'.$rlt->tp_used.'</td>';
				$sx .= '</tr>'.cr();
		    }	 

            $sx .= '</table>'.cr();
            return($sx);
        }
	function templat_data($data)
		{
			global $wpdb;
			$id = $data['id_tp'];
			$tp_id = $data['tp_id'];
			$tp_name = $data['tp_name'];
			$tp_description = $data['tp_descr'];
			$tp_status = $data['tp_status'];
			
			if (isset($data['id_tp']))
				{
					$sql = "update set ".DMP_TABLE_TEMPLAT." set
								tp_name = '$t_name',
								tp_id = '$t_id',
								tp_description = '$t_description'
							where id_tp = $id";
					$wpdb -> query($sql);
				} else {
					$sql = "insert into ".DMP_TABLE_TEMPLAT." 
								(tp_name, tp_id, tp_description, tp_status, tp_used)
							values
								('$tp_name','$tp_id','$tp_description', 1, 0) ";
					$wpdb -> query($sql);
				}
				echo '<tt>'.$sql.'</tt>';
		}

    function cab($sub='') {
        $img = DMP_DIR.'img/icone-dmp-rnp.png';

        $img_logo = '<img src="' . get_site_url() . $img . '" class="img-thumbnail" style="height: 90px;" align="right">';
        $title = '<font style="background-color: yellow; padding: 0px 5px 0px 5px;" color="#0000ff">RDP</font><font color="green">Brasil</font>';
        $title .= ' - DMP';
        if (strlen($sub) > 0)
            {
                $title .= ' - '.$sub;
            }
        

        $sx = '<div class="wrap">';
        $sx .=  $img_logo;
        $sx .= '<h1 class="wp-heading-inline">' . $title . '</h1>';
        $sx .= '</div>';
        
        $sx .= '<a href="http://localhost/projeto/RDP-Brasil/wp-admin/plugin-install.php" class="page-title-action">Adicionar novo</a>';
        return($sx);
    }

}

/**********************************************************************************/
/* Admin Home Page ****************************************************************/
function dmp_rdp_plugin() {
    echo "********************** dmp_rdp_plugin";
}

/**********************************************************************************/
/* Register a custom menu page ****************************************************/
/**********************************************************************************/
function dmprdp_register_my_custom_menu_page2() {
    add_menu_page(__('Custom Menu Title', 'textdomain'), 'Data Managemment', 'manage_options', 'dmp_admin', 'dmp_admin_home', plugins_url('rdp-plugin/img/icone_menu_r.png'), 6);

    add_submenu_page('dmp_admin', 'DMP Templates', 'DMP Templates', 'manage_options', 'dmp_admin_templat', 'dmp_admin_templat', '2');
    add_submenu_page('dmp_admin', 'RDP Membros', 'Membros dos Grupos', 'manage_options', 'dmp_admin_group_members', 'rdp_admin_group_members', '1');
}

add_action('admin_menu', 'dmprdp_register_my_custom_menu_page2');

function dmp_admin_home() {
    $c = new dmp;
    $sx = $c->cab('home');
    echo $sx;
}

/*************** templat *****************************/
function dmp_admin_templat() {
    $c = new dmp;
    echo $c->cab('templat');
    $sx .= $c->templat_list();
    echo $sx;
}

function msg($t)
    {
    	return($t);
    }
function view($f)
	{
		//$f = DMP_DIR . $f;
		$dir = $_SERVER['SCRIPT_NAME'];
		$dir = substr($dir,0,strpos($dir,'/admin')); 
		$dir .= '/../'.DMP_DIR;
		$f = $dir.$f;
		if (file_exists($f))
		{
			require($f);
			return("");
		} else {
			return("ERRO VIEW");
		}
	}
function cr()
    {
        return(chr(13).chr(10));
    }
if (!function_exists('troca')) {
    function troca($qutf, $qc, $qt) {
        if (is_array($qutf)) {
            return ('erro');
        }
        return (str_replace(array($qc), array($qt), $qutf));
    }
}	
?>
