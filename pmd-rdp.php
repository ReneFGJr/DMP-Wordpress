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
define("DMP_VERSION", "v0.17.09.20");
define("DMP_PLUGIN", "DMP-Wordpress");
define("DMP_DIR", "/wp-content/plugins/DMP-Wordpress/");
define("DMP_BOOTSTRAP_VERSION", "v3.3.7");
define("DMP_TABLE_TEMPLAT", "dmp_templat");

// CSS
wp_enqueue_style(DMP_PLUGIN, get_site_url() . DMP_DIR . ('css/style.css'), array(), DMP_VERSION, 'all');

wp_enqueue_style("bootstrap", get_site_url() . DMP_DIR . ('css/bootstrap.css'), array(), DMP_BOOTSTRAP_VERSION, 'all');

wp_enqueue_script("bootstrap", get_site_url() . DMP_DIR . ('js/bootstrap.js'), array(), DMP_BOOTSTRAP_VERSION, 'all');

/**********************************************************************************/
/* active *************************************************************************/
/**********************************************************************************/
register_activation_hook(__FILE__, 'dmp_activate');
function dmp_activate() {
    $rdp = new dmp;
    $rdp -> install();
}

/**********************************************************************************/
/* desative ***********************************************************************/
/**********************************************************************************/
register_deactivation_hook(__FILE__, 'dmp_desactivate');
function dmp_desactivate() {

}

class dmp {
    function install() {
        global $wpdb;
        $sqlMembros = "CREATE TABLE IF NOT EXISTS " . DMP_TABLE_TEMPLAT . " (
                id_tp serial NOT NULL,
                  tp_name char(200) NOT NULL,
                  tp_id char(20),
                  tp_description text,
                  tp_status int(11),
                  tp_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  tp_used int(11) DEFAULT 0,
                  tp_knowledge int(11) NOT NULL
                )";
        $wpdb -> query($sqlMembros);

