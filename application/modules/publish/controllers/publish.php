<?php
class Publish extends CI_Controller
{
	function __construct() {
		parent::__construct();
		$this->template->set('controller', $this);
		$this->load->model('publishmodel');
		$this->config->load('publish/config');
		
		// profiler benchmark
		$this->output->enable_profiler($this->config->item('enable_profiler'));

		if($this->config->item('cache_enable')) {
			$this->output->cache($this->config->item('cache_expires'));
		}		
	}
	
	function _remap() {
		$uri2 = $this->uri->segment(2);
		$uri3 = $this->uri->segment(3);

		// check if uri2 is reserved as category url
		// if it is, show category controller. Else show item details
		$category = $this->publishmodel->check_category($uri2);

		if ($uri2 == FALSE OR is_numeric($uri2)) {
			$this->index();
		} elseif($uri2 == 'rss' OR $uri3 == 'rss') {
			$this->rss();
		} elseif($uri2 == TRUE AND $category == FALSE OR $uri3 == TRUE AND !is_numeric($uri3)) {
			$this->detail();
		} elseif ($uri2 == TRUE OR is_numeric($uri3)) {
			$this->category();
		}
	}

	/**
	 * Articles from the current category
	 */	
	function index() {
		$this->load->model('extramodel');
		$this->load->helper('image');
		$uri1 = $this->uri->segment(1);
		$uri2 = $this->uri->segment(2);
		$uri3 = $this->uri->segment(3);

		// Get current category data
		$category = $this->publishmodel->get_category_data($uri1);

		/**
		 * Articles per page. Defined in the publish config file. 
		 * If category per_per page value is not defined, default per_page value will be used
		 */
		$per_category = ($category) ? $this->config->item('publish_'.$category[0]->tpl.'_per_page') : 0;
		$per_page = ($category AND $per_category == FALSE) ? $this->config->item('publish_per_page') : $per_category;		

		// Get all posts from the category and subcategories
		$posts = $this->publishmodel->get_posts(array('per_page' => $per_page, 'offset' => $uri2, 'category' => $uri1));

		// Pagination
		$pagination = array(
						   	'uri_segment' => 2,
							'base_url' => base_url().$uri1,
							'total_rows' => $posts['total_rows'],
							'per_page' => $per_page,
						   );
		$this->load->library('pagination', $pagination);

		// Set breadcrumbs title
		if($category) {
			$this->template->set('bc', array($category[0]->title));	
		}

		@$data = array(
					'class' => 'page-publish-index page-'.$category[0]->slug.'-index',
					'page_title' => $category[0]->title,
					'title' => ($category[0]->seo_title == '') ? $category[0]->title : $category[0]->seo_title,
					'id' => $category[0]->id,
					'image' => $category[0]->image,
					'image_title' => $category[0]->image_title,
					'image_alt' => $category[0]->image_alt,
					'content' => htmlspecialchars_decode($category[0]->content),
					'meta_keywords' => ($category[0]->meta_keywords == '') ? '' : '<meta name="keywords" content="'.$category[0]->meta_keywords.'" />' . "\n",
					'meta_description' => ($category[0]->meta_description == '') ? '' : '<meta name="description" content="'.$category[0]->meta_description.'" />' . "\n",
					'query' => $posts['posts'],
					'total_rows' => $posts['total_rows'],
					);
		
		// Load category template. If choosen template does not exist, default one will be used
		if(@$category[0]->tpl == 'default') {
			$template = 'index';
		} else {
			@$template = (file_exists(APPPATH.'/views/publish/'.$category[0]->tpl)) ? $category[0]->tpl.'/index' : 'index';
		}

		$this->template->load_partial('master', 'publish/'.$template, $data);
	}

	/**
	 * Articles from the sub category
	 */		
	function category() {
		$this->load->model('extramodel');
		$this->load->helper('image');
		$uri1 = $this->uri->segment(1);
		$uri2 = $this->uri->segment(2);
		$uri3 = $this->uri->segment(3);

		// Get current category data
		$category = $this->publishmodel->get_category_data(array($uri1, $uri2));
		
		/**
		 * Articles per page. Defined in the publish config file. 
		 * If category per_per page value is not defined, default per_page value will be used
		 */
		@$per_category = $this->config->item('publish_'.$category[1]->tpl.'_per_page');
		$per_page = ($category AND $per_category == FALSE) ? $this->config->item('publish_per_page') : $per_category;

		// Get posts from the root category
		$posts = $this->publishmodel->get_posts(array('per_page' => $per_page, 'offset' => $uri3, 'category' => $uri2));
		
		// create pagination
		$pagination = array(
						   	'uri_segment' => 3,
							'base_url' => base_url().$uri1.'/'.$uri2,
							'total_rows' => $posts['total_rows'],
							'per_page' => $per_page,
						   );
		$this->load->library('pagination', $pagination);

		// Set breadcrumbs title
		if($category) {
			$bc1 = '<a href="'.$this->config->item('base_url').$category[0]->slug.'">'.$category[0]->title.'</a>';
			$this->template->set('bc', array($bc1, $category[1]->title));
		}

		@$data = array(
					'class' => 'page-publish-index page-'.$category[1]->slug.'-index',
					'page_title' => $category[1]->title,
					'id' => $category[1]->id,
					'title' => ($category[1]->seo_title == '') ? $category[1]->title : $category[1]->seo_title,
					'content' => htmlspecialchars_decode($category[1]->content),
					'image' => $category[1]->image,
					'image_title' => $category[1]->image_title,
					'image_alt' => $category[1]->image_alt,
					'meta_keywords' => ($category[1]->meta_keywords == '') ? '' : '<meta name="keywords" content="'.$category[1]->meta_keywords.'" />' . "\n",
					'meta_description' => ($category[1]->meta_description == '') ? '' : '<meta name="description" content="'.$category[1]->meta_description.'" />' . "\n",
					'query' => $posts['posts'],
					'total_rows' => $posts['total_rows'],
					);
		
		// Load category template. If selected template does not exist, default one will be used
		if(@$category[1]->tpl == 'default') {
			$template = 'index';
		} else {
			@$template = (file_exists(APPPATH.'/views/publish/'.$category[1]->tpl)) ? $category[1]->tpl.'/index' : 'index';
		}

		$this->template->load_partial('master', 'publish/'.$template, $data);
	}
	
