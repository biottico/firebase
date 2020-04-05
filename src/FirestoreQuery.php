<?php

namespace biottico\firebase;

/**
 * 
 *
 * @author biotticos
 */
class FirestoreQuery extends Firestore {

    public $runQuery = Array();
    public $select = Array();
    public $from = Array();
    public $where = Array();
    public $orderBy = Array();

    /**
     * 
     * @param type $config
     */
    public function __construct($config = array()) {
        parent::__construct($config);
    }

    /**
     * 
     * @param type $field
     * @return type
     */
    private function parseField($field) {
        foreach ($field as $value) {
            
        }
        return $value;
    }

    /**
     * 
     * @param type $response
     * @return array
     */
    private function parseResponse($response) {
        $result = Array();
        $row = 0;
        foreach ($response['documents'] as $document) {
            $result[$row]['documentName'] = $document['name'];
            $result[$row]['createTime'] = $document['createTime'];
            $result[$row]['updateTime'] = $document['updateTime'];

            foreach ($document['fields'] as $key => $value) {
                $result[$row][$key] = $this->parseField($value);
            }
            $row++;
        }
        return $result;
    }

    /**
     * 
     * @param type $response
     * @return array
     */
    private function parseResponse2($response) {
        $result = Array();
        $row = 0;
        $response = json_decode(json_encode($response), true);
        $this->debug("FirestoreQuery->parseResponse2", var_export($response, true));
        if (isset($response[0]['document'])) {
            foreach ($response as $document) {
                $result[$row]['documentName'] = $document['document']['name'];
                $result[$row]['createTime'] = $document['document']['createTime'];
                $result[$row]['updateTime'] = $document['document']['updateTime'];
                foreach ($document['document']['fields'] as $key => $value) {
                    $result[$row][$key] = $this->parseField($value);
                }
                $row++;
            }
        }
        return $result;
    }

    /**
     * 
     * @param type $tableName
     * @param type $tableParam
     * @return array
     */
    public function query($collection, $tableParam) {
        $result = $this->execQuery($collection);
        $this->debug("FirestoreQuery->query", var_export($result, true));
        $dataResult = $this->parseResponse($result);
        $this->debug("FirestoreQuery->query", var_export($dataResult, TRUE));
        return $dataResult;
    }

    /**
     * 
     * @param type $tableName
     * @param type $tableParam
     * @return array
     */
    public function query2($collection) {

        if ($this->select)
            $this->runQuery['structuredQuery']['select'] = $this->select;
        if ($this->from)
            $this->runQuery['structuredQuery']['from'] = $this->from;
        if ($this->where)
            $this->runQuery['structuredQuery']['where'] = $this->where;
        if ($this->orderBy)
            $this->runQuery['structuredQuery']['orderBy'] = $this->orderBy;

        $result = $this->execQuery2($collection, json_encode($this->runQuery));
        $dataResult = $this->parseResponse2(json_decode($result));
        $this->debug("FirestoreQuery->query2", var_export($dataResult, TRUE));
        return $dataResult;
    }

    /**
     * 
     * @param string $collection
     * @param string $documentID
     * @return Array
     */
    public function getDocument($collection, $documentID) {
        $response = $this->getDocumentByID($collection, $documentID);
//        $this->debug("FirestoreQuery->getDocument", var_export($response, true));
        $dataResult['documentName'] = $response['name'];
        $dataResult['createTime'] = $response['createTime'];
        $dataResult['updateTime'] = $response['updateTime'];

        foreach ($response['fields'] as $key => $value) {
            $dataResult[$key] = $this->parseField($value);
        }

        $this->debug("FirestoreQuery->getDocument", var_export($dataResult, TRUE));
        return $dataResult;
    }

    /**
     * 
     * @param type $collection
     */
    public function addFrom($collection) {
        $result = [
            ["collectionId" => $collection],
        ];
        $this->from = $result;
        return;
    }

    /**
     * 
     * @param Array $fields
     * @return void
     */
    public function addSelect($fields) {
        $result = NULL;
        if ($fields) {
            $row = 0;
            $result = ["fields" => Array()];
            foreach ($fields as $value) {
                $result['fields'][$row++] = ["fieldPath" => $value];
            }
            $this->select = $result;
//            $this->debug('FirestoreQuery->addSelect', var_export($this->select, true));
        }
        return;
    }

    /**
     * 
     * @param Array $param
     */
    public function addWhere($fields, $attributes) {
        $filters = NULL;
        $where = Array();
        if ($fields) {
            $row = 0;
            $where = [
                'compositeFilter' => [
                    'op' => 'AND',
                ]
            ];
            foreach ($fields as $key => $value) {
                if ($value) {
                    $filters[$row++] = [
                        "fieldFilter" => [
                            "field" => [
                                "fieldPath" => $key,
                            ],
                            "op" => "EQUAL",
                            "value" => [$attributes[$key] => $value],
                        ],
                    ];
                }
            }
            $where['compositeFilter']['filters'] = $filters;
            $this->where = $where;
//            $this->debug('FirestoreQuery->addWhere', var_export($this->where, true));
        }
        return;
    }

    /**
     * 
     * @param type $fields
     */
    public function addOrderBy($fields) {
        $result = NULL;
        if ($fields) {
            foreach ($fields as $value) {
                $fieldWhere = [
                    "field" => ["fieldPath" => $value],
                    "direction" => "ASCENDING"
                ];
                $result[] = $fieldWhere;
            }
            $this->orderBy = $result;
//            $this->debug('FirestoreQuery->addOrderBy', var_export($this->orderBy, true));
        }
        return;
    }

}
