<?php
/**
 * Orcab Base setup
 *
 * @category  Orcab
 * @package   Orcab\Base
 * @author    Maxime Queneau <maxime.queneau@smile.fr>
 */
namespace Orcab\Base\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

/**
 * Install Data for Orcab Base
 *
 * @package   Orcab\Base\Helper
 */
class Catalog extends AbstractHelper
{
    const IMG_PATH   = 'catalog/fields_masks/img';
    const THUMB_PATH = 'catalog/fields_masks/thumb';
    const ZOOM_PATH  = 'catalog/fields_masks/zoom';

    /**
     * @var string
     */
    protected $imgMask;

    /**
     * @var string
     */
    protected $thumbMask;

    /**
     * @var string
     */
    protected $zoomMask;

    /**
     * Return gallery images Json
     *
     * @param $product
     * @return json
     */
    public function getGalleryImagesJson($product) {

        $imagesItems = [];

        for ($i=1; $i<6; $i++) {
            if ($img = $product->getData('photo_produit_'.$i)) {
                $imagesItems[] = [
                    'thumb'    => $this->getGenerateImg($img, 'thumb'),
                    'img'      => $this->getGenerateImg($img, 'img'),
                    'full'     => $this->getGenerateImg($img, 'zoom'),
                    'caption'  => '',
                    'position' => 1,
                    'isMain'   => true,
                ];
            }
        }

        if (count($imagesItems) == 0) {
            if ($img = $product->getData('photo_vignette')) {
                $imagesItems[] = [
                    'thumb'    => $this->getGenerateImg($img, 'thumb'),
                    'img'      => $this->getGenerateImg($img, 'img'),
                    'full'     => $this->getGenerateImg($img, 'zoom'),
                    'caption'  => '',
                    'position' => 1,
                    'isMain'   => true,
                ];
            }
        }

        return json_encode($imagesItems);
    }

    /**
     * Generate image
     *
     * @param string $img
     * @param string $type
     * @return string
     */
    public function getGenerateImg($img, $type)
    {
        switch ($type) {
            case 'thumb':
                $mask = $this->getThumbMask();
                break;
            case 'img':
                $mask = $this->getImgMask();
                break;
            case 'zoom':
                $mask = $this->getZoomMask();
                break;
            default:
                $mask = $this->getImgMask();
        }

        $nbr    = substr_count($mask, '/');
        $maskEx = explode('|', $mask);
        $imgEx  = explode('/', $img);
        array_splice($imgEx, $nbr+1, 0, $maskEx[1]);

        return implode('/', $imgEx);
    }

    /**
     * Get img mask
     *
     * @return string
     */
    public function getImgMask()
    {
        if (!$this->imgMask) {
            $this->imgMask = $this->getConfig(self::IMG_PATH);
        }
        return $this->imgMask;
    }

    /**
     * Get thumb mask
     *
     * @return string
     */
    public function getThumbMask()
    {
        if (!$this->thumbMask) {
            $this->thumbMask = $this->getConfig(self::THUMB_PATH);
        }
        return $this->thumbMask;
    }

    /**
     * Get zoom mask
     *
     * @return string
     */
    public function getZoomMask()
    {
        if (!$this->zoomMask) {
            $this->zoomMask = $this->getConfig(self::ZOOM_PATH);
        }
        return $this->zoomMask;
    }

    /**
     * get config
     *
     * @param string $config_path
     * @return mixed
     */
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}