        /* repositorios */
        $sqlMembros = "CREATE TABLE IF NOT EXISTS " . DMP_TABLE_TEMPLAT . "_repository (
                id_rp serial NOT NULL,
                  rp_name char(200) NOT NULL,
                  rp_url char(20),
                  rp_description text,
                  rp_status int(11),
                  rp_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  rp_knowledge int(11)
                )";
        $wpdb -> query($sqlMembros);

        $sqlMembros = "CREATE TABLE IF NOT EXISTS " . DMP_TABLE_TEMPLAT . "_knowledge (
                id_kn serial NOT NULL,
                  kn_name char(200) NOT NULL,
                  kn_code char(12),
                  kn_description text,
                  kn_status int(11),
                  kn_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
                )";
        $wpdb -> query($sqlMembros);
        
        /* Plans */
        $sqlMembros = "CREATE TABLE IF NOT EXISTS " . DMP_TABLE_TEMPLAT . "_plans (
                id_pl serial NOT NULL,
                  pl_name char(200) NOT NULL,
                  pl_own int(11),
                  pl_proposal char(200),
                  pl_description text,
                  pl_status int(11),
                  pl_visibility int(11),
                  pl_created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  pl_knowledge int(11)
                )";
        $wpdb -> query($sqlMembros);
                
        return (1);
    }

    /*************************************** knowledge *************************/
    function knowledge_list() {
        global $data, $wpdb;
        
        $novo = '<span class="btn btn-default" id="templat_new" >' . msg('import_knowledge') . '</span>';
        echo '<h3>' . msg('knowledge_area') . ' ' . $novo . '</h3>';

        $sx .= '<table width="100%" class="table">' . cr();
        $sx .= '<tr>';
        $sx .= '<th width="2%">' . msg("#") . "</th>" . cr();
        $sx .= '<th width="88%">' . msg("knowledge") . "</th>" . cr();
        $sx .= '<th width="10%">' . msg("code") . "</th>" . cr();        
        $sx .= '</tr>' . cr();

        $sql = "select * from " . DMP_TABLE_TEMPLAT . "_knowledge where kn_status >= 1 order by kn_code ";
        $rlt = $wpdb -> get_results($wpdb -> prepare($sql, $some_parameter));
        $id = 1;
        foreach ($rlt as $rlt) {
            $sx .= '<tr style="font-size: 150%;">';
            $sx .= '<td>' . ($id++) . '</td>';
            $sx .= '<td>' . $rlt -> kn_name . '</td>';
            $sx .= '<td>' . $rlt -> kn_code . '</td>';
            $sx .= '</tr>' . cr();
        }
        $sx .= '<tr><td colspan="5">total '.$id.'</td></tr>';
        $sx .= '</table>' . cr();
        return ($sx);

    }

    /*************************************** templat ***************************/
    function le_templat($id='')
        {
        global $wpdb;
        $sql = "select * from " . DMP_TABLE_TEMPLAT . " where id_tp = $id ";
        $rlt = $wpdb -> get_results($wpdb -> prepare($sql, $some_parameter));
        if (count($rlt) > 0)
            {
                return($rlt[0]);        
            } else {
                return(array());
            }
          
        }
    function templat_list() {
        global $data, $wpdb;
        $act = $_GET['act'];
        $id = $_GET['id'];
        $data = array();
        $data['style'] = 'display: none;';
        
        switch($act)
            {
                case 'edit':
                    $link = '<a href="admin.php?page=dmp_admin_templat" class="btn btn-default">';
                    $novo = $link . msg('cancel') . '</a>';
                    echo '<h3>' . msg('plans_templat') . ' ' . $novo . '</h3>';
                    $rlt = $this->le_templat($id);
                    $data['id_tp'] = $rlt->id_tp;
                    $data['tp_id'] = $rlt->tp_id;
                    $data['tp_name'] = $rlt->tp_name;
                    $data['tp_descr'] = $rlt->tp_description;
                    $data['style'] = 'display: block;';
                    break;
                default:
                    $novo = '<span class="btn btn-default" id="templat_new" >' . msg('add_new') . '</span>';
                    echo '<h3>' . msg('plans_templat') . ' ' . $novo . '</h3>';                                   
            }

        /* form */        
        if ($_POST) {
            $data['id_tp'] = $id;
            $data['tp_id'] = $_POST['tp_id'];
            $data['tp_name'] = $_POST['tp_name'];
            $data['tp_descr'] = $_POST['tp_descr'];
            $data['action'] = $_POST['action'];
        }                            
        $data['save'] = msg('save') . ' >>>';
        
        $sx .= view("view/templat_form.php");
        
        /* SAVE */
        if ((strlen($data['tp_name']) > 0) and (strlen($data['tp_descr']) > 0) and (strlen($data['action']) > 0)) {
            $this -> templat_data($data);
            echo cr().'<meta http-equiv="refresh" content="0;URL=admin.php?page=dmp_admin_templat">' ;
            exit;
        }

        $sx .= '<table width="100%" class="table">' . cr();
        $sx .= '<tr>';
        $sx .= '<th width="2%">' . msg("#") . "</th>" . cr();
        $sx .= '<th width="2%">' . msg("id") . "</th>" . cr();
        $sx .= '<th width="36%">' . msg("name") . "</th>" . cr();
        $sx .= '<th width="43%">' . msg("desciption") . "</th>" . cr();
        $sx .= '<th width="15%">' . msg("created") . "</th>" . cr();
        $sx .= '<th width="2%">' . msg("plans") . "</th>" . cr();
        $sx .= '</tr>' . cr();

        $sql = "select * from " . DMP_TABLE_TEMPLAT . " where tp_status >= 1 order by tp_id ";
        $rlts = $wpdb -> get_results($wpdb -> prepare($sql, $some_parameter));
        $id = 1;
        foreach ($rlts as $rlt) {
            $link = '<a href="admin.php?page=dmp_admin_templat&id='.$rlt->id_tp.'&act=edit" style="font-size: 70%;" class="btn btn-default">';
            $link2 = '<a href="admin.php?page=dmp_admin_templat&action=list&id='.$rlt->id_tp.'">';
            $sx .= '<tr style="font-size: 150%; border-left: 5px #8080ff solid;">';
            $sx .= '<td>' . ($id++) . '</td>';
            $sx .= '<td>' . $rlt -> tp_id . '</td>';
            $sx .= '<td>' . $link2 . $rlt -> tp_name . '</a>' . '</td>';
            $sx .= '<td>' . $rlt -> tp_description . '</td>';
            $sx .= '<td>' . $rlt -> tp_created . '</td>';
            $sx .= '<td align="center">' . $rlt -> tp_used . '</td>';
            $sx .= '<td align="center">'.$link.'edit</a></td>';
            $sx .= '</tr>' . cr();
        }
        $sx .= '<tr><td colspan=5>total '.$id.' '.msg('templat').'</td></tr>';
        $sx .= '</table>' . cr();
        return ($sx);
    }

    function templat_data($data) {
        global $wpdb;
        $id = $data['id_tp'];
        $tp_id = $data['tp_id'];
        $tp_name = $data['tp_name'];
        $tp_description = $data['tp_descr'];
        $tp_status = $data['tp_status'];

        if (isset($data['id_tp'])) {
            $sql = "update " . DMP_TABLE_TEMPLAT . " set
								tp_name = '$tp_name',
								tp_id = '$tp_id',
								tp_description = '$tp_description'
							where id_tp = ".$data['id_tp'];
            echo '<br><br><center><h2>Saving...</h2><br><br>';                            
            $wpdb -> query($sql);
        } else {
            $sql = "insert into " . DMP_TABLE_TEMPLAT . " 
								(tp_name, tp_id, tp_description, tp_status, tp_used)
							values
								('$tp_name','$tp_id','$tp_description', 1, 0) ";
            echo '<br><br><center><h2>Saving...</h2><br><br>';                                
            $wpdb -> query($sql);
        }
        return(1);
    }

    function cab($sub = '') {
        $img = DMP_DIR . 'img/icone_dmp_rnp.png';

        $img_logo = '<img src="' . get_site_url() . $img . '" class="img-thumbnail" style="height: 90px;" align="right">';
        $title = '<font style="background-color: yellow; padding: 0px 5px 0px 5px;" color="#8080ff">RDP</font><font color="green">Brasil</font>';
        $title .= ' - DMP';
        if (strlen($sub) > 0) {
            $title .= ' - ' . $sub;
        }

        $sx = '<div class="wrap">';
        $sx .= $img_logo;
        $sx .= '<h1 class="wp-heading-inline">' . $title . '</h1>';
        $sx .= '</div>';

        //$sx .= '<a href="http://localhost/projeto/RDP-Brasil/wp-admin/plugin-install.php" class="page-title-action">Adicionar novo</a>';
        return ($sx);
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

    add_submenu_page('dmp_admin', 'DMP Templates', 'DMP Templates', 'manage_options', 'dmp_admin_templat', 'dmp_admin_templat', '1');
    add_submenu_page('dmp_admin', 'Knowledge ', 'Knowledge Area', 'manage_options', 'dmp_admin_group_members', 'dmp_admin_knowledge', '2');
}

