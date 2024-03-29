<?php

/*
 * This file is part of the Symfony2 PhoneNumberBundle.
 *
 * (c) University of Cambridge
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Misd\PhoneNumberBundle\Serializer\Normalizer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Phone number serialization for Symfony serializer.
 */
class PhoneNumberNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * Region code.
     *
     * @var string
     */
    private string $region;

    /**
     * Display format.
     *
     * @var int
     */
    private int $format;

    /**
     * Display format.
     *
     * @var PhoneNumberUtil
     */
    private PhoneNumberUtil $phoneNumberUtil;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil Phone number utility.
     * @param string $region Region code.
     * @param int $format Display format.
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil, $region = PhoneNumberUtil::UNKNOWN_REGION, $format = PhoneNumberFormat::E164)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->region = $region;
        $this->format = $format;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = array()): mixed
    {
        return $this->phoneNumberUtil->format($object, $this->format);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null, array $context = []): bool
    {
        return $data instanceof PhoneNumber;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UnexpectedValueException
     */
    public function denormalize($data, $class, $format = null, array $context = array()): mixed
    {
        try {
            return $this->phoneNumberUtil->parse($data, $this->region);
        } catch (NumberParseException $e) {
            throw new UnexpectedValueException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null, array $context = array()): bool
    {
        return $type === 'libphonenumber\PhoneNumber';
    }

    public function getSupportedTypes(?string $format): array
    {
        return [];
    }
}