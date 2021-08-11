<?php

namespace App\Service;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = "e00219e022bc19b83dc66726a74df40e";
    private $api_key_secret = "3c63d148fb57e3b73f60385ffc81fe41";

    public function send ($to_email, $to_name, $subject, $content_title,  $content_subTitle, $content_category)
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "florent.f21@orange.fr",
                        'Name' => "Le Collectif Taliesin"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 2667848,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        "content_title"=> $content_title,
                        "content_subTitle" => $content_subTitle,
                        "content_category" => $content_category,
                    ]

                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        //$response->success() && dd($response->getData());
        $response->success();
    }
}