add_action('admin_menu', 'dmprdp_register_my_custom_menu_page2');

function dmp_admin_home() {
    $c = new dmp;
    $sx = $c -> cab('home');
    echo $sx;
}

/*************** templat *****************************/
function dmp_admin_templat() {
    global $data,$wpdb;
    $c = new dmp;
    $page = $_GET['action'];
    $id = $_GET['id'];
    echo $c -> cab('templat');

    switch ($page)
        {
        case 'list':
            $data = $c -> le_templat($id);
            $data = get_object_vars($data);
            
            view('view/templat_show.php');
            $link = '<a href="admin.php?page=dmp_admin_templat" class="btn btn-default">';
            $novo = $link . msg('return') . '</a>';
            echo $novo;
                        
            break;
        default:
            $sx .= $c -> templat_list();
            break;            
        }
    echo $sx;
}

function dmp_admin_knowledge() {
    $c = new dmp;
    
    echo $c -> cab('templat');
    
    switch ($page)
        {
        case 'list':
            $sx = "<h1>Hello</h1>'";
            break;
        default:
            $sx = $c -> knowledge_list();
            break;            
        }
    echo $sx;
}

//[foobar]
function dmp_login( $atts ){
    global $data,$wpdb;
    $sx = '<!-- BEGIN # BOOTSNIP INFO -->
            <h3 class="text-left">DMP-Login</h3>
            <p class="text-left"><a href="#" class="btn btn-primary" role="button" data-toggle="modal" data-target="#login-modal">Open Login Modal</a></p>
            <!-- END # BOOTSNIP INFO -->
            
            <!-- BEGIN # MODAL LOGIN -->
            <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" align="center">
                                <img class="img-responsive" width="30%" id="img_logo" src="'.home_url().'/wp-content/themes/wp-rdp-brasil/img/logos_brasil_blue.png" heigth="30">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                </button>
                            </div>
                            
                            <!-- Begin # DIV Form -->
                            <div id="div-forms">
                            
                                <!-- Begin # Login Form -->
                                <form id="login-form">
                                    <div class="modal-body">
                                        <div id="div-login-msg">
                                            <span id="text-login-msg">Enter your e-mail</span>
                                            <br>
                                            <input id="login_username" class="form-control" type="text" placeholder="Username (type ERROR for error effect)" required>
                                            <br>
                                            <span id="text-login-msg">Enter your password</span>
                                            <br>
                                            <input id="login_password" class="form-control" type="password" placeholder="Password" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                                        </div>
                                        <div>
                                            <button id="login_lost_btn" type="button" class="btn btn-link">Lost Password?</button>
                                            <button id="login_register_btn" type="button" class="btn btn-link">Register</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- End # Login Form -->
                                
                                <!-- Begin | Lost Password Form -->
                                <form id="lost-form" style="display:none;">
                                    <div class="modal-body">
                                        <div id="div-lost-msg">
                                            <div id="icon-lost-msg" class="glyphicon glyphicon-chevron-right"></div>
                                            <span id="text-lost-msg">Type your e-mail.</span>
                                        </div>
                                        <input id="lost_email" class="form-control" type="text" placeholder="E-Mail (type ERROR for error effect)" required>
                                    </div>
                                    <div class="modal-footer">
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">Send</button>
                                        </div>
                                        <div>
                                            <button id="lost_login_btn" type="button" class="btn btn-link">Log In</button>
                                            <button id="lost_register_btn" type="button" class="btn btn-link">Register</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- End | Lost Password Form -->
                                
                                <!-- Begin | Register Form -->
                                <form id="register-form" style="display:none;">
                                    <div class="modal-body">
                                        <div id="div-register-msg">
                                            <div id="icon-register-msg" class="glyphicon glyphicon-chevron-right"></div>
                                            <span id="text-register-msg">Register an account.</span>
                                        </div>
                                        <input id="register_username" class="form-control" type="text" placeholder="Username (type ERROR for error effect)" required>
                                        <input id="register_email" class="form-control" type="text" placeholder="E-Mail" required>
                                        <input id="register_password" class="form-control" type="password" placeholder="Password" required>
                                    </div>
                                    <div class="modal-footer">
                                        <div>
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                                        </div>
                                        <div>
                                            <button id="register_login_btn" type="button" class="btn btn-link">Log In</button>
                                            <button id="register_lost_btn" type="button" class="btn btn-link">Lost Password?</button>
                                        </div>
                                    </div>
                                </form>
                                <!-- End | Register Form -->
                                
                            </div>
                            <!-- End # DIV Form -->
                            
                        </div>
                    </div>
                </div>
                <!-- END # MODAL LOGIN -->';
    //$sx .= $wpdb->wp_get_current_user();
    return $sx;
}
add_shortcode( 'dmp_login', 'dmp_login' );

function msg($t) {
    return ($t);
}

function view($f) {
    //$f = DMP_DIR . $f;
    $dir = $_SERVER['SCRIPT_NAME'];
    $dir = substr($dir, 0, strpos($dir, '/admin'));
    $dir .= '/../' . DMP_DIR;
    $f = $dir . $f;
    if (file_exists($f)) {
        require ($f);
        return ("");
    } else {
        return ("ERRO VIEW");
    }
}

function cr() {
    return (chr(13) . chr(10));
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
