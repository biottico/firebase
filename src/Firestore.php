<?php

namespace biottico\firebase;

use Google\Cloud\Firestore\FirestoreClient;

/**
 * Description of Firestore
 *
 * @author biotticos
 */
class Firestore extends Firebase {

    private $uniqueId = "";
    public $store;
    public $conn;

    /**
     * 
     * @param type $config
     */
    public function __construct($config = array()) {
        parent::__construct($config);
//        $this->connect();
        $this->uniqueId = uniqid();
    }

    /**
     * 
     * @param type $title
     * @param type $text
     * @return type
     */
    public function debug($title = "", $text = "", $toScreen = FALSE) {
        $toDebug = date('Y-m-d H:m:i')
                . "|" . $this->uniqueId
                . "|$title|$text";
        file_put_contents($this->getLogFile(), "$toDebug\n", FILE_APPEND);
        if ($toScreen) {
            echo "<pre>";
            var_dump($toDebug);
            echo "</pre>";
        }
        return;
    }

    /**
     * 
     * @return type
     */
    public function connect() {
        $keyFile = __DIR__ . "/" . $this->getKeyFile();
        $this->debug("Firestore->connect", "keyFile: $keyFile");

        $this->conn = new FirestoreClient([
            'keyFile' => json_decode(file_get_contents($keyFile), true)
        ]);
        // esto da error de permisos
        $lis = $this->conn->collection('universal_listings');
        $query = $lis->where('longitude', '=', '-58.436551');
        $sn = $query->documents();

        return;
    }

    /**
     * 
     * @param type $params
     * @return type
     */
    public function execQuery($params) {
        $url = $this->getFirestoreApiServer() . "/" . $params;
        $curl = curl_init($url);
        $this->debug("Firestore->execQuery", "url:$url");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: text/html"));
        curl_setopt($curl, CURLOPT_POST, false);
        $json_response = curl_exec($curl);

        curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = json_decode($json_response, true);
        return $response;
    }

    /**
     * 
     * @param type $params
     * @return type
     */
    public function execQuery2($collection, $jsonToPost) {
        $response = null;

        $this->debug("Firestore->execQuery2", "jsonPost:" . $jsonToPost);

        $url = $this->getFirestoreApiServer() . ":runQuery";
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type: application/json',
                'Content-Length: ' . strlen($jsonToPost)),
            CURLOPT_URL => $url . '?key=' . $this->getFirestoreKey(),
            CURLOPT_USERAGENT => 'cURL',
            CURLOPT_POSTFIELDS => $jsonToPost
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * 
     * @param type $params
     * @return type
     */
    public function execInsert($collection, $jsonToPost, $documentID) {
        $response = null;

        $this->debug("Firestore->execInsert", "jsonPost:" . $jsonToPost);

        $url = $this->getFirestoreApiServer() . "/" . $collection . "/" . $documentID;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type: application/json',
                'Content-Length: ' . strlen($jsonToPost),
                'X-HTTP-Method-Override: PATCH'),
            CURLOPT_URL => $url . '?key=' . $this->getFirestoreKey(),
            CURLOPT_USERAGENT => 'cURL',
            CURLOPT_POSTFIELDS => $jsonToPost
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    /**
     * 
     * @param string $documentID
     * @return array
     */
    public function getDocumentByID($collection, $documentID) {
        $url = $this->getFirestoreApiServer() . "/" . $collection . "/" . $documentID;
        $curl = curl_init($url);
        $this->debug("Firestore->getDocumentByID", "url:$url");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: text/html"));
        curl_setopt($curl, CURLOPT_POST, false);
        $json_response = curl_exec($curl);

        curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = json_decode($json_response, true);
        return $response;
    }

    /**
     * 
     * @param type $params
     * @return type
     */
    public function execDelete($collection, $documentID) {
        $response = null;

        $this->debug("Firestore->execDelete", "collection:$collection|documentID:$documentID");

        $url = $this->getFirestoreApiServer() . "/" . $collection . "/" . $documentID;
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array('Content-Type: application/json',
//                'Content-Length: ' . 0,
                'X-HTTP-Method-Override: DELETE'),
            CURLOPT_URL => $url . '?key=' . $this->getFirestoreKey(),
            CURLOPT_USERAGENT => 'cURL',
//            CURLOPT_POSTFIELDS => $jsonToPost
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    /**
     * 
     * @param type $content
     * @param type $documentID
     * @return type
     */
    public function insert($collection, $content = Array(), $documentID = "") {
        $dataToPost = ["fields" => (object) $content];
        $this->debug("Firestore->insert", var_export($content, TRUE));
        $jsonToPost = json_encode($dataToPost);
        $result = $this->execInsert($collection, $jsonToPost, $documentID);
        return $result;
    }

    /**
     * 
     * @param type $collection
     * @param type $documentID
     * @return type
     */
    public function delete($collection, $documentID) {
        $this->debug("Firestore->delete", "collection:$collection|documentID:$documentID");
        $result = $this->execDelete($collection, $documentID);
        return $result;
    }

}
