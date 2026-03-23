<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db

 * @property SocialPlatform_model $SocialPlatform_model
 */
class seederController extends CI_Controller
{

    public function index()
    {
        $this->load->model('SocialPlatform_model');

        if (!$this->db->table_exists('social_platforms')) {
            // if social_platforms does not present
            $data = [
                'status' => 'error',
                'message' => 'Table does not exist!',
                'title' => 'Seeder Error'
            ];
        } elseif (!$this->SocialPlatform_model->is_empty()) {
            // if social_platforms having data 
            //next verion we can check by name/id for seeding
            $data = [
                'status' => 'warning',
                'message' => 'Already seeded. Skipping...',
                'title' => 'Seeder Info'
            ];
        } else {
            //initial hardcoded social platform
            $platforms = [
                ['name' => 'Facebook', 'slug' => 'facebook', 'icon' => 'fab fa-facebook', 'color' => '#1877F2', 'max_chars' => 63206],
                ['name' => 'Twitter (X)', 'slug' => 'twitter', 'icon' => 'fab fa-twitter', 'color' => '#000000', 'max_chars' => 280],
                ['name' => 'Instagram', 'slug' => 'instagram', 'icon' => 'fab fa-instagram', 'color' => '#E1306C', 'max_chars' => 2200],
                ['name' => 'LinkedIn', 'slug' => 'linkedin', 'icon' => 'fab fa-linkedin', 'color' => '#0077B5', 'max_chars' => 3000],
            ];

            $this->SocialPlatform_model->insert_batch($platforms);

            $data = [
                'status' => 'success',
                'message' => 'Social platforms seeded successfully!',
                'title' => 'Seeder Success'
            ];
        }
        $data['menu'] = 'seeder';
        // Load UI
        $this->load->view('layout/header', $data);
        $this->load->view('seeder', $data);
        $this->load->view('layout/footer');
    }
}
