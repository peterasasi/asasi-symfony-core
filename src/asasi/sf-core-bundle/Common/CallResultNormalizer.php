<?php
/**
 * Created by PhpStorm.
 * User: asasi
 * Date: 2018/8/3
 * Time: 14:32
 */

namespace Dbh\SfCoreBundle\Common;


use by\infrastructure\base\BaseCallResult;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * 序列化
 * Class CallResultSerializer
 * @package App\Serializer
 */
class CallResultNormalizer implements NormalizerAwareInterface, NormalizerInterface
{
    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }


    public function normalize($object, $format = null, array $context = array())
    {
        if ($format == 'json' && $object instanceof BaseCallResult) {
            $data = $this->normalizer->normalize($object->getData(), $format);
            return ['data' => $data, 'code' => $object->getCode(), 'msg' => $object->getMsg()];
        }
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof BaseCallResult && $format == 'json';
    }

}
