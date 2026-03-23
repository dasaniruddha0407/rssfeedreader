<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Post_model extends CI_Model
{

    protected $table = 'posts';

    // Insert single post
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    // Insert multiple posts (batch)
    public function insert_batch($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    // Get all posts (with sorting)
    public function get_all($order = 'DESC')
    {
        $this->db->order_by('pub_date', $order);
        return $this->db->get($this->table)->result();
    }

    // Get posts by priority
    public function get_by_priority()
    {
        $this->db->order_by('priority', 'ASC');
        return $this->db->get($this->table)->result();
    }

    // Check duplicate by title
    public function exists($title)
    {
        return $this->db->where('title', $title)
            ->count_all_results($this->table) > 0;
    }

    // Delete all posts
    public function truncate()
    {
        return $this->db->truncate($this->table);
    }

    // Delete single post
    public function delete($id)
    {
        return $this->db->where('id', $id)
            ->delete($this->table);
    }

    // Get single post
    public function get($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table)
            ->row();
    }
    //total records
    public function get_total_count()
    {
        return $this->db->count_all($this->table);
    }

    public function get_paginated($limit, $offset)
    {
        return $this->db
            ->order_by('priority', 'ASC')
            ->limit($limit, $offset)
            ->get($this->table)
            ->result();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }
    //fetch post data those have scoicalicon link
    public function getPosts($limit, $offset, $platform_id = null)
    {
        $this->db->select("
        posts.*,
        GROUP_CONCAT(post_platforms.platform_id) as platform_ids
    ");
        $this->db->from('posts');
        $this->db->join('post_platforms', 'post_platforms.post_id = posts.id');

        $this->db->group_by('posts.id');
        $this->db->order_by('posts.priority', 'ASC');
        if ($platform_id) {
            $this->db->where('post_platforms.platform_id', $platform_id);
        }
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        $result = $query->result();

        // Convert platform_ids string → array
        foreach ($result as $row) {
            $row->linked_platform_ids = $row->platform_ids
                ? explode(',', $row->platform_ids)
                : [];
        }

        return $result;
    }
    //count all post data
    public function countPosts()
    {
        return $this->db->count_all('posts');
    }
    //count all post data those have scoicalicon link
    public function countPostsWithSocial($platform_id = null)
    {

        $this->db->select('COUNT(DISTINCT posts.id) as total');
        $this->db->from('posts');
        $this->db->join('post_platforms', 'post_platforms.post_id = posts.id');

        if ($platform_id) {
            $this->db->where('post_platforms.platform_id', $platform_id);
        }

        $query = $this->db->get()->row();

        return $query->total;
    }
}
