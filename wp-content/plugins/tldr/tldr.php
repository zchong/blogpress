<?php

/*
Plugin Name: TLDR Review
Plugin URI: http://tldrstuff.com
Description: TLDR Review for WordPress generates short summaries of posts that replace the extract that WordPress uses by default. Readers will enjoy readable sentences from posts, rather than the first 55 words that WordPress generates. The TLDR Review Plugin inserts the summary when a draft is saved or published using the WordPress editor, as well as provides keywords and optimization tips for search. Tweak the summary to make it more attention grabbing, slightly vary the sentences from the main copy, or use the summary that was automatically generated. Wherever your templates displays the WordPress Extract, the Create summary will appear instead.
Version: 1.0
Author: Stremor Corp.
Author URI: http://stremor.com
License: GPL2
*/

// Stop direct call
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

class tldr_plugin {
 	protected $_endpoint 		= 'http://liquidhelium.stremor.com/tldr/';
 	protected $_version 		= '2.0';
 	protected $_error;
 	protected $_message_key 	= "tldr_message";
 	protected $_tags_key 		= "tldr_tags";
 	protected $_summarize_key	= "tldr_summarize";
 	
 	
 	public function __construct() {
 		global $pagenow;
 		
 		//add_action('admin_menu', array(&$this, 'tldr_add_submenu'));
 		
 		// There is no nice way to include these actionns only for posts since
 		// posts and page share the same php file (post.php) 		 		
 		if (strcasecmp($pagenow, "post.php") == 0) {
	 		add_action('wp_insert_post', array(&$this, 'tldr_summary'));
	 		add_action('admin_notices', array(&$this, 'tldr_admin_notice'));
	 		add_action('post_submitbox_misc_actions', array(&$this, 'tldr_publish'));
	 		add_action('admin_enqueue_scripts', array(&$this, 'tldr_styles'));	 		
	 		$this->_error = new WP_Error();
	 	}
	 	
	 	if (strcasecmp($pagenow, "post-new.php") == 0) {
	 		add_action('post_submitbox_misc_actions', array(&$this, 'tldr_publish'));
	 		add_action('admin_enqueue_scripts', array(&$this, 'tldr_styles'));	 		
	 	}
 	}
 		 	
 	/**
	* Display option in publish box to disable TLDR.
	*
	* @param integer $post_id Post id of current post revision (populated by Wordpress action hook).
	* @return void  
	*/
 	public function tldr_publish() {
 		global $post;
 		
 		$is_revision 	= wp_is_post_revision($post->ID);
	 	$parent_id 		= ($is_revision === false) ? $post->ID : $is_revision;
		$value 			= get_post_meta($parent_id, $this->_summarize_key, true);
		$post_type 		= get_post_type($parent_id);
		
		//cleanup so meta doesn't show up in custom fields list
		delete_post_meta($parent_id, $this->_summarize_key);
		
		// only show tldr checkbox for post pages 
		if ($post_type && strcasecmp($post_type, 'post') == 0) {	
			print '<div class="misc-pub-section misc-pub-section-last">
        		<span id="timestamp">'
        		. '<label><input type="checkbox"' . (empty($value) ? null : ' checked="checked" ') . 'value="1" name="' . $this->_summarize_key . '" class="' . $this->_summarize_key . '" />&nbsp;<img src="data:image/png;base64,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" width="236" height="27" /></label></span></div>';
        }	
 	}
 	
 	/**
	* Add the plugin's stylesheet to the admin header.
	*
	* @param string $hook The current page to be loaded
	* @return void  
	*/
 	public function tldr_styles($hook) {
	 	wp_register_style( 'tldr_css', plugins_url('tldr.css', __FILE__) );
        wp_enqueue_style( 'tldr_css' );
 	}
 	
 	/**
	* get wordpress post content and send to LHe for processing.
	* update wordpress excerpt and tags on successful response from LHe
	*
	* @param integer $post_id Post id of current post revision (populated by Wordpress action hook).
	* @return void  
	*/
 	public function tldr_summary($post_id) {
 		global $wpdb;
 		 		
 		$post 			= get_post($post_id);
 		$is_revision 	= wp_is_post_revision($post_id);
	 	$parent_id 		= ($is_revision === false) ? $post_id : $is_revision;
	 		
	 	update_post_meta($parent_id, $this->_summarize_key, $_POST[$this->_summarize_key]);

 		$summarize = get_post_meta($parent_id, $this->_summarize_key, true);
 		 		
 		if (!empty($post->post_content) && $summarize === '1') {
	 		$response = $this->tldr_liquid_helium($post->post_content, $post->post_title);
	 			 		
	 		if ($response !== false) {
	 			if ($response['status']['success'] !== false) {
	 				//set excerpt content for post
	 				$wpdb->update('wp_posts', array('post_excerpt'=>$response['summary']), array('ID'=>$parent_id));
	 				
	 				//set tags after response back
	 				$this->tldr_set_tags($parent_id, $response['keywords']);
	 			} else {
		 			$this->_error->add('error', _('Sorry, TLDR could not create an excerpt for this post')); 		
		 		}
		 	}
		 	
		 	$this->tldr_store_message($parent_id);
		}	
 	}
 	
 	/**
	* Display post meta data messages if any.
	*
	* @return void  
	*/
 	public function tldr_admin_notice() {
 		global $post;
 		
 		$is_revision = wp_is_post_revision($post->ID);
 		$post_id = ($is_revision === false) ? $post->ID : $is_revision;
 		
 		$message = get_post_meta($post_id, $this->_message_key, true);
 		if ($message !== "") {
			print '<div class="error"><p>' . $message . '</p></div>';
			delete_post_meta($post_id, $this->_message_key);
		}
 	}
 	
