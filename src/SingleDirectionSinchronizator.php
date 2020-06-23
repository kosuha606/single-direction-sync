<?php

namespace kosuha606\SingleDirectionSync;

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

    /**
     * @param $existedModels (Array of models what already existed in your system)
     * @param $importedModels (Array of models what come from external data storage)
     * @param string $importModelUniqIdField (Uniq external model id)
     * @param array $checksumFields (Array of model uniq fields, needed to check if data was changed)
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
    }
}