<?php

class OpalEstate_Command {

    public $path;

    /**
     * Constructor
     */
    public function __construct() {
        return;

        $this->path = OPALESTATE_PLUGIN_DIR;

        // $this->cfg 	   = json_decode( file get contents( $this->path.'project.json' ) );
        $this->cfg = json_decode(wp_remote_get($this->path . 'project.json'));

        $this->server  = $this->cfg->server;
        $this->oldurl  = $this->cfg->oldurl;
        $this->theme   = $this->cfg->theme;
        $this->name    = $this->cfg->name;
        $this->subpath = $this->cfg->subpath;
    }

    /**
     * Write data in file
     */
    private function output_file_content($file_path, $output) {
        $fp = fopen($file_path, 'w+');
        fwrite($fp, $output);
        fclose($fp);
    }

    /**
     * Get more options
     */
    public function get_more_options() {
        $data = array(
            'header' => 'header-1',
            'footer' => 'footer-1',
            'page'   => 'home-1'
        );

        if (isset($this->cfg->active) && $this->cfg->active) {
            $data = (array)$this->cfg->active;
        }

        return array(
            'active'  => $data,
            'oldurl'  => $this->oldurl,
            'server'  => $this->server,
            "samples" => array(
                'post'    => array(),
                'product' => array()
            )
        );
    }

    /**
     * Export theme options in customizer
     */
    public function options() {
        return array();
        $thememods = get_option('theme_mods_' . $this->theme);
        $file      = $this->path . '/sample/thememods.json';
        $ids       = array();
        foreach ($thememods as $key => $mod) {


            if (is_string($mod)) {
                // $thememods[$key] = $this->_replace_uri( $mod, $this->oldurl, "SITE_URL_HERE");
                if (preg_match("#jpg|png|gif|svg#", $mod) && $mod) {
                    $ids[$key] = $this->find_image_id_byguid($mod);
                }
            }
            if (isset($thememods['wpopal_customize_css'])) {
                unset($thememods['wpopal_customize_css']);
            }

            if (isset($thememods['osf_theme_custom_style'])) {
                unset($thememods['osf_theme_custom_style']);
            }

            if (isset($thememods['sidebars_widgets'])) {
                unset($thememods['sidebars_widgets']);
            }

            ///
            if ($key == 'custom_logo' && $mod) {
                $ids[$key] = $mod;
            }

        }


        $attachments = array();

        if ($ids) {
            foreach ($ids as $id) {
                $post = get_post($id);
                if ($post) {
                    $attachments[$post->ID] = array(
                        'id'          => $post->ID,
                        'guid'        => wp_get_attachment_url($post->ID),
                        'post_parent' => $post->post_parent,
                        'post_name'   => $post->post_name,
                        'post_date'   => $post->post_date);
                }
            }
        }

        $options = array(
            'woocommerce_single_image_width'               => '',
            'woocommerce_thumbnail_image_width'            => '',
            'woocommerce_thumbnail_cropping'               => '',
            'woocommerce_thumbnail_cropping_custom_height' => '',
            'woocommerce_thumbnail_cropping_custom_width'  => '',
            "woocommerce_shop_page_id"                     => '',
            "woocommerce_cart_page_id"                     => '',
            'woocommerce_checkout_page_id'                 => '',
            'woocommerce_myaccount_page_id'                => '',
            'woocommerce_terms_page_id'                    => '',
            'yith_wcwl_wishlist_page_id'                   => ''

        );

        foreach ($options as $key => $value) {
            $value = get_option($key);
            if (empty($value)) {
                unset($options[$key]);
                continue;
            }
            $options[$key] = $value;
        }

        $options['page_for_posts'] = 'Blog';
        if (function_exists('wp_get_custom_css_post')) {
            $options['wp_css'] = wp_get_custom_css();
        }
        $data = array('thememods' => $thememods, 'attachments' => $attachments, "options" => $options);


        //	$this->output_file_content( $file, wp_json_encode($data ,JSON_PRETTY_PRINT ) );

        return $data;
    }

    /**
     * Replace URI in xml
     */
    public function _replace_uri($str, $oldurl, $server) {
        $str = str_replace(str_replace("/", "\/", $oldurl), str_replace("/", "\/", $server), $str);
        $str = str_replace($oldurl, $server, $str);
        $str = str_replace(str_replace("/", "\\\\\\/", $oldurl), str_replace("/", "\\\\\\/", $server), $str);
        return $str;
    }

