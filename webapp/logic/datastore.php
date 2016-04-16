<?php
    /**
     * Class Message
     * Example class to explain
     */
    class Message extends \CloudFramework\Service\DataStore\Message\Schema
    {
        public $message_string;
        public $ts_datetime;
        public $idUser_int_index;

        public function getKind()
        {
            return 'test';
        }
    }

    $dst = \CloudFramework\Service\DataStore\DataStore::getInstance(array(
        "service-account-name" => "account-1@api-bloombees-com.iam.gserviceaccount.com",
        "private-key" => file_get_contents(ROOT_CLASS_DIRECTORY . '/datastore/resources/private.p12'),
        "application-id" => "api-bloombees-com",
        "namespace" => "bloombees",
        "dataset" => "api-bloombees-com",
    ));

    try {
        //Save case
        $id = microtime(true) * 10000;
        $message = new Message($id);
        $message->message_string = "Lorem ipsum";
        $message->ts_datetime = new \DateTime('now', new DateTimeZone('UTC'));
        $message->idUser_int_index = 3;//round(rand(1, 10), 0);
        $result = $dst->save($message) ? 'OK' : 'ERROR';
        //Search case
        if ('OK' === $result) {
            $message->idUser_int_index = 3;
            $results = $dst->search($message);
            $messages = array();
            /** @var Message $result */
            foreach($results as $result) {
                $messages[] = $result->export();
                $result->message_string .= ' ' . time();
                $result->ts_datetime = $result->ts_datetime ? \DateTime::createFromFormat(\DateTime::ATOM, $result->ts_datetime) : new \DateTime();
                $dst->save($result);
            }
        }
    } catch(\Exception $e) {
        $result = $e->getMessage();
    }
    $dst->debugText(print_r($messages, true));
    die;