<?php
/**
 *
 * @link              http://www.ysgdesign.com/
 * @since             1.0.0
 * @package           ColumnPro
 *
 * @wordpress-plugin
 * Plugin Name:       ColumnPro
 * Plugin URI:        http://www.ysgdesign.com/
 * Description:       Add a column for any meta data to any post type. Sort by it or filter by it.
 * Version:           2.0.0
 * Author:            Yair Gelb
 * Author URI:        http://www.ysgdesign.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

/*  Copyright 2016 yair gelb
*/

class yg_columnpro {
    /**
     * @access   protected
     * @var      string    $plugin_name
     */
    protected $plugin_name;

    /**
     * @access   protected
     * @var      string    $version
     */
    protected $version;

    /**
     * Array to store meta columns
     * @access   protected
     * @var      array    $options
     */
    protected $options;

    /**
     * Array to store taxonomy columns
     * @access   protected
     * @var      array    $taxoptions
     */
    protected $taxoptions;

    /**
     * Array to store removed columns
     * @access   protected
     * @var      array    $removeoptions
     */
    protected $removeoptions;

    /**
     * Array to store all columns
     * @access   protected
     * @var      array    $alloptions
     */
    protected $alloptions;

    /**
     * Array to store cache options
     * @access   protected
     * @var      array    $cacheoptions
     */
    protected $cacheoptions;

    /**
     * Array to store cache options
     * @access   protected
     * @var      string    $showcol
     */
    protected $showcol;

    /**
     * Array to store duration values
     * @access   protected
     * @var      array    $post_types
     */
    protected $post_types;