    /**
     * Export config samples
     */
    public function config_samples($dev = 0) {


        $data['samples'] = array();


        $file = $this->path . $this->cfg->folder_source . '/samples.json';


        $niches = $this->cfg->niches;

        $single = $this->cfg->single;

        $key               = 0;
        $data['samples'][] = array(
            "name"    => isset($single->name) ? $single->name : "Sample",
            "key"     => "niche-" . $key,
            "url"     => isset($single->url) ? $single->url : "",
            "demo"    => isset($single->demo) ? $single->demo : "",
            "preview" => $this->cfg->server_source . '/screenshot.png',
            "sample"  => $this->cfg->server_source . '/data.zip'
        );

        $this->output_file_content($file, wp_json_encode($data));
        ///
        //$file = $this->path.'/wp-content/themes/'.$this->cfg->theme.'/project.json';
        //$data = $this->cfg->themeinfo;
        //$this->output_file_content( $file, json_encode($data , JSON_PRETTY_PRINT )  );
    }

    /**
     * Export all pages
     */
    public function pages() {

        return array();
        $excludes = array();

        global $wpdb;

        $post_type = 'page';
        /// items

        $where = $wpdb->prepare("{$wpdb->posts}.post_type = %s and {$wpdb->posts}.post_content LIKE '%opalestate_%'", $post_type);


        // grab a snapshot of post IDs, just in case it changes during the export
        $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE $where");

        $data = $this->export_json_ids($post_ids, $excludes, $isattachement);

        echo '<pre>' . print_r($data, 1);
        die;

        //$file = $this->path.'sample/pages.json';
        // $this->output_file_content( $file, wp_json_encode($data ) );
        return $data;

    }

    /**
     * Export all pages
     */
    public function data_posttypes() {

        $excludes = array();

        $data = $this->export_posttype(array(
            'opalestate_agent',
            'opalestate_agency',
            'opalestate_agent_ft',
            'opalestate_agency_ft',
            'opalestate_rating_ft',
            'opalestate_property'
        ), $excludes);

        //	$file = ABSPATH.'src/json/elementor.json';
        //	$this->output_file_content( $file, wp_json_encode($data ) );

        return $data;

    }

    /**
     * get all attachments by post ids
     */
    protected function get_attachment($post_ids) {
        $attachments = array();
        foreach ($post_ids as $post_id) {

            $value = get_post_meta($post_id, '_thumbnail_id', true);
            if ($value) {
                $attachments = array_merge($attachments, array($value));
            }


            $attachArgs  = array(
                'post_parent'  => $post_id,
                'post_type'    => 'attachment',
                'numberposts'  => -1,
                'post__not_in' => $attachments, //To skip duplicates
            );
            $attachList  = get_children($attachArgs, ARRAY_A);
            $attachments = array_merge($attachments, array_keys($attachList));
        }

        $ids = $this->get_images_posts($post_ids);
        if ($ids) {
            $attachments = array_merge($ids, $attachments);
        }

        return $attachments;
    }

    /**
     * Export data posts by post type with attachments
     */
    protected function export_posttype($post_type, $excludes = array(), $isattachement = true) {

        global $wpdb;

        /// items

        if (is_array($post_type)) {
            $tmp   = "'" . implode("','", $post_type) . "'";
            $where = "{$wpdb->posts}.post_type IN( " . $tmp . " )";
        } else {
            $where = $wpdb->prepare("{$wpdb->posts}.post_type = %s", $post_type);
        }

        // grab a snapshot of post IDs, just in case it changes during the export
        $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE $where");

        return $this->export_json_ids($post_ids, $excludes, $isattachement);

    }

    public function aa() {
        global $wpdb;
        $postmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE  post_id = %d", 240));

        $exls = array(
            '_edit_last',
            '_edit_lock',
            '_elementor_version',
            '_menu_item_type',
            '_menu_item_menu_item_parent',
            '_menu_item_object_id',
            '_menu_item_object',
            '_menu_item_target',
            '_menu_item_classes',
            '_menu_item_xfn',
            '_menu_item_url',
            '_elementor_css'
        );

        $tmp = array();
        foreach ($postmeta as $key => $meta) {
            unset($postmeta[$key]->post_id);
            unset($postmeta[$key]->meta_id);
            foreach ($exls as $exl) {
                if ($meta->meta_key == $exl) {
                    unset($postmeta[$key]);
                }
            }
        }
        foreach ($postmeta as $key => $meta) {
            $tmp[$meta->meta_key] = $meta->meta_value;
        }
    }

