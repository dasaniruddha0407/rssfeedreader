<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_posts extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => FALSE
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => TRUE
            ],
            'char_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'pub_date' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ],
            'priority' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ],
            'feed_url' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
                'null' => TRUE
            ],
            'created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP'
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('posts', TRUE, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci'
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_table('posts');
    }
}