    public function __construct() {
        global $wpdb;
        $this->plugin_name = 'ColumnPro';
        $this->version = '2.0.0';
        $this->cacheoptions = get_option('yg_filter_cache');
        $this->showcol = get_option('yg_filt_show_col_name');
        $this->post_types = array();
        $ptype_excld = array('attachment','revision','nav_menu_item');

        if(get_option('yg_filter_version')<$this->version){
            $this->options = get_option('yg_filter_any_options');
            $this->taxoptions = get_option('yg_filter_any_tax_options');
            $mergedArray = array();
            foreach ($this->options as $pstype => $value) {
                foreach ($value as $metaname => $args) {
                    $value[$metaname]['t']='meta';
                }
                $mergedArray[$pstype]=$value;
            }
            foreach ($this->taxoptions as $pstype => $value) {
                foreach ($value as $metaname => $args) {
                    $value[$metaname]['t']='tax';
                }
                if(isset($mergedArray[$pstype]))
                    $mergedArray[$pstype]=array_merge($mergedArray[$pstype],$value);
                else
                    $mergedArray[$pstype]=$value;
            }
            add_option('yg_filter_all_options',$mergedArray);
            update_option('yg_filter_version',$this->version);
            delete_option('yg_filter_any_options');
            delete_option('yg_filter_any_tax_options');
            $this->options = array();
            $this->taxoptions = array();
            $this->removeoptions = array();
        }
        $this->alloptions = get_option('yg_filter_all_options');
        foreach ($this->alloptions as $pt => $term) {
            foreach ($term as $field => $params) {
                if($params['t']=='meta')
                    $this->options[$pt][$field] = $params;
                elseif($params['t']=='tax')
                    $this->taxoptions[$pt][$field] = $params;
                elseif($params['t']=='rmv')
                    $this->removeoptions[$pt][$field] = $params;
            }
        }

        /*foreach ($ptype_excld as $ptype) {
            if(isset($this->post_types[$ptype]))
                unset($this->post_types[$ptype]);
        }*/
        if (is_admin()) {
            add_action('admin_init',array(&$this, 'yg_filter_get_post_types'));
            add_action('admin_menu',array(&$this, 'yg_filter_add_plugin_page'));
            add_action('admin_init',array(&$this, 'yg_filt_page_init'));
            add_action('admin_print_styles-edit.php', array(&$this,'yg_fltr_post_list_css'));
            add_action('wp_ajax_yg_fltr_get_meta', array(&$this,'yg_fltr_get_meta'));
            add_action('wp_ajax_yg_fltr_get_tax', array(&$this,'yg_fltr_get_tax'));
            add_action('wp_ajax_yg_flt_clr_cache', array(&$this,'yg_flt_clr_cache'));
            add_action('wp_ajax_yg_flt_update_mn_oc', array(&$this,'yg_flt_update_mn_oc'));

            add_action('pre_get_posts',array(&$this,'yg_flt_clm_orderby'));
            add_filter('posts_clauses',array(&$this,'orderby_tax_clauses'), 10, 2 );
            add_action('restrict_manage_posts',array(&$this,'yg_flt_list_dd'));
            add_filter('parse_query',array(&$this,'yg_flt_add_dd_filter'));
        }
    }
    /**
     * Get post types that show in admin
     */
    public function yg_filter_get_post_types(){
        $this->post_types = get_post_types(array('show_ui'=>true),'names' );
        if(is_array($this->options)){
            foreach ($this->options as $pt => $value){
                add_filter('manage_edit-'.$pt.'_columns', array(&$this,'yg_flt_add_clm'));
                add_action('manage_'.$pt.'_posts_custom_column', array(&$this,'yg_flt_add_clm_data'),10,2);
                add_filter('manage_edit-'.$pt.'_sortable_columns', array(&$this,'yg_flt_sortable_clm'));
            }
        }
        if($this->showcol=='yes'){
            foreach ($this->post_types as $pt){
                add_filter('manage_edit-'.$pt.'_columns', array(&$this,'yg_flt_show_clm_names'));
            }
        }
    }
    /**
     * Add options page
     */
    public function yg_filter_add_plugin_page(){
        // This page will be under "Settings"
        add_options_page(
            'ColumnPro settings',
            'ColumnPro settings',
            'manage_options',
            'yg_filter_settings',
            array( $this, 'create_yg_filter_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_yg_filter_admin_page(){
        //get_user_meta($user_id, $key, $single);get_user_meta( $user_id, $meta_key, $meta_value, $unique );
        $user_id = get_current_user_id();
        $mnOpenClose = get_user_meta($user_id,'yg_fltr_menu_oc',true);
        wp_register_style('yg_filt_admin_css', plugin_dir_url( __FILE__ ) . 'css/yg_filter_admin.min.css');
        wp_enqueue_style('yg_filt_admin_css');
        wp_register_script('yg_filt_admin_js', plugin_dir_url( __FILE__ ) . 'js/yg_filt_admin.min.js', array('jquery'));
        wp_localize_script('yg_filt_admin_js','columnpro',array('post_types'=>$this->post_types,'uid'=>$user_id,'wpnonce'=>wp_create_nonce('filter-update-oc9630')));
        wp_enqueue_script('yg_filt_admin_js');
        wp_enqueue_script('jquery-ui-sortable', array('jquery'));
        $i = 0; 
        $ocInd = 0; ?>
        <div class="wrap filter-wrap">
            <h2 class="filt-main-ttl"><div class="filt-logo"></div>ColumnPro settings</h2>
            <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]=='c');$ocInd++; ?>
            <h3 class="green sld-slct<?php if($closed) echo ' close'; ?>">Metadata columns</h3>
            <form method="post" action="options.php" class="sld-div<?php if($closed) echo ' closed'; ?>">
                <?php if(is_array($this->alloptions)){
                    foreach ($this->alloptions as $key => $value) { ?>
                <div class="pt-wrap">
                    <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]=='c'); ?>
                    <p class="pt-ttl sld-slct<?php if($closed) echo ' close'; ?>"><strong><?php echo $key ?></strong> columns</p>
                    <div class="pt-val-div sld-div<?php if($closed) echo ' closed'; ?>">
                <?php foreach ($value as $meta => $valarray) { 
                    if($valarray['t']!='rmv'){ ?>
                    <div class="pt-val" flt_nonce="<?php echo wp_create_nonce('filter-get-meta9630') ?>">
                        <p class="pt-val-ttl"><strong><span class="clm-hdr-name"><?php echo $valarray['name'] ?></span></strong> lists <strong><?php if(isset($valarray['field'])) echo $valarray['field'] ?></strong> values</p>
                        <input type="hidden" id="yg_pop_filter_ptype<?php echo $i ?>" name="yg_filt_type[<?php echo $i ?>]" value="<?php echo $key ?>" />
                        <input type="hidden" id="yg_pop_filter_meta<?php echo $i ?>" name="yg_filt_meta[<?php echo $i ?>]" value="<?php if(isset($valarray['field'])) echo $valarray['field'] ?>" />
                        <input type="hidden" id="yg_pop_filter_name<?php echo $i ?>" name="yg_filt_name[<?php echo $i ?>]" value="<?php if(isset($valarray['name'])) echo $valarray['name'] ?>" />
                        <input type="hidden" id="yg_pop_filter_sort<?php echo $i ?>" name="yg_filt_sort[<?php echo $i ?>]" value="<?php if(isset($valarray['sort'])) echo $valarray['sort'] ?>" />
                        <input type="hidden" id="yg_pop_filter_sort_num<?php echo $i ?>" name="yg_filt_sort_num[<?php echo $i ?>]" value="<?php if(isset($valarray['sortnum'])) echo $valarray['sortnum'] ?>" />
                        <input type="hidden" id="yg_pop_filter_dd<?php echo $i ?>" name="yg_filt_dd[<?php echo $i ?>]" value="<?php if(isset($valarray['dd'])) echo $valarray['dd'] ?>" />
                        <input type="hidden" id="yg_pop_filter_msdd<?php echo $i ?>" name="yg_filt_msdd[<?php echo $i ?>]" value="<?php if(isset($valarray['msdd'])) echo $valarray['msdd'] ?>" />
                        <input type="hidden" id="yg_pop_filter_t<?php echo $i ?>" name="yg_filt_t[<?php echo $i ?>]" value="<?php if(isset($valarray['t'])) echo $valarray['t'] ?>" />
                        <div class="pt-val-edit<?php echo (isset($valarray['t']) && $valarray['t']=='tax')?' taxval':' metaval'; ?>" div-int="<?php echo $i ?>"></div>
                        <div class="pt-val-delete"></div>
                    </div>
                <?php }else{ ?>
                    <div class="pt-val" flt_nonce="<?php echo wp_create_nonce('filter-get-meta9630') ?>">
                        <input type="hidden" id="yg_pop_filter_ptype<?php echo $i ?>" name="yg_filt_type[<?php echo $i ?>]" value="<?php echo $key ?>" />
                        Remove <input type="text" id="yg_pop_filter_name<?php echo $i ?>" name="yg_filt_name[<?php echo $i ?>]" value="<?php if(isset($valarray['name'])) echo $valarray['name'] ?>" />
                        <input type="hidden" id="yg_pop_filter_t<?php echo $i ?>" name="yg_filt_t[<?php echo $i ?>]" value="<?php if(isset($valarray['t'])) echo $valarray['t'] ?>" />
                        <div class="pt-val-delete"></div>
                    </div>
                <?php }
                $i++;
                    } ?>
                    </div>
                </div>
                <?php 
                    $ocInd++;
                    }
                } ?>
                <div style="clear:both"></div>
            <?php settings_fields( 'yg_filt_options' ); ?>
                <div class="select_clm" flt_nonce="<?php echo wp_create_nonce('filter-get-meta9630') ?>">
                     <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]!='o' || !isset($mnOpenClose[$ocInd]));$ocInd++; ?>
                    <p class="pt-ttl sld-slct<?php if($closed) echo ' close'; ?>"><strong>Add meta column</strong></p>
                    <div class="select_clm_inner sld-div<?php if($closed) echo ' closed'; ?>">
                        <div class="filt_row">
                        <label for="yg_pop_filter_ptype<?php echo $i ?>">Choose post type:</label>
                        <select id="yg_pop_filter_ptype<?php echo $i ?>" name="yg_filt_type[<?php echo $i ?>]" class="select_pt">
                            <option value="">Select</option>
                <?php
                    foreach ($this->post_types as $typ) {
                        echo '<option value="'.$typ.'">'.$typ.'</option>';
                    }
                ?>
                        </select>
                        </div>
                        <div class="filt_row">
                            <label for="yg_pop_filter_meta<?php echo $i ?>">Choose meta key:</label>
                            <select id="yg_pop_filter_meta<?php echo $i ?>" name="yg_filt_meta[<?php echo $i ?>]" class="select_mt">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="filt_row">
                            <label for="yg_pop_filter_name<?php echo $i ?>">Column name:</label>
                            <input type="text" id="yg_pop_filter_name<?php echo $i ?>" name="yg_filt_name[<?php echo $i ?>]" value="" />
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_sort<?php echo $i ?>" name="yg_filt_sort[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to make column sortable</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_sort_num<?php echo $i ?>" name="yg_filt_sort_num[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to sort numerically otherwise will sort alphabetically</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_dd<?php echo $i ?>" name="yg_filt_dd[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to create a dropdown for filtering on values</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_msdd<?php echo $i ?>" name="yg_filt_msdd[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box for multi-select dropdown</label>
                        </div>
                        <input type="hidden" id="yg_pop_filter_t<?php echo $i ?>" name="yg_filt_t[<?php echo $i ?>]" value="meta" />
                    </div>
                </div>
                <?php $i++;?>
                <div class="select_clm" flt_nonce="<?php echo wp_create_nonce('filter-get-meta9630') ?>">
                    <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]!='o' || !isset($mnOpenClose[$ocInd]));$ocInd++; ?>
                    <p class="pt-ttl orange sld-slct<?php if($closed) echo ' close'; ?>"><strong>Add taxonomy column</strong></p>
                    <div class="select_clm_inner sld-div<?php if($closed) echo ' closed'; ?>">
                        <div class="filt_row">
                        <label for="yg_pop_filter_ptype<?php echo $i ?>">Choose post type:</label>
                        <select id="yg_pop_filter_ptype<?php echo $i ?>" name="yg_filt_type[<?php echo $i ?>]" class="select_pt_t">
                            <option value="">Select</option>
                <?php
                    foreach ($this->post_types as $typ) {
                        echo '<option value="'.$typ.'">'.$typ.'</option>';
                    }
                ?>
                        </select>
                        </div>
                        <div class="filt_row">
                            <label for="yg_pop_filter_tax<?php echo $i ?>">Choose taxonomy:</label>
                            <select id="yg_pop_filter_tax<?php echo $i ?>" name="yg_filt_meta[<?php echo $i ?>]" class="select_mt_t">
                                <option value="">Select</option>
                            </select>
                        </div>
                        <div class="filt_row">
                            <label for="yg_pop_filter_name<?php echo $i ?>">Column name:</label>
                            <input type="text" id="yg_pop_filter_name<?php echo $i ?>" name="yg_filt_name[<?php echo $i ?>]" value="" />
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_sort<?php echo $i ?>" name="yg_filt_sort[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to make column sortable</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_sort_num<?php echo $i ?>" name="yg_filt_sort_num[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to sort numerically otherwise will sort alphabetically</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_dd<?php echo $i ?>" name="yg_filt_dd[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box to create a dropdown for filtering on values</label>
                        </div>
                        <div class="filt_row">
                            <input type="checkbox" id="yg_pop_filter_msdd<?php echo $i ?>" name="yg_filt_msdd[<?php echo $i ?>]" value="1" />
                            <label class="filt_cb">Check this box for multi-select dropdown</label>
                        </div>
                        <input type="hidden" id="yg_pop_filter_t<?php echo $i ?>" name="yg_filt_t[<?php echo $i ?>]" value="tax" />
                    </div>
                </div>
                <?php $i++; ?>
                <div class="select_clm" flt_nonce="<?php echo wp_create_nonce('filter-get-meta9630') ?>">
                    <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]!='o' || !isset($mnOpenClose[$ocInd]));$ocInd++; ?>
                    <p class="pt-ttl red sld-slct<?php if($closed) echo ' close'; ?>"><strong>Remove column</strong></p>
                    <div class="select_clm_inner sld-div<?php if($closed) echo ' closed'; ?>">
                        <div class="filt_row">
                        <label for="yg_pop_filter_ptype<?php echo $i ?>">Choose post type:</label>
                        <select id="yg_pop_filter_ptype<?php echo $i ?>" name="yg_filt_type[<?php echo $i ?>]" class="select_pt_rm">
                            <option value="">Select</option>
                <?php
                    foreach ($this->post_types as $typ) {
                        echo '<option value="'.$typ.'">'.$typ.'</option>';
                    }
                ?>
                        </select>
                        </div>
                        <div class="filt_row">
                            <label for="yg_pop_filter_name<?php echo $i ?>">Column name:</label>
                            <input type="text" id="yg_pop_filter_name<?php echo $i ?>" name="yg_filt_name[<?php echo $i ?>]" value="" />
                        </div>
                        <p>The column name must be the columns ID. You can show column names whith the option "Show column names" below</p>
                        <input type="hidden" id="yg_pop_filter_t<?php echo $i ?>" name="yg_filt_t[<?php echo $i ?>]" value="rmv" />
                    </div>
                </div>
                <div style="clear:both"></div>
            <?php $i++;
                submit_button(); ?>
            </form>
            <?php $closed = (isset($mnOpenClose[$ocInd]) && $mnOpenClose[$ocInd]=='c');$ocInd++; ?>
            <h3 class="orng sld-slct<?php if($closed) echo ' close'; ?>">Show column names</h3>
            <form method="post" action="options.php" class="opt-form2">
                <p>If the site has many posts with many meat data attributes and taxonomy terms you can improve performance by caching results.<br />
                Leave empty or set to 0 for no caching.</p>
                <?php settings_fields( 'yg_filt_show_col' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">Show column names for hiding</th>
                            <td><input type="checkbox" id="yg_filter_cache" name="yg_filt_show_col_name" value="yes"<?php echo ($this->showcol=='yes')?' checked="checked"':'' ?> /></td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
            <h3 class="yellow">Cache settings</h3>
            <form method="post" action="options.php" class="opt-form2">
                <p>If the site has many posts with many meat data attributes and taxonomy terms you can improve performance by caching results.<br />
                Leave empty or set to 0 for no caching.</p>
                <?php settings_fields( 'yg_filt_cache' ); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">Set cache duration in minutes</th>
                            <td><input type="number" class="tiny-text" id="yg_filter_cache" name="yg_filter_cache[cachetime]" value="<?php echo intval($this->cacheoptions['cachetime']) ?>" min="0" step="1"></td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>
            <p>If you want to bust, remove all term and meta data cache, click below</p>
            <p><a href="" class="button button-primary" id="clr_fltr_cache" pop_nonce="<?php echo wp_create_nonce('filter-cache4286') ?>">Cleare cache</a></p>
        </div>
    <?php
    }

    /**
     * Register and add settings
     */
    public function yg_filt_page_init(){        
        register_setting(
            'yg_filt_options',
            'yg_filter_all_options',
            array($this,'yg_filt_sanitize')
        );

        register_setting(
            'yg_filt_show_col',
            'yg_filt_show_col_name',
            array($this,'yg_filt_show_col_sanitize')
        );

        register_setting(
            'yg_filt_cache',
            'yg_filter_cache',
            array($this,'yg_filt_cache_sanitize')
        );
    }

    /**
     * Sanitize duration options
     */
    public function yg_filt_sanitize($input){
        $new_input = array();
        foreach ($_REQUEST['yg_filt_type'] as $key => $value){
            if($value!='' && in_array($value,$this->post_types) && $_REQUEST['yg_filt_meta'][$key]!=''){
                $name = sanitize_text_field($_REQUEST['yg_filt_name'][$key]);
                $meta = sanitize_text_field($_REQUEST['yg_filt_meta'][$key]);
                $new_input[$value][$meta] = array('name'=>$name,'field'=>$meta);
                if(isset($_REQUEST['yg_filt_sort'][$key]) && $_REQUEST['yg_filt_sort'][$key]==1)
                    $new_input[$value][$meta]['sort'] = 1;
                if(isset($_REQUEST['yg_filt_sort_num'][$key]) && $_REQUEST['yg_filt_sort_num'][$key]==1)
                    $new_input[$value][$meta]['sortnum'] = 1;
                if(isset($_REQUEST['yg_filt_dd'][$key]) && $_REQUEST['yg_filt_dd'][$key]==1)
                    $new_input[$value][$meta]['dd'] = 1;
                if(isset($_REQUEST['yg_filt_msdd'][$key]) && $_REQUEST['yg_filt_msdd'][$key]==1)
                    $new_input[$value][$meta]['msdd'] = 1;

                $new_input[$value][$meta]['t'] = ($_REQUEST['yg_filt_t'][$key]=='tax')?'tax':'meta';
            }elseif($value!='' && in_array($value,$this->post_types) && $_REQUEST['yg_filt_t'][$key]=='rmv'){
                $name = sanitize_text_field($_REQUEST['yg_filt_name'][$key]);
                $new_input[$value]['r-'.$name] = array('name'=>$name);
                $new_input[$value]['r-'.$name]['t'] = 'rmv';
            }
        }
        return $new_input;
    }

    public function yg_filt_show_col_sanitize($input){
        if( isset( $_REQUEST['yg_filt_show_col_name'] ) && $_REQUEST['yg_filt_show_col_name']=='yes' )
            $new_input = 'yes';
        return $new_input;
    }

    public function yg_filt_cache_sanitize($input){
        $new_input = array();
        if( isset( $input['cachetime'] ) )
            $new_input['cachetime'] = absint( $input['cachetime'] );
        return $new_input;
    }

    public function yg_fltr_post_list_css(){
        wp_register_style('yg_filt_admin_tbl_css', plugin_dir_url( __FILE__ ) . 'css/yg_filter_admin_tbl.min.css');
        wp_enqueue_style('yg_filt_admin_tbl_css');
        wp_register_style('yg_filt_sumo_css', plugin_dir_url( __FILE__ ) . 'css/sumoselect.min.css');
        wp_enqueue_style('yg_filt_sumo_css');
        wp_register_script('yg_filt_sumo_js', plugin_dir_url( __FILE__ ) . 'js/jquery.sumoselect.min.js', array('jquery'));
        wp_enqueue_script('yg_filt_sumo_js');
        wp_register_script('yg_filt_admin_tbl_js', plugin_dir_url( __FILE__ ) . 'js/yg_filt_admin_tbl.min.js', array('jquery'));
        wp_enqueue_script('yg_filt_admin_tbl_js');
    }
    /**
     * Column functions
     */
    public function yg_flt_show_clm_names($columns){
        foreach ($columns as $key => $value) {
            $columns[$key] = $value.'<div class="colName">'.$key.'</div>';
        }
        return $columns;
    }

    public function yg_flt_add_clm($columns){
        global $wp_query;
        $pt = $wp_query->query['post_type'];
        $new_columns = array();
        $hasBothIdThumb = (array_key_exists('thumbnail',$this->alloptions[$pt]) && array_key_exists('post_id',$this->alloptions[$pt]))?2:1;
        if(isset($this->alloptions[$pt]) && is_array($this->alloptions[$pt])){
            foreach ($this->alloptions[$pt] as $meta => $valarray){
                if($meta=='thumbnail')
                    $columns = array_slice($columns,0, $hasBothIdThumb,true) + array('thumbnail' => $valarray['name']) + array_slice($columns,$hasBothIdThumb,NULL,true);
                if($meta=='post_id')
                    $columns = array_slice($columns,0, 1,true) + array('post_id' => $valarray['name']) + array_slice($columns,1,NULL,true);
                if($valarray['t']!='rmv')
                    $new_columns[$meta] = $valarray['name'];
            }
        }

        if(isset($this->removeoptions[$pt]) && is_array($this->removeoptions[$pt])){
            foreach ($this->removeoptions[$pt] as $col => $valarray){
                if(isset($columns[$valarray['name']]))
                    unset($columns[$valarray['name']]);
            }
        }
        /*if(isset($this->taxoptions[$pt]) && is_array($this->taxoptions[$pt])){
            foreach ($this->taxoptions[$pt] as $tax => $valarray){
                $new_columns[$tax] = __($valarray['name'], '');
            }
        }*/
        return array_merge($columns,$new_columns);
    }

    public function yg_flt_add_clm_data($column,$post_id){
        $p = get_post($post_id);
        $pt = $p->post_type;
        if(isset($this->options[$pt]) && is_array($this->options[$pt])){
            foreach ($this->options[$pt] as $meta => $valarray){
                if($column=='thumbnail' && $meta=='thumbnail')
                    echo get_the_post_thumbnail($post_id,array(80,80));
                elseif($column=='post_id' && $meta=='post_id')
                    echo $post_id;
                elseif($column=='author' && $meta=='author')
                    echo get_post_field('post_author',$post_id);
                elseif($column==$meta){
                    $metaCol = get_post_meta($post_id,$meta);
                    echo (is_array($metaCol))?implode(', ', $metaCol):$metaCol;
                }
            }
        }
        if(isset($this->taxoptions[$pt]) && is_array($this->taxoptions[$pt])){
            foreach ($this->taxoptions[$pt] as $tax => $valarray){
                if($column==$tax){
                    $clmTrms = wp_get_post_terms($post_id,$tax,array('fields'=>'names'));
                    echo implode(', ',$clmTrms);
                }
            }
        }
    }

    public function yg_flt_sortable_clm($columns){
        global $wp_query;
        $pt = $wp_query->query['post_type'];
        $a = array();
        if(isset($this->options[$pt]) && is_array($this->options[$pt])){
            foreach ($this->options[$pt] as $meta => $valarray){
                if(isset($valarray['sort']) && $valarray['sort']==1)
                    $columns[$meta] = $meta;
            }
        }
        if(isset($this->taxoptions[$pt]) && is_array($this->taxoptions[$pt])){
            foreach ($this->taxoptions[$pt] as $tax => $valarray){
                if(isset($valarray['sort']) && $valarray['sort']==1)
                    $columns[$tax] = $tax;
            }
        }
        return $columns;
    }

    public function yg_flt_clm_orderby(){
        global $wp_query;
        if(!is_admin())
            return;
        $pt = $wp_query->query['post_type'];
        $orderby = $wp_query->get('orderby');
        if(isset($this->options[$pt]) && is_array($this->options[$pt])){
            if(isset($this->options[$pt][$orderby])){
                $alphNum = (isset($this->options[$pt][$orderby]['sortnum']) && $this->options[$pt][$orderby]['sortnum']==1)?'meta_value_num':'meta_value';
                if($orderby=='author'){
                    $wp_query->set('orderby','author');
                }elseif($orderby=='post_id'){
                    $wp_query->set('orderby','ID');
                }else{
                    $wp_query->set('meta_key',$orderby);
                    $wp_query->set('orderby',$alphNum);
                }
            }
        }
    }

    public function orderby_tax_clauses($clauses,$wp_query){
        global $wpdb;
        $pt = $wp_query->query['post_type'];
        $taxonomies = $this->yg_filt_get_tax_pt($pt);
        if(is_array($taxonomies) && isset($wp_query->query['orderby']) && in_array($wp_query->query['orderby'],$taxonomies)){
            foreach($taxonomies as $taxonomy){
                if(isset($wp_query->query['orderby']) && $taxonomy == $wp_query->query['orderby']){
                    $clauses['join'] .="LEFT OUTER JOIN {$wpdb->term_relationships} `trcp` ON {$wpdb->posts}.ID=trcp.object_id
                    LEFT OUTER JOIN {$wpdb->term_taxonomy} `ttcp` ON trcp.term_taxonomy_id=ttcp.term_taxonomy_id
                    LEFT OUTER JOIN {$wpdb->terms} ON ttcp.term_id={$wpdb->terms}.term_id";
                    $clauses['where'] .= " AND (taxonomy = '{$taxonomy}' OR taxonomy IS NULL)";
                    $clauses['groupby'] = "trcp.object_id";
                    $clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC) ";
                    $clauses['orderby'] .= ( 'ASC' == strtoupper( $wp_query->get('order') ) ) ? 'ASC' : 'DESC';
                }
            }
        }
        return $clauses;
    }

    public function yg_flt_list_dd(){
        global $wp_query;
        $pt = $wp_query->query['post_type'];
        if($GLOBALS['pagenow'] === 'upload.php'){
            return;
        }
        if(isset($this->options[$pt]) && is_array($this->options[$pt])){
            foreach ($this->options[$pt] as $meta => $valarray){
                if(isset($valarray['dd']) && $valarray['dd']==1){
                $values = $this->yg_filt_get_meta_vals($pt,$meta);
        ?>
        <select name="yg_filt_<?php echo $pt.'_'.$meta ?><?php if(isset($valarray['msdd']) && $valarray['msdd']==1) echo '[]'; ?>"<?php if(isset($valarray['msdd']) && $valarray['msdd']==1) echo ' multiple="multiple" class="sumo"'; ?>>
        <option value=""><?php echo 'All '.$meta.'s' ?></option>
        <?php
            $curr = isset($_GET['yg_filt_'.$pt.'_'.$meta])? $_GET['yg_filt_'.$pt.'_'.$meta]:'';
            foreach ($values as $val) {
                $val2 = ($meta=='author')?get_the_author_meta('display_name',$val):$val;
                printf(
                    '<option value="%s"%s>%s</option>',
                    $val,
                    ($val == $curr || (is_array($curr) && in_array($val,$curr)))?' selected="selected"':'',
                    $val2
                );
            }
        ?>
        </select>
        <?php
                }
            }
        }
        if(isset($this->taxoptions[$pt]) && is_array($this->taxoptions[$pt])){
            foreach ($this->taxoptions[$pt] as $tax => $valarray){
                if(isset($valarray['dd']) && $valarray['dd']==1){
                $values = get_terms($tax);
        ?>
        <select name="yg_filt_<?php echo $pt.'_'.$tax ?><?php if(isset($valarray['msdd']) && $valarray['msdd']==1) echo '[]'; ?>"<?php if(isset($valarray['msdd']) && $valarray['msdd']==1) echo ' multiple="multiple" class="sumo"'; ?>">
        <option value=""><?php echo 'All '.$tax.'s' ?></option>
        <?php
            $curr = isset($_GET['yg_filt_'.$pt.'_'.$tax])? $_GET['yg_filt_'.$pt.'_'.$tax]:'';
            foreach ($values as $val) {
                printf(
                    '<option value="%s"%s>%s</option>',
                    $val->term_id,
                    ($val->term_id == $curr || (is_array($curr) && in_array($val->term_id,$curr)))?' selected="selected"':'',
                    $val->name
                );
            }
        ?>
        </select>
        <?php
                }
            }
        }
    }

    public function yg_flt_add_dd_filter(){
        global $pagenow,$wp_query;
        $pt = $wp_query->query['post_type'];
        $metaQry = array();
        if(isset($this->options[$pt]) && is_array($this->options[$pt])){
            foreach ($this->options[$pt] as $meta => $valarray){
                if (is_admin() && $pagenow=='edit.php' && isset($_GET['yg_filt_'.$pt.'_'.$meta]) && $_GET['yg_filt_'.$pt.'_'.$meta] != '') {
                    if($meta=='post_id'){
                        $wp_query->set('post__in',(array)$_GET['yg_filt_'.$pt.'_'.$meta]);
                    }elseif($meta=='author'){
                        $wp_query->set('author',implode(',',(array)$_GET['yg_filt_'.$pt.'_'.$meta]));
                    }else{
                        $metaQry[] = array(
                            'key' => $meta,
                            'value' => $_GET['yg_filt_'.$pt.'_'.$meta],
                            'compare' => 'in',
                        );
                    }
                }
            }
        }
        if(!empty($metaQry))
            $wp_query->set('meta_query',$metaQry);

        $texQry = array();
        if(isset($this->taxoptions[$pt]) && is_array($this->taxoptions[$pt])){
            foreach ($this->taxoptions[$pt] as $tax => $valarray){
                if (
                    is_admin() && $pagenow=='edit.php' && isset($_GET['yg_filt_'.$pt.'_'.$tax]) 
                    && 
                    (
                        (!is_array($_GET['yg_filt_'.$pt.'_'.$tax]) && $_GET['yg_filt_'.$pt.'_'.$tax] != '') || 
                        (is_array($_GET['yg_filt_'.$pt.'_'.$tax]) && !empty($_GET['yg_filt_'.$pt.'_'.$tax]) && $_GET['yg_filt_'.$pt.'_'.$tax][0]!='')
                    )
                ) {
                    if($tax=='category'){
                        $wp_query->set('cat',implode(',',$_GET['yg_filt_'.$pt.'_'.$tax]));
                    }else{
                        $texQry[] = array(
                            'taxonomy' => $tax,
                            'field' => 'term_id',
                            'terms' => $_GET['yg_filt_'.$pt.'_'.$tax]
                        );
                    }
                }
            }
        }
        if(!empty($texQry))
            $wp_query->set('tax_query',$texQry);
    }
    /**
     * Helper functions
     */

    /**
     * Gets distinct meta values for a certain post type and meta key
     *
     * @param string $pt post type.
     * @param string $meta meta key.
     * @param int $rows number of records to return.
     *
     */
    public function yg_filt_get_meta_vals($pt,$meta,$rows=false){
        global $wpdb;
        if(!is_admin() || empty($pt) || !in_array($pt,$this->post_types) || empty($meta))
            return;
        if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0 && $res = get_transient('yg_flt_tran'.$pt.'-'.$meta.'_meta_vals')){

        }else{
            if($meta=='author'){
                $q = "SELECT DISTINCT(u.ID) FROM $wpdb->posts `p`,$wpdb->users `u` WHERE u.ID=p.post_author AND 'author'='%s' AND p.post_status='publish' AND p.post_type = '%s' ORDER BY p.post_author DESC";
            }elseif($meta=='post_id'){
                $q = "SELECT DISTINCT(ID) FROM $wpdb->posts WHERE 'post_id'='%s' AND post_status='publish' AND post_type = '%s' ORDER BY ID DESC";
            }else{
                $q = "SELECT DISTINCT(m.meta_value) FROM $wpdb->postmeta `m`
                LEFT JOIN $wpdb->posts `p` ON p.ID = m.post_id
                WHERE m.meta_key = '%s'
                AND p.post_status = 'publish'
                AND p.post_type = '%s' AND m.meta_value!='' AND m.meta_value IS NOT NULL ORDER BY m.meta_value DESC";
                
            }
            if($rows){
                $q .= " LIMIT ".intval($rows);
            }
            
            $res = $wpdb->get_col($wpdb->prepare($q,$meta,$pt));

            if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0)
                set_transient('yg_flt_tran'.$pt.'-'.$meta.'_meta_vals',$res,intval($this->cacheoptions['cachetime'])*60);
        }
        return $res;
    }

    /**
     * Gets distinct texonomy for a certain post type
     *
     * @param string $pt post type.
     *
     */
    public function yg_filt_get_tax_pt($pt){
        global $wpdb;
        if(!is_admin() || empty($pt) || !in_array($pt,$this->post_types))
            return;
        if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0 && $taxs = get_transient('yg_flt_tran'.$pt.'_tax')){

        }else{
            $q = "SELECT tt.taxonomy FROM $wpdb->terms `t`
            INNER JOIN $wpdb->term_taxonomy `tt` ON t.term_id = tt.term_id AND tt.taxonomy NOT IN('post_tag')
            INNER JOIN $wpdb->term_relationships `r` ON r.term_taxonomy_id = tt.term_taxonomy_id 
            INNER JOIN $wpdb->posts `p` ON p.ID = r.object_id AND p.post_type='%s'
            GROUP BY tt.taxonomy";

            $taxs = $wpdb->get_col($wpdb->prepare($q,$pt));
            if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0)
                set_transient('yg_flt_tran'.$pt.'_tax',$taxs,intval($this->cacheoptions['cachetime'])*60);
        }
        return $taxs;
    }

    /**
     * AJAX functions
     */
    public function yg_fltr_get_meta(){
        if(isset($_REQUEST['posttype']) && $_REQUEST['posttype']!='' && check_admin_referer('filter-get-meta9630')){
            if(in_array($_REQUEST['posttype'],$this->post_types)){
                if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0 && $meta_keys = get_transient('yg_flt_tran'.$_REQUEST['posttype'].'_meta_keys')){

                }else{
                    global $wpdb;
                    $q = "SELECT DISTINCT(m.meta_key) FROM $wpdb->posts `p` 
                    LEFT JOIN $wpdb->postmeta `m` ON p.ID=m.post_id 
                    WHERE p.post_type='%s' AND p.post_status='publish' AND m.meta_value!='' AND m.meta_key!='' AND m.meta_key NOT RegExp '(^[_0-9].+$)' AND m.meta_key NOT RegExp '(^[0-9]+$)'";
                    $meta_keys = $wpdb->get_col($wpdb->prepare($q,$_REQUEST['posttype']));
                    if(isset($this->cacheoptions['cachetime']) && $this->cacheoptions['cachetime']>0)
                        set_transient('yg_flt_tran'.$_REQUEST['posttype'].'_meta_keys',$meta_keys,intval($this->cacheoptions['cachetime'])*60);
                }
                $html = '';
                if(post_type_supports($_REQUEST['posttype'],'thumbnail') && current_theme_supports('post-thumbnails'))
                    $html .= '<option value="thumbnail">thumbnail</option>';
                $html .= '<option value="post_id">id</option>';
                $html .= '<option value="author">author</option>';
                foreach ($meta_keys as $key) {
                    $html .= '<option value="'.$key.'">'.$key.'</option>';
                }

                print_r($html);
            }
        }
        exit;
    }

    public function yg_fltr_get_tax(){
        if(isset($_REQUEST['posttype']) && $_REQUEST['posttype']!='' && check_admin_referer('filter-get-meta9630')){
            if(in_array($_REQUEST['posttype'],$this->post_types)){
                $taxs = $this->yg_filt_get_tax_pt($_REQUEST['posttype']);
                $html = '';
                foreach ($taxs as $tax) {
                    $html .= '<option value="'.$tax.'">'.$tax.'</option>';
                }
                print_r($html);
            }
        }
        exit;
    }
    public function yg_flt_clr_cache(){
        global $wpdb;
        if(check_admin_referer('filter-cache4286')){
            $q = "SELECT option_name FROM $wpdb->options WHERE option_name LIKE '_transient_yg_flt_tran%';";
            $trans = $wpdb->get_col($q);
            $i = 0;
            foreach($trans as $tran){//use transient API for compatibility with memcache if enabled
                delete_transient($tran->option_name);
                $i++;
            }
            echo 'Cache has been deleted for '.$i.' record sets';
        }
        exit;
    }
    public function yg_flt_update_mn_oc(){
        if(isset($_REQUEST['oc']) && $_REQUEST['oc']!='' && isset($_REQUEST['fuid']) && $_REQUEST['fuid']!='' && check_admin_referer('filter-update-oc9630')){
            update_user_meta($_REQUEST['fuid'],'yg_fltr_menu_oc',$_REQUEST['oc']);
        }
        exit;
    }
}
$yg_columnpro = new yg_columnpro();