    /**
     * export json data by ids
     */
    protected function export_json_ids($post_ids, $excludes, $isattachement) {
        global $wpdb;

        $output = array();

        $export = array();

        if ($isattachement) {
            $ids = $this->get_attachment($post_ids);


            if ($ids) {

                $where = 'WHERE ID IN (' . join(',', $ids) . ')';
                $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

                $export['attachments'] = array();
                foreach ($posts as $post) {
                    $export['attachments'][$post->ID] = array(
                        'id'          => $post->ID,
                        'guid'        => wp_get_attachment_url($post->ID),
                        'post_parent' => $post->post_parent,
                        'post_name'   => $post->post_name,
                        'post_date'   => $post->post_date);
                }
            }
        }

        while ($next_posts = array_splice($post_ids, 0, 20)) {

            $where = 'ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} WHERE post_status='publish' and  $where");

            // Begin Loop.
            foreach ($posts as $post) {

                if (in_array($post->ID, $excludes)) {
                    continue;
                }

                setup_postdata($post);
                $data = array();
                if (get_post_meta($post->ID, '_elementor_edit_mode', true) === 'builder') {
                    $post->post_content = "";
                }

                $post->guid = $this->_replace_uri($post->guid, $this->oldurl, "SITE_URL_HERE");

                $excludes = array(
                    'guid',
                    'to_ping',
                    'pinged',
                    'post_content_filtered',
                    'post_mime_type',
                    'comment_count',
                    'filter',
                    'post_modified',
                    'post_modified_gmt'
                );

                foreach ($excludes as $exl) {
                    if (isset($post->$exl)) {
                        unset($post->$exl);
                    }
                }

                $data['post'] = $post;
                // meta data
                $postmeta = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE  post_id = %d", $post->ID));

                $exls = array(
                    '_edit_last',
                    '_edit_lock',
                    '_elementor_version',
                    '_menu_item_type',
                    '_menu_item_menu_item_parent',
                    '_menu_item_object_id',
                    '_menu_item_object',
                    '_menu_item_target',
                    '_menu_item_classes',
                    '_menu_item_xfn',
                    '_menu_item_url',
                    '_elementor_css'
                );

                $tmp = array();
                foreach ($postmeta as $key => $meta) {
                    unset($postmeta[$key]->post_id);
                    unset($postmeta[$key]->meta_id);
                    foreach ($exls as $exl) {
                        if ($meta->meta_key == $exl) {
                            unset($postmeta[$key]);
                        }
                    }
                }
                foreach ($postmeta as $key => $meta) {
                    $tmp[$meta->meta_key] = $meta->meta_value;
                }

                $data['postmeta'] = $tmp;


                if ($post->post_type == 'attachment') {
                    $data['attachment_url'] = wp_get_attachment_url($post->ID);
                }


                $data['thumbnail_id'] = get_post_meta($post->ID, '_thumbnail_id', true);

                // taxonomy
                $taxonomies = get_object_taxonomies($post->post_type);

                if (!empty($taxonomies)) {

                    $data['taxonomy'] = array();
                    $terms            = wp_get_object_terms($post->ID, $taxonomies);

                    foreach ((array)$terms as $term) {

                        if ($term->taxonomy == 'nav_menu') {
                            continue;
                        }

                        $t                  = array(
                            $term->taxonomy => array(
                                $term->slug => $term->name
                            )
                        );
                        $data['taxonomy'][] = $t;
                    }
                }

                $output [] = $data;
            }
        }

        $export['posts'] = $output;
        return $export;
    }

    /**
     * find attachment post id by guid
     */
    private function find_image_id_byguid($image_url) {
        global $wpdb;
        $attachment = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE guid LIKE '%" . $image_url . "%';");

        return isset($attachment[0]) ? $attachment[0] : 0;
    }

