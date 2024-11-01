<?PHP
/*
  Plugin Name: WP-TagTip
  Plugin URI: http://www.coders.me/wordpress/wp-tagtip
  Description: An sexy way to show a related post of one specific tag.
  Version: 0.1
  Author: Eduardo Daniel Sada
  Author URI: http://www.coders.me/
  Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eduardosada%40gmail%2ecom&item_name=WPTagTip%20Plugin%20Development&no_shipping=0&no_note=1&tax=0&currency_code=USD
*/


add_option('related_width' , 340);
add_option('related_css'   , "vista");
add_option('related_items' , 4);
add_option('related_title' , "Enlaces relacionados");
add_option('related_framework', 1);

$related['data'] = '';

// Returns the script path
function relatedPath()
{
  return plugins_url('/wp-tagtip/');
}

// Add CSS to header
function relatedHeader()
{
  if (!is_single())
  {
    return false;
  }

  $relatedPath = relatedPath();
  echo '<link rel="stylesheet" href="'.$relatedPath.'js/sexy-tooltips/'.get_option('related_css').'.css" type="text/css" media="all"/>';
  echo '<link rel="stylesheet" href="'.$relatedPath.'css/style.css" type="text/css" media="all"/>';
}

// Add Scripts to footer
function relatedFooter()
{
  global $related;

  if (!$related['data'])
  {
    return false;
  }

  $relatedPath = relatedPath();
  echo "\n\n<!-- relatedLink Start -->\n\t";
  
  echo '
  <script type="text/javascript">

    var related = new Array();
    related["dir"]     = "'.$relatedPath.'";
    related["width"]   = '.get_option('related_width').';
    related["title"]   = "'.get_option('related_title').'";
    related["data"]    = new Array();
    
  </script>
  <script type="text/javascript">'."\n";
  foreach ((array)$related['data'] as $i=>$title)
  {
    echo "  related['data'][{$i}] = '{$title}'; \n";
  }
  echo '  </script>';

  
  if (get_option('related_framework'))
  {
    echo '<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/mootools/1.2.3/mootools-yui-compressed.js"></script>';
  }
  echo '<script type="text/javascript" src="'.$relatedPath.'js/sexy-tooltips.js"></script>';
  echo '<script type="text/javascript" src="'.$relatedPath.'js/relatedcore.js"></script>';
  echo "\n\n<!-- relatedLink End -->\n\t";
}

$related['i'] = 0;
function relatedtag_func($atts, $content=null)
{
  global $related;
  global $post;

  if (!is_single())
  {
      return $content;
  }

	$related_query = new WP_Query();
  $related_query->query(array(
  "showposts" => get_option("related_items"),
  "post__not_in"   => array($post->ID),
  "tag"       => str_replace(" ","-", trim(strtolower($content)))
  ));

	$related_query->in_the_loop = true;
	$related_query->is_single = true;
				
  if ($related_query->have_posts())
  {
      $temp = '';
      while ($related_query->have_posts())
      {
          $related_query->the_post();
          $thumb = get_post_meta($related_query->post->ID, 'thumbnail', true);
          if ((bool)$thumb)
          {
              $thumb = "<img src=\"{$thumb}\" width=\"48\" height=\"48\" alt=\"\"/>";
          }
          else
          {
              if (function_exists('get_cat_icon'))
              {
                  $thumb = get_cat_icon('link=false&fit_width=48&fit_height=48&small=true&echo=false');
              }
              else
              {
                $thumb = "<img src=\"".relatedPath()."images/onebit_47.png\" width=\"48\" height=\"48\" alt=\"\"/>";
              }
          }

          $perma = get_permalink();
          $title = the_title('','',false);
          
          $temp  .= "<li><a href=\"{$perma}\">{$thumb}{$title}<br/><span>".__('(more...)')."</span></a></li>";

      }
      $related['data'][$related['i']] = $temp;
      $related['i']++;
      wp_reset_query();
      $span = '<span class="relatedspan">'.$content.'</span>';
  }
  else
  {
      $span = $content;
  }



  return $span;
}







