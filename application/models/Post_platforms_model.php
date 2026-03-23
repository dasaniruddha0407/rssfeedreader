<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Post_platforms_model extends CI_Model {

    protected $table = 'post_platforms';

    // Insert single mapping
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Insert multiple (batch)
    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    // Get platforms by post_id
    public function get_by_post($post_id)
    {
        return $this->db->select('sp.*')
                        ->from($this->table . ' pp')
                        ->join('social_platforms sp', 'sp.id = pp.platform_id')
                        ->where('pp.post_id', $post_id)
                        ->get()
                        ->result();
    }

    // Get posts by platform_id
    public function get_by_platform($platform_id)
    {
        return $this->db->select('p.*')
                        ->from($this->table . ' pp')
                        ->join('posts p', 'p.id = pp.post_id')
                        ->where('pp.platform_id', $platform_id)
                        ->get()
                        ->result();
    }

    // Delete by post_id
    public function delete_by_post($post_id)
    {
        return $this->db->where('post_id', $post_id)
                        ->delete($this->table);
    }

    // Delete by platform_id
    public function delete_by_platform($platform_id)
    {
        return $this->db->where('platform_id', $platform_id)
                        ->delete($this->table);
    }

    // Check mapping exists
    public function exists($post_id, $platform_id)
    {
        return $this->db->where([
                            'post_id' => $post_id,
                            'platform_id' => $platform_id
                        ])
                        ->count_all_results($this->table) > 0;
    }
}