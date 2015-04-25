<?php
namespace Balloon\Factory;

use Balloon\Balloon;
use Balloon\Bridge\Factory\FileReaderFactory;
use Balloon\Bridge\Factory\IFileReaderBridgeFactory;
use Balloon\Bridge\IFileReader;
use Balloon\Decorator\Json;
use Balloon\Mapper\DataMapper;
use Balloon\Mapper\DataMapperDecorator;
use Balloon\Proxy\FileReaderCache;
use Balloon\Proxy\FileReaderProxy;

/**
 * Class BalloonFactory
 * @package Balloon\Factory
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class BalloonFactory
{
    /**
     * @var IFileReaderBridgeFactory
     */
    private $fileReaderBridgeFactory;

    /**
     * @param IFileReaderBridgeFactory $fileReaderBridgeFactory
     */
    public function __construct(IFileReaderBridgeFactory $fileReaderBridgeFactory = null)
    {
        $this->fileReaderBridgeFactory = $fileReaderBridgeFactory ? : new FileReaderFactory();
    }

    /**
     * @param string $filePath
     * @param string $className
     * @param string $primaryKey
     * @return Balloon
     */
    public function create($filePath, $className = '', $primaryKey = '')
    {
        $format = pathinfo($filePath, PATHINFO_EXTENSION);
        if(!method_exists($this, $format)){
            throw new \InvalidArgumentException(sprintf('Format %s not supported', $format));
        }

        return $this->$format($filePath, $className, $primaryKey);
    }

    /**
     * @param string $filePath
     * @param string $className
     * @param string $primaryKey
     * @param int $flags
     * @return Balloon
     */
    public function json($filePath, $className = '', $primaryKey = '', $flags = null)
    {
        return $this->instantiate(
            new Json($this->fileReaderBridgeFactory->create($filePath), $flags),
            $className,
            $primaryKey
        );
    }

    /**
     * @param IFileReader $formatDecorator
     * @param string $className
     * @param string $primaryKey
     * @return Balloon
     */
    private function instantiate(IFileReader $formatDecorator, $className, $primaryKey)
    {
        return new Balloon(
            new FileReaderProxy(
                new DataMapperDecorator(
                    $formatDecorator,
                    new DataMapper($className)
                ),
                new FileReaderCache()
            ),
            $primaryKey
        );
    }
}