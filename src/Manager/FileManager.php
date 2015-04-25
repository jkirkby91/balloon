<?php
namespace Balloon\Manager;

use Balloon\Bridge\IFileReader;

/**
 * Class FileManager
 * @package Balloon\Manager
 * @author Raphaël Lefebvre <raphael@raphaellefebvre.be>
 */
class FileManager implements IFileManager
{
    /**
     * @var IFileReader
     */
    private $fileReader;

    /**
     * @var string
     */
    private $primaryKey;

    /**
     * @param IFileReader $fileReader
     * @param string $primaryKey
     */
    public function __construct(IFileReader $fileReader, $primaryKey = '')
    {
        $this->setFileReader($fileReader);
        $this->setPrimaryKey($primaryKey);
    }

    /**
     * @param mixed $id
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->getAll());
    }

    /**
     * @param mixed $id
     * @return array|null
     */
    public function get($id)
    {
        if($this->has($id)){
            return $this->getAll()[$id];
        }
        return null;
    }

    /**
     * @return array[]
     */
    public function getAll()
    {
        return (array) $this->fileReader->read();
    }

    /**
     * @param array $data
     * @return int
     */
    public function add($data)
    {
        $dataList = $this->getAll();
        if($this->primaryKey && array_key_exists($this->primaryKey, $data)){
            $dataList[$data[$this->primaryKey]] = $data;
        }else{
            $dataList[] = $data;
        }
        return $this->fileReader->write($dataList);
    }

    /**
     * @param mixed $id
     * @param array $data
     * @return int
     */
    public function modify($id, $data)
    {
        $dataList = $this->getAll();
        if(isset($dataList[$id])){
            $dataList[$id] = $data;
            return $this->fileReader->write($dataList);
        }
        return 0;
    }

    /**
     * @param mixed $id
     * @return int
     */
    public function remove($id)
    {
        $dataList = $this->getAll();
        unset($dataList[$id]);
        return $this->fileReader->write($dataList);
    }

    /**
     * @return int
     */
    public function removeAll()
    {
        return $this->fileReader->write(null);
    }

    /**
     * Getter of $fileReader
     *
     * @return IFileReader
     */
    public function getFileReader()
    {
        return $this->fileReader;
    }

    /**
     * Getter of $primaryKey
     *
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * Setter of $primaryKey
     *
     * @param string $primaryKey
     */
    private function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = (string)$primaryKey;
    }

    /**
     * Setter of $fileReader
     *
     * @param IFileReader $fileReader
     */
    private function setFileReader(IFileReader $fileReader)
    {
        $this->fileReader = $fileReader;
    }
}