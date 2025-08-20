<?php
class Pages extends CI_Controller
{
	function __construct() {
		parent::__construct();
		$this->template->set('controller', $this);

		$this->output->enable_profiler($this->config->item('enable_profiler'));

		if($this->config->item('cache_enable')) {
			$this->output->cache($this->config->item('cache_expires'));
		}
	}

	/**
	 * Display the Page
	 */		
	function index() {
		// Get requested page
		$result = $this->pagesmodel->get_page();

		// Show the 404 template if page slug does not exist
		if ($result AND $result->slug) {
			// Page class
			$page_class = ($result->slug == '/') ? 'homepage' : $result->slug;

			// Choosen template
			$template = (!$result->tpl) ? 'default' : $result->tpl;

			// Set breadcrumbs title
			$bc = ($result AND $result->slug == '/') ? '' : $result->title;
			$this->template->set('bc', array($bc));

			$data = array(
					'id' => $result->id,
					'class' => 'page-'.$page_class.' page-template-'.$template,
					'page_title' => $result->title,
					'title' => ($result->seo_title == '') ? $result->title : $result->seo_title,
					'content' => htmlspecialchars_decode($result->content),
					'meta_keywords' => ($result->meta_keywords == '') ? '' : '<meta name="keywords" content="'.$result->meta_keywords.'" />' . "\n",
					'meta_description' => ($result->meta_description == '') ? '' : '<meta name="description" content="'.$result->meta_description.'" />' . "\n",
					);
			
			$this->template->load_partial('master', 'pages/'.$template, $data);
		} else {
			$data['page_class'] = 'page-404';
			
			$this->load->view('404', $data);
		}
	}
}
?>