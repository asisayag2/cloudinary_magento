<?php

use CloudinaryExtension\Cloud;
use CloudinaryExtension\CloudinaryImageProvider;
use CloudinaryExtension\Credentials;
use CloudinaryExtension\ImageManager;
use CloudinaryExtension\Security\Key;
use CloudinaryExtension\Security\Secret;
use CloudinaryExtension\Image;

class Cloudinary_Cloudinary_Model_Image extends Mage_Core_Model_Abstract
{
    use Cloudinary_Cloudinary_Model_PreConditionsValidator;

    public function upload(array $imageDetails)
    {
        $imageProvider = new CloudinaryImageProvider($this->_getCredentials(), $this->_getCloudName());
        $cloudinary = new ImageManager($imageProvider);
        $cloudinary->uploadImage(
            $this->_imageFullPathFromImageDetails($imageDetails)
        );

        Mage::getModel('cloudinary_cloudinary/synchronisation')->tagImageAsBeingInCloudinary($imageDetails);
    }

    private function _imageFullPathFromImageDetails($imageDetails)
    {
        return  $this->_getMediaBasePath() . $this->_getImageDetailFromKey($imageDetails, 'file');
    }

    private function _getCredentials()
    {
        $key = Key::fromString($this->_getConfigHelper()->getApiKey());
        $secret = Secret::fromString($this->_getConfigHelper()->getApiSecret());

        return new Credentials($key, $secret);
    }

    private function _getImageDetailFromKey(array $imageDetails, $key)
    {
        if(!array_key_exists($key, $imageDetails)) {
            throw new Cloudinary_Cloudinary_Model_Exception_BadFilePathException("Invalid image data structure. Missing " . $key);
        }
        return $imageDetails[$key];
    }

    private function _getCloudName()
    {
        return Cloud::fromName($this->_getConfigHelper()->getCloudName());
    }

    private function _getMediaBasePath()
    {
        return Mage::getSingleton('catalog/product_media_config')->getBaseMediaPath();
    }
}