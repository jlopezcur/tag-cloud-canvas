<?php /*

**************************************************************************

Plugin Name:  Tag Cloud Canvas
Plugin URI:   https://github.com/noknokstdio/tag-cloud-canvas
Description:  Tag cloud 3d using canvas (Sphere and Cylinder)
Version:      0.1.0
Author:       Javier López Úbeda
Author URI:   http://www.noknokstdio.com

**************************************************************************

Copyright (C) 2012 Javier López Úbeda

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

**************************************************************************/

/**
 * Adds Tag_Cloud_HTML5 widget.
 */
class Tag_Cloud_Canvas extends WP_Widget {

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
            'tag_cloud_canvas', // Base ID
            'Tag Cloud Canvas', // Name
            array('description' => __('Tag cloud 3d using canvas (Sphere and Cylinder)', 'tag-cloud-canvas')), // Args
            array('width' => 400)
        );
        $plugin_dir = basename(dirname(__FILE__));
        load_plugin_textdomain('tag-cloud-canvas', false, $plugin_dir.'/languages/');
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $taxonomies = apply_filters('widget_taxonomies', $instance['taxonomies']);
        $shape = apply_filters('widget_shape', $instance['shape']);
        $width = apply_filters('widget_width', $instance['width']);
        $height = apply_filters('widget_height', $instance['height']);
        $text_color = apply_filters('widget_text_color', $instance['text_color']);
        $bg_color = apply_filters('widget_bg_color', $instance['bg_color']);
        $bg_transparent = apply_filters('widget_bg_transparent', $instance['bg_transparent']);
        
        echo $before_widget;
        if (!empty($title)) echo $before_title . $title . $after_title;
        
        ?>
<canvas width="<?php echo $width ?>" height="<?php echo $height ?>" id="tagCloudCanvas<?php echo $this->number ?>"
    <?php if ($bg_transparent != 1) : ?>style="background-color: <?php echo $bg_color ?>;"<?php endif; ?>>
    <p><?php echo __("Your browser doesn't support canvas.", 'tag-cloud-canvas') ?></p>
</canvas>
<div id="tags">
    <?php if ($taxonomies == 'tags' || $taxonomies == 'both') : ?>
        <?php wp_tag_cloud(array('taxonomy' => 'post_tag')); ?>
    <?php endif; ?>
    <?php if ($taxonomies == 'categories' || $taxonomies == 'both') : ?>
        <?php wp_tag_cloud(array('taxonomy' => 'category')); ?>
    <?php endif; ?>
</div>
<script type="text/javascript">
jQuery(function() {
   if(!jQuery('#tagCloudCanvas<?php echo $this->number ?>').tagcanvas({
     textColour : '<?php echo $text_color ?>',
     shape : '<?php echo $shape ?>',
     outlineThickness : 1,
     maxSpeed : 0.03,
     depth : 0.75
   }, 'tags')) {
     // TagCanvas failed to load
     jQuery('#tagCloudCanvas<?php echo $this->number ?>Container').hide();
   }
   // your other jQuery stuff here...
 });
