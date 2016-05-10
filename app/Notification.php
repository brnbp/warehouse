<?php

namespace App;

use App\Http\Controllers\WarehouseController;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * send any notifition you may want
 * @package App
 */
class Notification extends Model
{
    /** @var  SLACK_WEB_HOOK url to use bot on slack */
    const SLACK_WEB_HOOK = '';

    /**
    * Envia Notificação para o Slack ao ocorrer log Critical
    * @param array $data array com as informações do log critical que foi inserido
    */
    public static function Slack($content)
    {
        if ($content['level'] > 1) {
            return;
        }

        $subject = $content['log_name'];
        if (strpos($content['log_name'], '_') == true) {
            $subject = strstr($content['log_name'], '_', true);
        }

        $color = '';
        switch ($content['level']) {
            case 1:
                $color = 'danger';
                break;
            case 2:
                $color = '#F2D600';
                break;
            default:
                $color = '#00C2E0';
                break;
        }

        $message = json_encode(
            [
                'channel' => "#logs",
                'username' => strtoupper($subject),
                'icon_url' => 'cdn_path_here/logs/img/'.$subject.'.png',
                'attachments' => [
                    [
                        'title' => "SITE: ".$content['site']." | LOGNAME: ".$content['log_name'],
                        'text' => 'Identifier: ' . $content['identifier'],
                        'color' => $color,
                        'fields' => [
                            [
                                'value' => substr(stripslashes($content['content']), 0, 700),
                                'short' => true
                            ]
                        ]
                    ]
                ],
            ]
        );

        $opts = [
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => "payload=$message"
            ]
        ];
        $context  = stream_context_create($opts);
        file_get_contents(self::SLACK_WEB_HOOK, false, $context);
    }
}