function related_ShowOptions()
{

	if (isset($_POST['info_update'])) : ?>
      <div id="message" class="updated fade">
      <p><strong>
        <?
          update_option('related_framework', (bool) $_POST["related_framework"]);
          update_option('related_width',     (int)  $_POST["related_width"]);
          update_option('related_items',     (int)  $_POST["related_items"]);
          update_option('related_css',       $_POST["related_css"]);
          update_option('related_title',     $_POST["related_title"]);

          _e('Settings saved.');
        ?>
      </strong></p>
      </div>
  <? endif; ?>

	<div class="wrap">
    <div id="icon-plugins" class="icon32">
      <br/>
    </div>
    <h2>WP-TagTip</h2>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
      <input type="hidden" name="info_style" value="1" />

      <h3>Style</h3>

      <table class="form-table">
        <tr valign="top">
          <th scope="row"><label for="related_framework">Add MooTools ?</label></th>
          <td>
          <select name="related_framework"/>
          <option value="0" <?php echo get_option('related_framework')==0?'selected="selected"':''; ?>><?php _e('No'); ?></option>
          <option value="1" <?php echo get_option('related_framework')==1?'selected="selected"':''; ?>><?php _e('Yes'); ?></option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="related_title">Title</label></th>
          <td>
          <input type="text" name="related_title" value="<?php echo get_option('related_title')?>"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="related_width">Width</label></th>
          <td>
          <input type="text" name="related_width" value="<?php echo get_option('related_width')?>"/>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="related_css">CSS</label></th>
          <td>
          <select name="related_css"/>
          <option value="vista" <?php echo get_option('related_css')=="vista"?'selected="selected"':''; ?>>Vista</option>
          <option value="coda" <?php echo get_option('related_css')=="coda"?'selected="selected"':''; ?>>Coda</option>
          <option value="blue" <?php echo get_option('related_css')=="blue"?'selected="selected"':''; ?>>Blue</option>
          <option value="rosita" <?php echo get_option('related_css')=="rosita"?'selected="selected"':''; ?>>Rosita</option>
          <option value="hulk" <?php echo get_option('related_css')=="hulk"?'selected="selected"':''; ?>>Hulk</option>
          </select>
          </td>
        </tr>
        <tr valign="top">
          <th scope="row"><label for="related_items">Max number of related posts</label></th>
          <td>
          <input type="text" name="related_items" value="<?php echo get_option('related_items')?>"/>
          </td>
        </tr>
      </table>
      <p class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options &raquo;'); ?>" />
      </p>
    </form>

    <h3>Donate</h3>
    <p>Support this plugin for future updates</p>
    <p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=eduardosada%40gmail%2ecom&item_name=WPTagTip%20Plugin%20Development&no_shipping=0&no_note=1&tax=0&currency_code=USD">Buy me a coffee</a></p>
    <h3>Credits</h3>
    <p><a href="http://www.coders.me/wordpress/wp-tagtip">wp-tagtip</a>. Author: <a href="http://www.coders.me/about">Eduardo Daniel Sada</a></p>
  </div>
	
<?php
} //end function

function related_menu()
{
  if (function_exists('add_options_page'))
  {
    add_options_page('WP-TagTip', "WP-TagTip", 8, __FILE__, 'related_ShowOptions');
  }
}



add_shortcode('tagtip', 'relatedtag_func');

add_action('wp_head', 'relatedHeader' );
add_action('wp_footer', 'relatedFooter' );
add_action('admin_menu', 'related_menu');




// Register editor button hooks
add_filter( 'mce_external_plugins'  , 'RELATED_mce_external_plugins' );
add_filter( 'mce_buttons'           , 'RELATED_mce_buttons' );

// Load the custom TinyMCE plugin
function RELATED_mce_external_plugins( $plugins )
{
    $plugins['RELATED_customMCEPlugin'] = plugins_url('/wp-tagtip/js/editor_plugin.js');
    return $plugins;
}


// Add the custom TinyMCE buttons
function RELATED_mce_buttons( $buttons ) {
    array_push( $buttons, 'RELATED_MCECustomButton');
    return $buttons;
}


add_action('admin_print_scripts', 'RELATED_my_custom_quicktags');

function RELATED_my_custom_quicktags()
{
    wp_enqueue_script('my_custom_quicktags', relatedPath().'js/my-custom-quicktags.js', array('quicktags') );
}

?>