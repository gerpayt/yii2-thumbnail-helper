<?php
namespace gerpayt\yii2_thumbnail_helper;

use Yii;
use Imagine\Image\Box;
use Imagine\Image\Color;
use Imagine\Image\ManipulatorInterface;
use Imagine\Image\Point;
use yii\imagine\BaseImage;

class Thumbnail extends BaseImage
{
    const THUMBNAIL_OUTBOUND = ManipulatorInterface::THUMBNAIL_OUTBOUND;
    const THUMBNAIL_INSET = ManipulatorInterface::THUMBNAIL_INSET;

    public static function thumbnail($filename, $width, $height, $mode = ManipulatorInterface::THUMBNAIL_OUTBOUND, $enlarge = false)
    {
        $box = new Box($width, $height);
        $img = static::getImagine()->open(Yii::getAlias($filename));

        if (!$box->getWidth() && !$box->getHeight()) {
            return $img->copy();
        } elseif ($img->getSize()->getWidth() <= $box->getWidth() || $img->getSize()->getHeight() <= $box->getHeight()) {
            if ($enlarge) {
                $ratio = max($box->getWidth() / $img->getSize()->getWidth(), $box->getHeight() / $img->getSize()->getHeight());
                $enlargeBox = new Box($img->getSize()->getWidth() * $ratio, $img->getSize()->getHeight() * $ratio);
                $img->resize($enlargeBox);
            } else {
                return $img->copy();
            }
        }

        $img = $img->thumbnail($box, $mode);

        // create empty image to preserve aspect ratio of thumbnail
        $thumb = static::getImagine()->create($box, new Color('FFF', 100));

        // calculate points
        /** @var $img \Imagine\Image\ImageInterface */
        $size = $img->getSize();

        $startX = 0;
        $startY = 0;
        if ($size->getWidth() < $width) {
            $startX = ceil($width - $size->getWidth()) / 2;
        }
        if ($size->getHeight() < $height) {
            $startY = ceil($height - $size->getHeight()) / 2;
        }

        $thumb->paste($img, new Point($startX, $startY));

        return $thumb;
    }

}
