<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db
 * @property Post_model $Post_model
 * @property SocialPlatform_model $SocialPlatform_model
 * @property Post_platforms_model $Post_platforms_model
 
 */
class dashboardController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('SocialPlatform_model');
        $this->load->model('Post_platforms_model');
    }

    //index dashbord menod
    public function index()
    {
        $data = [];
        $data["menu"] = 'dashboard';
        $data["title"] = 'dashboard';
        $data['all_platforms'] = $this->SocialPlatform_model->get_all();
        $data['total'] =  $this->Post_model->countPosts();
         $data['totalsocial'] =  $this->Post_model->countPostsWithSocial();
        $this->load->view('layout/header', $data);
        $this->load->view('dashboard', $data);
        $this->load->view('layout/footer');
    }

    public function fetchPosts()
    {
        $page  = $this->input->post('page') ?? 1;
          $platform_id  = $this->input->post('platform_id') ?? null;
        $limit = 10; //  FIXED: 10 per page
        $offset = ($page - 1) * $limit;

        $this->load->model('Post_model');

        $posts = $this->Post_model->getPosts($limit, $offset,$platform_id);
        $total = $this->Post_model->countPostsWithSocial($platform_id);
         $total1 = $this->Post_model->countPosts();
        $html = $this->load->view('post_data', [
            'posts' => $posts,
            'all_platforms' => $this->SocialPlatform_model->get_all()
        ], true);
        $platform_name='';
        if($platform_id!=null){
            $platform_data=    $this->SocialPlatform_model->get($platform_id);
              $platform_name=$platform_data->name;
        }

        echo json_encode([
            'status' => true,
            'html'   => $html,
            'total'  => $total,
            'total1'=>$total1,
            'platform_id'=>$platform_name,
            'page'   => $page,
            'limit'  => $limit
        ]);
    }
}
