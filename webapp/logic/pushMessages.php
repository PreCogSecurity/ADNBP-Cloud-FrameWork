<?php
require_once ROOT_CLASS_DIRECTORY . '/notifications/autoload.php';
use CloudFramework\Service\Notifications\Notifier;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $config = null;
        $messageType = strtoupper($data['type']);
        $notifier = Notifier::getInstance('test');
        switch ($messageType) {
            case 'GCM':
            default:
                $config = array(
                    "GCM" => array(
                        'token' => $data['key']
                    )
                );
                break;
            case 'APNS':
                $config = array(
                    "APNS" => array(
                        'certificate' => $data['certPath'],
                        'certificatePassPhrase' => $data['phrase']
                    )
                );
                break;
            case 'MPNS':
                $config = array(
                    'MPNS' => array(
                        'certificate' => $data['certPath'],
                        'certificatePassPhrase' => $data['phrase']
                    )
                );
                break;
        }
        $config[$messageType]['url'] = $data['url'];
        $notifier->setup($config);
        $notifier->addMessage($messageType, $data['payload'], $data['device'], $data['pType'], $data['badge']);
        $sended = $notifier->notify();
        $response = array(
            "success" => $sended,
            "message" => $sended ? 'Messages processed correctly' : $notifier->getLog(),
        );
    } catch (\Exception $e) {
        http_response_code(400);
        $response = array(
            "success" => false,
            "message" => $e->getMessage()
        );
    }
    header("Content-type: application/json");
    echo json_encode($response, JSON_PRETTY_PRINT);
    die;
}