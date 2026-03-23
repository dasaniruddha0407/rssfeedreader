<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_post_platforms extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'auto_increment' => TRUE
            ],
            'post_id' => [
                'type' => 'INT',
                'null' => FALSE
            ],
            'platform_id' => [
                'type' => 'INT',
                'null' => FALSE
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);

        // FIRST create table
        $this->dbforge->create_table('post_platforms', TRUE);

        // THEN add unique constraint
        $this->db->query("
        ALTER TABLE post_platforms 
        ADD UNIQUE KEY unique_post_platform (post_id, platform_id)
    ");
    }

    public function down()
    {
        $this->dbforge->drop_table('post_platforms');
    }
}
