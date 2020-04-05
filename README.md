# Firebase

# Ejemplo:


require_once '../vendor/autoload.php';


$config = [
    'biottico-firebase' => [
        'logFile' => "/var/log/xxxx.log",
        'keyFile' => 'KEY_FILE.json',
        'storageBuketName' => 'XXXXXXXXXX.appspot.com',
        'storageUrlImage' => 'https://firebasestorage.googleapis.com/v0/b/XXXXXXXXXX.appspot.com/o/',
        'firestoreApiServer' => "https://firestore.googleapis.com/v1beta1/projects/XXXXXXXXXX/databases/(default)/documents",
        'firestoreKey' => "XXXXXXXXXXXXXXXXXXXXXXXXXXX",
        'firestoreProjectId' => "XXXXXXXXXX",
    ],
];

use biottico\firebase\FirestoreQuery;

$query = new FirestoreQuery($config);
$data = $query->getDocument("COLLECTION", "DOCUMENT_ID");

var_dump($data);
