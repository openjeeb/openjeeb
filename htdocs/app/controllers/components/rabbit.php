<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 2/19/19
 * Time: 9:44 AM
 */
require_once APP . '/vendors/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitComponent extends Object
{
    private function getRabbitConnection()
    {
        Configure::load("rabbitmq");
        $connection = new AMQPStreamConnection(Configure::read('Rabbitmq.host'), Configure::read('Rabbitmq.port'), Configure::read('Rabbitmq.user'), Configure::read('Rabbitmq.pass'));
        return $connection;
    }

    public function sendEmailToRabbitMQ($to, $subject, $params = [], $template = "message", $layout = 'default', $options = [])
    {
        $connection = $this->getRabbitConnection();
        $channel = $connection->channel();
        $connection->set_close_on_destruct(false);

        if (empty($to)) {
            return false;
        }

        $params = [
            'to' => $to,
            'subject' => $subject,
            'params' => $params,
            'template' => $template,
            'layout' => $layout,
            'options' => $options
        ];

        $exchange = 'jeeb_email';
        $routingKey = 'jeeb_email';

        try {
            // Declare the exchange
            $channel->exchange_declare($exchange, 'direct', false, true, false);

            $data = json_encode($params);
            $msg = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, $exchange, $routingKey);
            $channel->close();
            $connection->close();
            return true;
        } catch (Exception $exc) {
            $channel->close();
            $connection->close();
            return false;
        }
    }
}