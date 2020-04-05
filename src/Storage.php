<?php

namespace biottico\firebase;

use Google\Cloud\Storage\StorageClient;

/**
 * Description of VinitusST
 *
 * @author biotticos
 */
class Storage extends Firebase {

    private $storage;
    private $bucket;
    private $uniqueId;

    /**
     * 
     * @param type $config
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->uniqueId = uniqid();
        $this->connect();
    }

    /**
     * 
     * @return type
     */
    public function connect() {
        $keyFile = __DIR__ . "/" . $this->getKeyFile();
        $this->debug("VinitusST->connect", "keyFile: $keyFile");
        $this->storage = new StorageClient([
            'keyFile' => json_decode(file_get_contents($keyFile), true)
        ]);
        $this->bucket = $this->storage->bucket($this->getStorageBucketName());
        return;
    }

    /**
     * Crea una clave unica
     * @return type
     */
    public function getUniqueID() {
        $u = uniqid();

        $now = \DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
        $formatedNow = $now->format("YmdHisuv");

        $unique = md5(uniqid($formatedNow . $u, TRUE));
        return $unique;
    }

    /**
     * Envia datos para debug
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
     * File es la ruta completa del archivo en el server local.
     * Name es el nombre que recibira en el store
     * La respuesta es la URL completa del store para guardar en la base de datos
     * 
     * @param type $file
     * @param type $name
     * @return string
     */
    public function upload($file, $name) {
        $url = $this->getStorageUrlImage();
        $token = $this->getUniqueID();

        $options = [
            'name' => $name,
            'predefinedAcl' => 'publicRead',
            'metadata' => [
                'metadata' => [
                    'firebaseStorageDownloadTokens' => $token,
                ]
            ],
        ];
        $this->bucket->upload(fopen($file, 'r'), $options);
        $url = $url . $name . "?alt=media&token=$token";
        $this->debug("VinitusST->upload", "url:$url");
        return $url;
    }

}
