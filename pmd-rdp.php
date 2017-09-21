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
        $sqlMembros = "CREATE TABLE IF NOT EXISTS dmp_templat (
                id_tp bigint(20) unsigned NOT NULL,
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
            $sx = '<h3>'.msg('plans_templat').'</h3>';
            $sx .= '<table width="100%" class="table">'.cr();
            $sx .= '<tr>';
            $sx .= '<th width="2%">'.msg("#")."</th>".cr();
            $sx .= '<th width="36%">'.msg("name")."</th>".cr();
            $sx .= '<th width="50%">'.msg("desciption")."</th>".cr();
            $sx .= '<th width="10%">'.msg("created")."</th>".cr();
            $sx .= '<th width="2%">'.msg("plans")."</th>".cr();
            $sx .= '</tr>'.cr();
            $sx .= '</table>'.cr();
            return($sx);
        }

    function cab($sub='') {
        $img = DMP_DIR.'img/icone_dmp_rnp.png';

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
    $sx = $c->cab('templat');
    $sx .= $c->templat_list();
    echo $sx;
}

function msg($t)
    {
        return($t);
    }
function cr()
    {
        return(chr(13).chr(10));
    }
?>
