<?php
namespace Aws\Arn\S3;

use Aws\Arn\Arn;
use Aws\Arn\ArnInterface;
use Aws\Arn\Exception\InvalidArnException;
use Aws\Arn\ResourceTypeAndIdTrait;

/**
 * This class represents an S3 regional bucket ARN, which is in the
 * following format:
 *
 * arn:{partition}:s3:{region}:{accountId}:bucket/{bucketName}
 *
 * ':' and '/' can be used interchangeably as delimiters for components after
 * the account ID.
 *
 * @internal
 */
class RegionalBucketArn extends Arn implements BucketArnInterface
{
    use ResourceTypeAndIdTrait;

    /**
     * Parses a string into an associative array of components that represent
     * a RegionalBucketArn
     *
     * @param $string
     * @return array
     */
    public static function parse($string)
    {
        $data = parent::parse($string);
        $data = self::parseResourceTypeAndId($data);
        $data['bucket_name'] = $data['resource_id'];
        return $data;
    }

    /**
     * RegionalBucketArn constructor
     *
     * @param $data
     */
    public function __construct($data)
    {
        parent::__construct($data);
        static::validate($this->data);
    }

    public function getBucketName()
    {
        return $this->data['bucket_name'];
    }

    /**
     *
     * @param array $data
     */
    protected static function validate(array $data)
    {
        Arn::validate($data);

        if (($data['service'] !== 's3')) {
            throw new InvalidArnException("The 3rd component of an S3"
                . " bucket ARN represents the service and must be 's3'.");
        }

        self::validateRegion($data, 'S3 regional bucket ARN');
        self::validateAccountId($data, 'S3 regional bucket ARN');

        if (($data['resource_type'] !== 'bucket')) {
            throw new InvalidArnException("The 6th component of an S3"
                . " bucket ARN represents the resource type and must be"
                . " 'bucket'.");
        }

        if (empty($data['bucket_name'])) {
            throw new InvalidArnException("The 7th component of an S3"
                . " bucket ARN represents the bucket name and must not be empty.");
        }
    }
}
