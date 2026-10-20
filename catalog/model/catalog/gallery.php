<?php

class ModelCatalogGallery extends Model {

    public function getGalleryalbums($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "gallery_albums r WHERE status=1";

        $sql .= " ORDER BY r.sort_order ASC ";

        $query = $this->db->query($sql);

        return $query->rows;
    }
    
    public function getAlbumThumbImg($album_id){
        $query = $this->db->query("SELECT  DISTINCT image FROM " . DB_PREFIX . "gallery_album_images r WHERE r.album_id = '" . (int) $album_id . "' AND r.sort_order=1");

        return $query->row['image'];
    }
    
    public function getAlbumImages($album_id){
          $query = $this->db->query("SELECT  *  FROM " . DB_PREFIX . "gallery_album_images r WHERE r.album_id = '" . (int) $album_id . "'");

        return $query->rows;
    }

}

?>