    /**
     * find all images:svg,git,png,jpg  in data setting of elementor
     */
    private function get_images_posts($ids) {
        $mids   = array();
        $images = array();
        foreach ($ids as $id) {
            if (get_post_meta($id, '_elementor_edit_mode', true) === 'builder') {
                $data = json_decode(get_post_meta($id, '_elementor_data', true), ARRAY_A);
                if ($data) {
                    $string  = "start" . print_r($data, true) . "end";
                    $pattern = '~(http.*\.)(jpe?g|png|svg|gif|[tg]iff?|svg)~i';
                    $m       = preg_match_all($pattern, $string, $matches);
                    if ($matches[0]) {
                        foreach ($matches[0] as $img) {
                            $pattern = '#(/\d+/\d+/(.*))$#';
                            $a       = preg_match($pattern, $img, $m);
                            if (isset($m[1])) {    ///echo '<Pre>' . print_r( $data, 1 );die;
                                $_id = $this->find_image_id_byguid($m[1]);
                                if ($_id) {
                                    $mids[$_id] = $_id;
                                } else {
                                    $images[] = $img;
                                }
                            }
                        }
                    }
                }
            }
        }
        echo '<Pre> Missing Images:' . print_r($images, 1);  //die;
        return $mids;
    }


    public function sample() {
        $data = array();

        return $data;
    }


    public function download_images($attachments, $jcontent) {

        $folder = $this->path . '/' . $this->cfg->folder_source . '/images';

        if (is_dir($folder)) {
            $file = new Filesystem();
            $file->deleteDirectory($folder);
        }

        if (!is_dir($folder)) {
            mkdir($folder);
        }

        $url = $this->cfg->server_source . '/images/';

        $replaces = array();
        foreach ($attachments as $attachment) {
            $guid = str_replace($this->server, $this->oldurl, $attachment['guid']);
            // $image = file get contents(  $guid );
            $image = wp_remote_get($guid);
            $name  = basename($attachment['guid']);
            $path  = $folder . '/' . $name;
            file_put_contents($path, $image);

            $guid            = $attachment['guid'];
            $newurl          = $url . $name;
            $replaces[$guid] = $newurl;
        }

        foreach ($replaces as $key => $replace) {
            $jcontent = $this->_replace_uri($jcontent, $key, $replace);
        }

        return $jcontent;
    }

    /**
     * export all sample data
     */
    public function source() {

        // $content = file get contents( $this->path.'/sample/data.json' );
        $content = wp_remote_get($this->path . '/sample/data.json');

        $data = json_decode($content, true);

        $file = new Filesystem();

        $content = $this->download_images($data['attachments'], $content);

        $file = $this->path . '/' . $this->cfg->folder_source . '/data.json';
        $this->output_file_content($file, $content);

        $zip = new ZipArchive;
        if ($zip->open($this->path . '/' . $this->cfg->folder_source . '/data.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $zip->addFile($file, 'data.json');
            $zip->close();
            echo 'ok';
        } else {
            echo 'failed';
        }
    }

    /**
     * export all sample data
     */
    public function data() {

        $this->fix_images_parents();
        $data = $this->pages();

        $data = array_merge_recursive(
            $data,
            $this->data_posttypes(),
            $this->options(),
            $this->get_more_options(),
            $this->sample()
        );


        $data = apply_filters('opaltools_single_wp_cli_exporter', $data, $this->cfg);

        $file = $this->path . 'sample/data.json';


        ///
        $attachments = array();
        foreach ($data['attachments'] as $key => $value) {
            $attachments[$value['id']] = $value;
        }

        $data['attachments'] = $attachments;

        $content = $this->_replace_uri(json_encode($data, JSON_PRETTY_PRINT), $this->oldurl, $this->server);
        $content = str_replace($this->cfg->theme, $this->cfg->domain, $content);

        $this->output_file_content($file, $content);

        $this->config_samples();
        $this->source();
    }

    public function fix_images_parents() {
        global $wpdb;

        $fixs = array(
            'product-',
            'bg-slider',
            'slide-',
            'bg-slide'
        );

        foreach ($fixs as $fix) {
            $wpdb->query("UPDATE wp_posts SET post_parent=0 WHERE post_title LIKE '" . $fix . "%'");
        }
    }
}

WP_CLI::add_command('opalestate', new OpalEstate_Command);
?>
