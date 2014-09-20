<?php

/**
 * multiupload.tr.php
 *
 * This file registers the widget's core messages in Turkish.
 *
 * @vendor      BiberLtd
 * @package		Core\Bundles\CoreBundle
 * @subpackage	Resources
 * @name	    multiupload.tr.php
 *
 * @author		Said İmamoğlu
 *
 * @copyright   Biber Ltd. (www.biberltd.com)
 *
 * @version     1.0.0
 * @date        05.02.2014
 *
 * =============================================================================================================
 * !!! IMPORTANT !!!
 *
 * Depending your environment run the following code after you have modified this file to clear Symfony Cache.
 * Otherwise your changes will NOT take affect!
 *
 * $ sudo -u apache php app/console cache:clear
 * OR
 * $ php app/console cache:clear
 * =============================================================================================================
 * TODOs:
 * None
 */
/** Nested keys are accepted */
return array(
    'lbl' => array(
        'error' => 'Hata',
        'empty' => 'Boş',
        'file_name' => 'Dosya Adı',
        'file_size' => 'Boyut',
        'crop' => 'Crop',
        'dragdrop_info' => 'Sürkle Bırak',
        'preview' => 'Önizleme',
        'sort_order' => 'Sıralama',
        'options' => 'Seçenekler',
    ),
    'btn' => array(
        'add_files' => 'Dosyaları Seç',
        'cancel' => 'İptal Et',
        'delete' => 'Sil',
        'start' => 'Yükle',
    ),
    'crop' => array(
        'btn' => array(
            'add_crop'  => 'Crop Ekle',
            'close'  => 'Kapat',
            'save'  => 'Kaydet',
        ),
        'lbl' => array(
            'title' => 'Crop',
            'info' => 'Bilgi',
            'select_crop' => 'Crop\'u Seç',
        ),
    ),
    'widget' => array(
        'title' => 'Çoklu Dosya Yükleme',
    )
);
/**
 * Change Log
 * **************************************
 * v1.0.0                      Said İmamoğlu
 * 05.02.2014
 * **************************************
 * A lbl
 * A lbl.error
 * A lbl.file_name
 * A lbl.crop
 * A lbl.dragdrop_ingo
 * A lbl.sort_order
 * A lbl.options
 * A btn
 * A btn.cancel
 * A btn.delete
 * A btn.start
 * A crop
 * A crop.btn
 * A crop.btn.add_crop
 * A crop.btn.close
 * A crop.btn.save
 * A crop.lbl.title
 * A crop.lbl.info
 * A crop.lbl.select_crop
 * A widget.title
 * 
 */