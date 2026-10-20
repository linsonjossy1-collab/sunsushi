<?php

class ControllerInformationGallery extends Controller {

    private $error = array();

    public function index() {
        $this->language->load('information/gallery');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('catalog/gallery');

        $this->load->model('tool/image');

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('information/gallery'),
            'separator' => $this->language->get('text_separator')
        );

        $albums = $this->model_catalog_gallery->getGalleryalbums();
        $this->data['gallery_albums'] = array();
        foreach ($albums as $album) {
            $this->data['gallery_albums'][] = array(
                'album_id' => $album['album_id'],
                'album_name' => $album['album_name'],
                'thumb_img' => $this->model_tool_image->resize($this->model_catalog_gallery->getAlbumThumbImg($album['album_id']),150,150)
            );
        }
        $this->data['heading_title'] = $this->language->get('heading_title');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/gallery.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/gallery.tpl';
        } else {
            $this->template = 'default/template/information/gallery.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        $this->response->setOutput($this->render());
    }
    
    public function albumimages(){
        $album_id = $cat_id = $this->request->post['album_id'];
        $this->load->model('catalog/gallery');

        $this->load->model('tool/image');
        $images = $this->model_catalog_gallery->getAlbumImages($album_id);
       // print_r($images);
        $this->data['images']=array();
        if(!empty($images)){
            foreach ($images as $k=>$image) {
                list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $image['image']);
                $images[$k]['org_image']=$this->model_tool_image->resize($image['image'],$width_orig,$height_orig);
                $images[$k]['image']= $this->model_tool_image->resize($image['image'],310,207);
            }
        }
        $this->data['images']=$images;
       // print_r($images);
         if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/information/albumimages.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/information/albumimages.tpl';
        } else {
            $this->template = 'default/template/information/albumimages.tpl';
        }

        $this->response->setOutput($this->render());
    }

}

?>
