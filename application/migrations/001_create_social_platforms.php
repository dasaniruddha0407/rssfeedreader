<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_social_platforms extends CI_Migration
{

    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'auto_increment' => TRUE
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => TRUE
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => TRUE
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => TRUE
            ],
            'max_chars' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => TRUE
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->create_table('social_platforms', TRUE, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci'
        ]);
    }

    public function down()
    {
        $this->dbforge->drop_table('social_platforms');
    }
}
