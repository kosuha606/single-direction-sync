<?php

namespace kosuha606\SingleDirectionSync;

use kosuha606\VirtualModel\VirtualModelManager;
use Exception;

class SingleDirectionSynchronizator
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

    /** @var SingleDirectionSynchronizatorProviderInterface */
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
        array $importModelUniqIdField = ['import_id'],
        $checksumFieldName = 'checksum',
        array $checksumFields = ['id']
    ) {
        $this->existedModels = $existedModels;
        $this->importedModels = $importedModels;
        $this->importModelUniqIdField = $importModelUniqIdField;
        $this->checksumFields = $checksumFields;
        $this->checksumFieldName = $checksumFieldName;
        $this->provider = VirtualModelManager::getInstance()->getProvider(SingleDirectionSynchronizatorProviderInterface::class);
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        $existedModels = $this->indexByField($this->importModelUniqIdField, $this->existedModels);
        $importedModels = $this->indexByField($this->importModelUniqIdField, $this->importedModels);
        $toCreateModels = $toUpdateModels = [];

        foreach ($importedModels as $importedModelUniqKey => $importedModel) {
            $importedModel[$this->checksumFieldName] = $this->generateChecksumHash($importedModel);

            if (isset($existedModels[$importedModelUniqKey])) {
                $existedModel = $existedModels[$importedModelUniqKey];

                if (!isset($existedModel[$this->checksumFieldName])) {
                    throw new Exception("There are no checksum field {$this->checksumFieldName} in existed model, impossible to handle synchronization");
                }

                if ($importedModel[$this->checksumFieldName] !== $existedModel[$this->checksumFieldName]) {
                    $toUpdateModels[] = $importedModel;
                } else {
                    // If checksums are equal, then just skip it
                }

                unset($existedModels[$this->modelIndexKey($this->importModelUniqIdField, $importedModel)]);
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
        $result = [];

        foreach ($array as $item) {
            $result[$this->modelIndexKey($fieldName, $item)] = $item;
        }

        return $result;
    }

    /**
     * @param $fieldsData
     * @param $model
     * @return string
     * @throws Exception
     */
    private function modelIndexKey($fieldsData, $model)
    {
        $keyParts = [];

        foreach ($model as $key => $value) {
            if (in_array($key, $fieldsData)) {
                $keyParts[] = $value;
            }
        }

        if (empty($keyParts)) {
            $fieldsName = implode(' ', $fieldsData);
            throw new Exception("Threre are no field with name $fieldsName, impossible to index");
        }

        return implode('_', $keyParts);
    }
}