</script>    
        <?php
        echo $after_widget;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['taxonomies'] = $new_instance['taxonomies'];
        $instance['shape'] = $new_instance['shape'];
        $instance['width'] = $new_instance['width'];
        $instance['height'] = $new_instance['height'];
        $instance['text_color'] = $new_instance['text_color'];
        $instance['bg_color'] = $new_instance['bg_color'];
        $instance['bg_transparent'] = $new_instance['bg_transparent'];
        return $instance;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        if (isset($instance['title'])) $title = $instance['title']; else $title = __('Tag Cloud', 'tag-cloud-canvas');
        if (isset($instance['taxonomies'])) $taxonomies = $instance['taxonomies']; else $taxonomies = 'tags';
        if (isset($instance['shape'])) $shape = $instance['shape']; else $shape = 'sphere';
        if (isset($instance['width'])) $width = $instance['width']; else $width = 300;
        if (isset($instance['height'])) $height = $instance['height']; else $height = 300;
        if (isset($instance['text_color'])) $text_color = $instance['text_color']; else $text_color = '#333333';
        if (isset($instance['bg_color'])) $bg_color = $instance['bg_color']; else $bg_color = '#ffffff';
        if (isset($instance['bg_transparent'])) $bg_transparent = $instance['bg_transparent']; else $bg_transparent = '1';
        
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tag-cloud-canvas'); ?></label> 
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <table>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id('taxonomies'); ?>"><?php _e('Taxonomies:', 'tag-cloud-canvas'); ?></label> 
                    <select id="<?php echo $this->get_field_id('taxonomies'); ?>" name="<?php echo $this->get_field_name('taxonomies'); ?>" class="widefat" style="width:100%;">
                        <option value="tags" <?php if ('tags' == $taxonomies) echo 'selected="selected"'; ?>><?php _e('Tags', 'tag-cloud-canvas'); ?></option>
                        <option value="categories" <?php if ('categories' == $taxonomies) echo 'selected="selected"'; ?>><?php _e('Categories', 'tag-cloud-canvas'); ?></option>
                        <option value="both" <?php if ('both' == $taxonomies) echo 'selected="selected"'; ?>><?php _e('Both', 'tag-cloud-canvas'); ?></option>
                    </select>
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id('shape'); ?>"><?php _e('Shape:', 'tag-cloud-canvas'); ?></label> 
                    <select id="<?php echo $this->get_field_id('shape'); ?>" name="<?php echo $this->get_field_name('shape'); ?>" class="widefat" style="width:100%;">
                        <option value="sphere" <?php if ('sphere' == $shape) echo 'selected="selected"'; ?>><?php _e('Sphere', 'tag-cloud-canvas'); ?></option>
                        <option value="hcylinder" <?php if ('hcylinder' == $shape) echo 'selected="selected"'; ?>><?php _e('Horizontal Cylinder', 'tag-cloud-canvas'); ?></option>
                        <option value="vcylinder" <?php if ('vcylinder' == $shape) echo 'selected="selected"'; ?>><?php _e('Vertical Cylinder', 'tag-cloud-canvas'); ?></option>
                    </select>
                </td>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id('width'); ?>"><?php _e('Width:', 'tag-cloud-canvas'); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id('width'); ?>" name="<?php echo $this->get_field_name('width'); ?>" type="text" value="<?php echo $width ?>" />
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id('height'); ?>"><?php _e('Height:', 'tag-cloud-canvas'); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo $height ?>" />
                </td>
            </tr>
            <tr>
                <td>
                    <label for="<?php echo $this->get_field_id('text_color'); ?>"><?php _e('Text Color:', 'tag-cloud-canvas'); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id('text_color'); ?>" name="<?php echo $this->get_field_name('text_color'); ?>" type="text" value="<?php echo $text_color ?>" />
                    <div style="position: absolute;" id="colorpicker<?php echo $this->get_field_id('text_color'); ?>"></div>
                </td>
                <td>
                    <label for="<?php echo $this->get_field_id('bg_color'); ?>"><?php _e('Background Color:', 'tag-cloud-canvas'); ?></label> 
                    <input class="widefat" id="<?php echo $this->get_field_id('bg_color'); ?>" name="<?php echo $this->get_field_name('bg_color'); ?>" type="text" value="<?php echo $bg_color ?>" />
                    <div style="position: absolute;" id="colorpicker<?php echo $this->get_field_id('bg_color'); ?>"></div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="<?php echo $this->get_field_id('bg_transparent'); ?>"><?php _e('Background Transparent?', 'tag-cloud-canvas'); ?></label>
                    <input class="checkbox" type="checkbox" value="1" <?php checked($bg_transparent, 1); ?> id="<?php echo $this->get_field_id('bg_transparent'); ?>" name="<?php echo $this->get_field_name('bg_transparent'); ?>" />
                </td>
            </tr>
        </table>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            jQuery('#colorpicker<?php echo $this->get_field_id('text_color'); ?>').hide();
            jQuery('#colorpicker<?php echo $this->get_field_id('text_color'); ?>').farbtastic("#<?php echo $this->get_field_id('text_color'); ?>");
            jQuery("#<?php echo $this->get_field_id('text_color'); ?>").click(function(){
                jQuery('#colorpicker<?php echo $this->get_field_id('text_color'); ?>').each(function() {
                    if (jQuery(this).css('display') != 'block') jQuery(this).slideDown();
                });
            });
            jQuery(document).mousedown(function() {
                jQuery('#colorpicker<?php echo $this->get_field_id('text_color'); ?>').each(function() {
                    if (jQuery(this).css('display') == 'block') jQuery(this).slideUp();
                });
            });
            
            jQuery('#colorpicker<?php echo $this->get_field_id('bg_color'); ?>').hide();
            jQuery('#colorpicker<?php echo $this->get_field_id('bg_color'); ?>').farbtastic("#<?php echo $this->get_field_id('bg_color'); ?>");
            jQuery("#<?php echo $this->get_field_id('bg_color'); ?>").click(function(){
                jQuery('#colorpicker<?php echo $this->get_field_id('bg_color'); ?>').each(function() {
                    if (jQuery(this).css('display') != 'block') jQuery(this).slideDown();
                });
            });
            jQuery(document).mousedown(function() {
                jQuery('#colorpicker<?php echo $this->get_field_id('bg_color'); ?>').each(function() {
                    if (jQuery(this).css('display') == 'block') jQuery(this).slideUp();
                });
            });
        });
        </script>
        <?php 
    }

} // class Foo_Widget

// register Foo_Widget widget
add_action('widgets_init', create_function('', 'register_widget("tag_cloud_canvas");'));

function tag_cloud_canvas_widget_init() {
    wp_enqueue_style('farbtastic'); wp_enqueue_script('farbtastic');
    wp_enqueue_script('tagcanvas', plugin_dir_url( __FILE__ ) .'jquery.tagcanvas.min.js',array('jquery'));
}
add_action('init', 'tag_cloud_canvas_widget_init');
