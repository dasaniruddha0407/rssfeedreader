<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db

 * @property Post_model $Post_model
 * @property SocialPlatform_model $SocialPlatform_model
 * @property Post_platforms_model $Post_platforms_model
 */
class postController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
        $this->load->model('SocialPlatform_model');
        $this->load->model('Post_platforms_model');
    }

    public function index()
    {
        $data = [];
        $data["menu"]  = 'posts';
        $data["title"] = 'Posts';

        // Pagination config
        $config['base_url'] = base_url('postController/index');
        $config['total_rows'] = $this->Post_model->get_total_count();
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;

        // Optional styling
        $config['full_tag_open'] = '<div class="pagination">';
        $config['full_tag_close'] = '</div>';

        $this->pagination->initialize($config);

        // Current page
        $page = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;

        // Fetch posts
        $data['posts'] = $this->Post_model->get_paginated($config['per_page'], $page);

        // Pagination links
        $data['pagination'] = $this->pagination->create_links();
        // get all platforms
        $data['all_platforms'] = $this->SocialPlatform_model->get_all();
        // map linked platforms
        foreach ($data['posts'] as &$post) {
            $linked = $this->Post_platforms_model->get_by_post($post->id);

            // convert to array of ids
            $post->linked_platform_ids = array_map(function ($p) {
                return $p->id;
            }, $linked);
        }
        $this->load->view('layout/header', $data);
        $this->load->view('posts', $data);
        $this->load->view('layout/footer');
    }
    public function edit()
    {
        try {

            $id = $this->input->post("id");

            if (empty($id)) {
                throw new Exception("Post ID is required");
            }

            // Get post
            $post = $this->Post_model->get($id);

            if (!$post) {
                throw new Exception("Post not found");
            }

            // Get linked platforms
            $platforms = $this->Post_platforms_model->get_by_post($id);

            $platform_ids = array_map(function ($p) {
                return $p->id;
            }, $platforms);

            // Response
            echo json_encode([
                'status' => true,
                'data' => [
                    'id' => $post->id,
                    'title' => $post->title,
                    'content' => $post->content,
                    'char_count' => strlen($post->content),
                    'platform_ids' => $platform_ids
                ]
            ]);
        } catch (Exception $e) {

            echo json_encode([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }
    public function update()
    {
        try {

            //  START TRANSACTION
            $this->db->trans_begin();

            $id      = $this->input->post('id');
            $title   = trim($this->input->post('title'));
            $content = trim($this->input->post('content'));
            $platforms = $this->input->post('platforms'); // array

            //  VALIDATION
            if (empty($id)) {
                throw new Exception("Invalid post ID");
            }

            if (empty($title)) {
                throw new Exception("Title is required");
            }

            if (empty($content)) {
                throw new Exception("Content is required");
            }

            if (empty($platforms)) {
                throw new Exception("Select at least one platform");
            }

            //  Twitter (X) validation


            // ✅ UPDATE POST
            $postData = [
                'title'      => $title,
                'content'    => $content,
                'char_count' => strlen($content)
            ];

            $this->Post_model->update($id, $postData);

            //  UPDATE PLATFORM MAPPING
            // delete old
            $this->Post_platforms_model->delete_by_post($id);

            // insert new
            $insertData = [];
            foreach ($platforms as $pid) {
                $insertData[] = [
                    'post_id' => $id,
                    'platform_id' => $pid
                ];
            }

            if (!empty($insertData)) {
                $this->Post_platforms_model->insert_batch($insertData);
            }

            //  CHECK TRANSACTION
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Database error occurred");
            } else {
                $this->db->trans_commit();
            }
            // after successful update

            // get updated platforms
            $all_platforms = $this->SocialPlatform_model->get_all();
            $linked = $this->Post_platforms_model->get_by_post($id);

            $linked_ids = array_map(function ($p) {
                return $p->id;
            }, $linked);

            // 🔥 generate HTML
            $html = '';
            foreach ($all_platforms as $sp) {

                $isLinked = in_array($sp->id, $linked_ids);

                $html .= '<span class="icon ' . ($isLinked ? 'active' : 'inactive') . '" 
                style="background:' . $sp->color . '; color:#fff;" 
                title="' . $sp->name . '">
                <i class="' . $sp->icon . '"></i>
              </span>';
            }
            //  SUCCESS
            echo json_encode([
                'status'  => true,
                'html' => $html,
                'message' => "Post updated successfully"
            ]);
        } catch (Exception $e) {

            //  ROLLBACK
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function delete()
    {
        try {

            // START TRANSACTION
            $this->db->trans_begin();

            $id = $this->input->post('id');

            //  VALIDATION
            if (empty($id)) {
                throw new Exception("Invalid post ID");
            }

            // check post exists
            $post = $this->Post_model->get($id);
            if (!$post) {
                throw new Exception("Post not found");
            }

            // DELETE PLATFORM MAPPING FIRST
            $this->Post_platforms_model->delete_by_post($id);

            // DELETE POST
            $this->Post_model->delete($id);
            //  Shift remaining priorities
            $priority = $post->priority;
            $this->db->set('priority', 'priority - 1', FALSE);
            $this->db->where('priority >', $priority);
            $this->db->update('posts');

            //  CHECK TRANSACTION
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Database error occurred");
            } else {
                $this->db->trans_commit();
            }

            // SUCCESS RESPONSE
            echo json_encode([
                'status'  => true,
                'message' => "Post deleted successfully",
                'deleted_id' => $id,
                'deleted_priority' => $priority
            ]);
        } catch (Exception $e) {

            //  ROLLBACK
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updatePriority()
    {
        try {

            // START TRANSACTION
            $this->db->trans_begin();

            $order = json_decode($this->input->post('order'), true);


            if (empty($order)) {
                throw new Exception("Invalid data");
            }
            $id = 0;

            foreach ($order as $row) {
                if ($id == 0) {
                    $id = $row["id"];
                } else if ($id > $row["id"]) {
                    $id = $row["id"];
                }
            }
            $post = $this->Post_model->get($id);
            $priority = $post->priority;
            foreach ($order as $row) {

                if (empty($row['id']) || empty($row['priority'])) {
                    throw new Exception("Invalid row data");
                }
                $this->db->where('id', $row['id']);
                $this->db->update('posts', [
                    'priority' => ($row['priority'] + ($priority - 1))
                ]);
            }

            //  CHECK TRANSACTION
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Database error occurred");
            } else {
                $this->db->trans_commit();
            }

            // SUCCESS RESPONSE
            echo json_encode([
                'status'  => true,
                'message' => "Post deleted successfully",
                'id'      => $id
            ]);
        } catch (Exception $e) {

            //  ROLLBACK
            $this->db->trans_rollback();

            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