 	/**
	* Add admin options page.
	*
	* @return void  
	*/
 	public function tldr_add_submenu() {
		if (function_exists('add_options_page')) {
			add_options_page('TLDR Plugin Options', 'TLDR Options', 9, basename(__FILE__),  array($this, 'tldr_options_page'));
		} else {
			add_submenu_page('options-general.php', 'TLDR Plugin Options', 'TLDR Options', 9,  basename(__FILE__), array($this, 'tldr_options_page'));
		}
	}
 	
 	/**
	* Admin options page callback.
	*
	* @return void  
	*/
 	public function tldr_options_page() {
 		try {
	 		$contents = file_get_contents('settings.php', true);

	 		if ($contents !== false) {
	 			print $contents;
	 		}
	 	} catch (Exception $e) {
	 		print_r($e);
	 		die();
	 	}
 	}

 	/**
	* Update post tags with keywords and preserve any additional tags set by user.
	*
	* @return void  
	*/
 	protected function tldr_set_tags($post_id, $new_tags) {	
 		$current_tags = get_post_meta($post_id, $this->_tags_key, true);
 		
 		if ($current_tags != "") { 
 			$post_tags = wp_get_post_tags($post_id, array('fields'=>'names'));
	 		
	 		//loop thru post tags and remove old Lhe keywords
	 		foreach($post_tags as $key=>$post_tag) {
		 		if (in_array($post_tag, $current_tags)) {
			 		unset($post_tags[$key]);
		 		}		
	 		}
	 		
	 		if (is_string($new_tags)) {
	 			$new_tags = explode(",", str_replace(", ", ",", $new_tags));
	 		}
	 			
	 		$merge_tags = array_merge($new_tags, $post_tags);
	 		wp_set_post_tags($post_id, $merge_tags);
	 	} else {
	 		wp_set_post_tags($post_id, $new_tags, true);
	 	}
	 	
	 	update_post_meta($post_id, $this->_tags_key, $new_tags);
 	}
 	
 	/**
	* Store error message in post meta data so it can be received after page redirect
	*
	* @return void 
	*/
 	protected function tldr_store_message($post_id) {
		$messages = $this->_error->get_error_messages('error');	
		
		if (count($messages) > 0) {
			update_post_meta($post_id, $this->_message_key, end($messages));
		}
 	}
 	
 	/**
	* Run out to LHe and retrieve the summary and keywords for the post.
	* Use Curl if possible. Else, use fopen.
	*
	* @param array $params Array source to convert to url encoded string
	* @param string $prefix
	* @param bool $removeFinalAmp
	* @return string  
	*/
 	protected function tldr_liquid_helium($content, $title) {
 		$post_data = array(
        	"content"		=> $content, 
            "url"			=> 'http' . ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'], 
            "isSelection"	=> true,
            "version"		=> "WP-" . $this->_version
        );
 		
 		$post_data = (function_exists('http_build_query')) ? http_build_query($post_data) : $this->tldr_make_query_string($post_data); 		
 		 		
 		if  (in_array('curl', get_loaded_extensions())) {
 			$ch = curl_init($this->_endpoint);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
       
            $response = curl_exec($ch);

            curl_close($ch);
        } else if (function_exists('fopen') && ini_get('allow_url_fopen') == true) {
	        $params = array('http' => array(
              'method' => 'POST',
              'content' => $post_data
            ));
			
			$ctx = stream_context_create($params);
			
			$fp = @fopen($this->_endpoint, 'rb', false, $ctx);
			
			if (!$fp) {
				$this->_error->add('error', _('Well this is embarrassing, could not access TLDR'));
				return false;
			}
			
			$response = @stream_get_contents($fp);
			
			if ($response === false) {
				$this->_error->add('error', _('Problem reading data from TLDR'));
				return false;
			}			
        }
          
        if (isset($response)) {
        	if (function_exists('json_decode')) {
        		return json_decode($response, true);
        	} else {
        		$this->_error->add('no json parser', _('This is a bummer. Looks like you\'re server and wordpress do not have an adequate json parser'));
        	}
        }
        
        return false;
    }
    
    /**
	* query string builder for php versions < 5 or php versions missing http_build_query.
	*
	* @param array $params Array source to convert to url encoded string
	* @param string $prefix
	* @param bool $removeFinalAmp
	* @return string  
	*/
    protected function tldr_make_query_string($params, $prefix = '', $removeFinalAmp = true) { 
	    $queryString = ''; 
	    
	    if (is_array($params)) { 
	        foreach ($params as $key => $value) { 
	            $correctKey = $prefix; 
	            
	            if ('' === $prefix) { 
	                $correctKey .= $key; 
	            } else { 
	                $correctKey .= "[" . $key . "]"; 
	            } 
	            
	            if (!is_array($value)) { 
	                $queryString .= urlencode($correctKey) . "=" . urlencode($value) . "&"; 
	            } else { 
	                $queryString .= $this->tldr_make_query_string($value, $correctKey, false); 
	            } 
	        } 
	    } 
	    
	    if ($removeFinalAmp === true) { 
	        return substr($queryString, 0, strlen($queryString) - 1); 
	    } else { 
	        return $queryString; 
	    } 
	}
}  

//startup plugin
$tldr_plugin = new tldr_plugin();
?>