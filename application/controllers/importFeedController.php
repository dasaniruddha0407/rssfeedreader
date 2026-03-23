<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_DB_query_builder $db

 * @property Post_model $Post_model
 */
class importFeedController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Post_model');
    }


    //index dashbord menod
    public function index()
    {
        $data = [];
        $data["menu"] = 'import_feed';
        $data["title"] = 'Import Feed';

        $this->load->view('layout/header', $data);
        $this->load->view('import_feed', $data);
        $this->load->view('layout/footer');
    }




    public function import()
    {
        try {

            $url = $this->input->post('rss_url');
            $sort = $this->input->post('sort_mode', TRUE);

            //Basic Validation
            if (empty($url)) {
                throw new Exception("RSS URL is required");
            }

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception("Invalid URL format");
            }

            if (!preg_match('/(\.xml|\.rss|feed)/i', $url)) {
                throw new Exception("URL must be RSS feed (xml/rss/feed)");
            }

            // Load RSS
            libxml_use_internal_errors(true);
            $rss = simplexml_load_file($url);

            if (!$rss || !isset($rss->channel->item)) {
                throw new Exception("Invalid or empty RSS feed");
            }

            $items = [];
            $link = current($rss->channel->link);


            $image_first = get_first_image_from_page((string)$link);
            foreach ($rss->channel->item as $item) {

                $image = '';

                // media:content
                if (isset($item->children('media', true)->content)) {
                    $media = $item->children('media', true)->content;
                    $image = (string) $media->attributes()->url;
                }

                // enclosure
                if (!$image && isset($item->enclosure)) {
                    $image = (string) $item->enclosure['url'];
                }

                // description <img>
                if (!$image) {
                    preg_match('/<img.*?src=["\'](.*?)["\']/', $item->description, $matches);
                    if (!empty($matches[1])) {
                        $image = $matches[1];
                    }
                }


                // 🔥 final fallback (helper)
                if (!$image) {

                    $image = $image_first;
                }

                $items[] = [
                    'title'       => (string)$item->title,
                    'description' => (string)$item->description,
                    'link'        => (string)$item->link,
                    'pubDate'     => (string)$item->pubDate,
                    'image'       => $image
                ];
            }

            if (empty($items)) {
                throw new Exception("No items found in RSS");
            }

            //  Sorting
            usort($items, function ($a, $b) use ($sort) {
                return ($sort === 'ASC')
                    ? strtotime($a['pubDate']) - strtotime($b['pubDate'])
                    : strtotime($b['pubDate']) - strtotime($a['pubDate']);
            });

            // Insert with priority

            $total = $this->Post_model->get_total_count(); // existing records
            $priority = $total + 1; // start from next
            $inserted = 0;
            // START TRANSACTION
            $this->db->trans_begin();
            foreach ($items as $item) {

                if (!$this->Post_model->exists($item['title'])) {

                    $data = [
                        'title'       => $item['title'],
                        'content' => $item['description'],
                        'image_url'=> $item['image'],
                        'feed_url'=> $item['link'],
                        'char_count'   => strlen($item['description']),
                        'pub_date'    => date('Y-m-d H:i:s', strtotime($item['pubDate'])),
                        'priority'    => $priority++
                    ];

                    $this->Post_model->insert($data);
                    $inserted++;
                }
            }
            //  CHECK TRANSACTION STATUS
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                throw new Exception("Database error occurred");
            } else {
                $this->db->trans_commit();
            }
            //  Success Response
            echo json_encode([
                'status'  => true,
                'message' => "$inserted posts imported successfully"
            ]);
        } catch (Exception $e) {
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }
            // Error Response
            echo json_encode([
                'status'  => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
