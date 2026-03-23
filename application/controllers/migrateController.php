<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Migration $migration
 */
class MigrateController extends CI_Controller
{

    public function index()
    {
        $this->load->library('migration');

        $data = [];

        if ($this->migration->current() === FALSE) {
            $data['status'] = 'error';
            $data['message'] = $this->migration->error_string();
            $data['title'] = 'Migration Failed';
        } else {
            $data['status'] = 'success';
            $data['message'] = 'Migration completed successfully!';
            $data['title'] = 'Migration Success';
        }
         $data['menu'] = 'migration';
        $this->load->view('layout/header', $data);
        $this->load->view('migration_result', $data);
        $this->load->view('layout/footer');
    }
}
