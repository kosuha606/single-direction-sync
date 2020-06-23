<?php

namespace kosuha606\SingleDirectionSync;

use kosuha606\VirtualModel\VirtualModelManager;
use Exception;

class SingleDirectionSinchronizator
{
    /**
     * @var array
     */
    private $existedModels = [];

    /**
     * @var array
     */
    private $importedModels = [];

    /**
     * @var string
     */
    private $importModelUniqIdField;

    /**
     * @var array
     */
    private $checksumFields;
    /**
     * @var string
     */
    private $checksumFieldName;

    /** @var SingleDirectionSinchronizatorProviderInterface */
    private $provider;

    /**
     * @param $existedModels (Array of models what already existed in your system)
     * @param $importedModels (Array of models what come from external data storage)
     * @param string $importModelUniqIdField (Uniq external model id)
     * @param array $checksumFields (Array of model uniq fields, needed to check if data was changed)
     * @throws \Exception
     */
    public function __construct(
        $existedModels,
        $importedModels,
        $importModelUniqIdField = 'import_id',
        $checksumFieldName = 'checksum',
        $checksumFields = ['id']
    ) {
        $this->existedModels = $existedModels;
        $this->importedModels = $importedModels;
        $this->importModelUniqIdField = $importModelUniqIdField;
        $this->checksumFields = $checksumFields;
        $this->checksumFieldName = $checksumFieldName;
        $this->provider = VirtualModelManager::getInstance()->getProvider(SingleDirectionSinchronizatorProviderInterface::class);
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $existedModels = $this->indexByField($this->importModelUniqIdField, $this->existedModels);
        $importedModels = $this->indexByField($this->importModelUniqIdField, $this->importedModels);
        $toCreateModels = $toUpdateModels = [];

        foreach ($importedModels as $importedModel) {
            $importedModel[$this->checksumFieldName] = $this->generateChecksumHash($importedModel);

            if (isset($existedModels[$importedModel[$this->importModelUniqIdField]])) {
                $existedModel = $existedModels[$importedModel[$this->importModelUniqIdField]];

                if (!isset($existedModel[$this->checksumFieldName])) {
                    throw new Exception("There are no checksum field {$this->checksumFieldName} in existed model, impossible to handly synchronization");
                }

                if ($importedModel[$this->checksumFieldName] !== $existedModel[$this->checksumFieldName]) {
                    $toUpdateModels[] = $importedModel;
                } else {
                    // If checksums are equal, then just skip it
                }

                unset($existedModels[$importedModel[$this->importModelUniqIdField]]);
            } else {
                $toCreateModels[] = $importedModel;
            }
        }

        $this->provider->handleUpdate($toUpdateModels);
        $this->provider->handleCreate($toCreateModels);
        // If existed models not empty they should be removed
        $this->provider->handleDelete($existedModels);
    }

    /**
     * @param $model
     * @return string
     * @throws Exception
     */
    private function generateChecksumHash($model)
    {
        $checksumData = [];

        foreach ($this->checksumFields as $checksumField) {
            if (!isset($model[$checksumField])) {
                throw new Exception("There are no field $checksumField in model, impossible to create checksum");
            }

            $checksumData[] = $model[$checksumField];
        }

        return md5(implode('_', $checksumData));
    }

    /**
     * @param $fieldName
     * @param $array
     * @return array
     * @throws Exception
     */
    private function indexByField($fieldName, $array)
    {
        if (isset($array[0][$fieldName])) {
            throw new Exception("Threre are no field with name $fieldName, impossible to index");
        }

        $result = [];

        foreach ($array as $item) {
            $result[$item[$fieldName]] = $item;
        }

        return $result;
    }
}