	/**
	 * Article details
	 */
	function detail() {
		$post = $this->publishmodel->get_post();

		if($post['images']) {
			$this->load->helper('image');
		}
		
		// Get current language
		$lang = ($this->config->item('language_abbr') == $this->config->item('default_language')) ? '' : $this->config->item('language_abbr') . '/';
		
		// Setting up breadcrumbs
		if($post) {
			$bc_post_title = $post['post']->title;
		
			$total_segments = $this->uri->total_segments();
			if($total_segments > 2) {		
				$bc1 = $this->publishmodel->get_category_data($this->uri->segment(1));
				$bc1 = ($bc1) ? '<a href="'.get_category_url($bc1[0]->slug).'">'.$bc1[0]->title.'</a>' : '';
				$bc2 = '<a href="'.get_category_url($post['category']->slug, $post['category']->level).'">'.$post['category']->title.'</a>';
				$bc = array($bc1, $bc2, $bc_post_title);
			} else {
				$bc1 = '<a href="'.get_category_url($post['category']->slug, $post['category']->level).'">'.$post['category']->title.'</a>';		
				$bc = array($bc1, $bc_post_title);
			}
			$this->template->set('bc', $bc);	
		}

		@$data = array(
					'class' => 'page-publish-detail page-'.$post['category']->slug.'-detail',
					'id' => $post['post']->id,
					'page_title' => $post['post']->title,
					'title' => ($post['post']->seo_title == '') ? $post['post']->title : $post['post']->seo_title,
					'excerpt' => htmlspecialchars_decode($post['post']->excerpt),
					'content' => htmlspecialchars_decode($post['post']->content),
					'date' => $post['post']->date,
					'modified' => $post['post']->modified,
					'author_username' => $post['post']->author_username,
					'author_name' => $post['post']->author_name,
					'meta_keywords' => ($post['post']->meta_keywords == '') ? '' : '<meta name="keywords" content="'.$post['post']->meta_keywords.'" />' . "\n",
					'meta_description' => ($post['post']->meta_description == '') ? '' : '<meta name="description" content="'.$post['post']->meta_description.'" />' . "\n",
					'category' => $post['category'],
					'images' => $post['images'],
					);		

		if($post AND $post['images']) {
			$data['main_image'] = $post['images'][0];
		}

		// Load post template
		if($post AND $post['category']->tpl == 'default') {
			$template = 'detail';
		} else {
			@$template = (file_exists(APPPATH.'/views/publish/'.$post['category']->tpl)) ? $post['category']->tpl.'/detail' : 'detail';
		}

		$this->template->load_partial('master', 'publish/'.$template, $data);
	}


	/**
	 * RSS feed
	 */
	function rss() {
		$this->load->helper('xml');
		$lang = $this->config->item('language_abbr');

		$s1 = $this->uri->segment(1);
		$s2 = $this->uri->segment(2);

		// Get category data based on current category level
		if($this->uri->total_segments() == 3) {			
			$rss_data = $this->publishmodel->get_posts(array('category' => $s2, 'per_page' => $this->config->item('publish_rss_per_page')));
			$category_data = $this->publishmodel->get_category_data(array($s1, $s2));
			
			$category_url = base_url() . $category_data[0]->slug . '/' . $category_data[1]->slug;
		} else {
			$rss_data = $this->publishmodel->get_posts(array('category' => $s1, 'per_page' => $this->config->item('publish_rss_per_page')));
			$category_data = $this->publishmodel->get_category_data(array($s1));

			$category_url = base_url() . $category_data[0]->slug;
		}

		$data = array(
					  'title' => (@$category_data[0]->seo_title == '') ? @$category_data[0]->title : @$category_data[0]->seo_title,
					  'content' => strip_tags(@$category_data[0]->content),
					  'link' => $category_url,
					  'language' => $lang,
					  'query' => $rss_data['posts'],
					  );
		
		$this->load->view('publish/rss', $data);
	}	
}
?>