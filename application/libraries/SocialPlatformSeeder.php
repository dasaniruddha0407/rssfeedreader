<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SocialPlatformSeeder {

    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    public function run()
    {
        // ✅ Run only if empty
        if ($this->CI->db->count_all('social_platforms') > 0) {
            echo "Already seeded. Skipping...<br>";
            return;
        }

        $data = [
            ['name'=>'Facebook','slug'=>'facebook','icon'=>'fab fa-facebook','color'=>'#1877F2','max_chars'=>63206],
            ['name'=>'Twitter (X)','slug'=>'twitter','icon'=>'fab fa-twitter','color'=>'#000000','max_chars'=>280],
            ['name'=>'Instagram','slug'=>'instagram','icon'=>'fab fa-instagram','color'=>'#E1306C','max_chars'=>2200],
            ['name'=>'LinkedIn','slug'=>'linkedin','icon'=>'fab fa-linkedin','color'=>'#0077B5','max_chars'=>3000],
            ['name'=>'YouTube','slug'=>'youtube','icon'=>'fab fa-youtube','color'=>'#FF0000','max_chars'=>5000],
        ];

        $this->CI->db->insert_batch('social_platforms', $data);

        echo "Social platforms seeded successfully!<br>";
    }
}