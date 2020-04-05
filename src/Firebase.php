<?php

namespace biottico\firebase;

/**
 * Description of Firebase
 *
 * @author biotticos
 */
class Firebase {

    private $logFile;
    private $keyFile;
    private $storageBucketName;
    private $storageUrlImage;
    private $firestoreApiServer;
    private $firestoreKey;
    private $firestoreProjectId;

    /**
     * 
     * @param type $config
     */
    public function __construct($config = []) {
        if (isset($config['biottico-firebase']['logFile']))
            $this->setLogFile($config['biottico-firebase']['logFile']);
        if (isset($config['biottico-firebase']['keyFile']))
            $this->setKeyFile($config['biottico-firebase']['keyFile']);
        if (isset($config['biottico-firebase']['storageBuketName']))
            $this->setStorageBucketName($config['biottico-firebase']['storageBuketName']);
        if (isset($config['biottico-firebase']['storageUrlImage']))
            $this->setStorageUrlImage($config['biottico-firebase']['storageUrlImage']);
        if (isset($config['biottico-firebase']['firestoreApiServer']))
            $this->setFirestoreApiServer($config['biottico-firebase']['firestoreApiServer']);
        if (isset($config['biottico-firebase']['firestoreKey']))
            $this->setFirestoreKey($config['biottico-firebase']['firestoreKey']);
        if (isset($config['biottico-firebase']['firestoreProjectId']))
            $this->setFirestoreProjectId($config['biottico-firebase']['firestoreProjectId']);
    }

    function getLogFile() {
        return $this->logFile;
    }

    function getKeyFile() {
        return $this->keyFile;
    }

    function getStorageBucketName() {
        return $this->storageBucketName;
    }

    function getStorageUrlImage() {
        return $this->storageUrlImage;
    }

    function getFirestoreApiServer() {
        return $this->firestoreApiServer;
    }

    function getFirestoreKey() {
        return $this->firestoreKey;
    }

    function getFirestoreProjectId() {
        return $this->firestoreProjectId;
    }

    private function setLogFile($logFile): void {
        $this->logFile = $logFile;
    }

    private function setKeyFile($keyFile): void {
        $this->keyFile = $keyFile;
    }

    private function setStorageBucketName($storageBucketName): void {
        $this->storageBucketName = $storageBucketName;
    }

    private function setStorageUrlImage($storageUrlImage): void {
        $this->storageUrlImage = $storageUrlImage;
    }

    private function setFirestoreApiServer($firestoreApiServer): void {
        $this->firestoreApiServer = $firestoreApiServer;
    }

    private function setFirestoreKey($firestoreKey): void {
        $this->firestoreKey = $firestoreKey;
    }

    private function setFirestoreProjectId($firestoreProjectId): void {
        $this->firestoreProjectId = $firestoreProjectId;
    }

}
