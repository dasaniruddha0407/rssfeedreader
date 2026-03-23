<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SocialPlatform_model extends CI_Model
{
    protected $table = 'social_platforms';
    //is empty check
    public function is_empty()
    {
        return $this->db->count_all('social_platforms') == 0;
    }
    //insert more than one row
    public function insert_batch($data)
    {
        return $this->db->insert_batch('social_platforms', $data);
    }

    // Get all platforms
    public function get_all($active_only = false)
    {
        if ($active_only) {
            $this->db->where('status', 1);
        }

        return $this->db->order_by('id', 'ASC')
            ->get($this->table)
            ->result();
    }

    // Get single platform
    public function get($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table)
            ->row();
    }

    // Get by slug
    public function get_by_slug($slug)
    {
        return $this->db->where('slug', $slug)
            ->get($this->table)
            ->row();
    }






    // Check exists by slug
    public function exists($slug)
    {
        return $this->db->where('slug', $slug)
            ->count_all_results($this->table) > 0;
    }
}
