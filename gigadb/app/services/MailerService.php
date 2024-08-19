<?php

declare(strict_types=1);

namespace GigaDB\services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Middleware;
use Yii;
use yii\base\Component;

class MailerService extends Component
{
    private Client       $httpClient;
    private TokenService $tokenSrv;
    private string       $requesterEmail;
    private              $token;

    public string        $template_path;
    public string        $sender;
    public string        $recipient;
    public string        $curators_email;
    public array         $spreadsheet_supported_format;

    public const API_ENDPOINT = 'http://fuw-public-api/notifications/emailSend';

    public function __construct(Client $httpClient, TokenService $tokenSrv, string $requesterEmail, array $config = [])
    {
        $this->httpClient = $httpClient;
        $this->tokenSrv = $tokenSrv;
        $this->requesterEmail = $requesterEmail;

        parent::__construct($config);
    }

    /**
     * method to render the email content to be sent when dataset status change
     *
     * @param string $targetStatus status we are changing dataset upload status to
     *
     * @return string renderered content
     */
    public function renderNotificationEmailBody(string $targetStatus, $identifier): string
    {
        // create a template loader from specific directory in file system
        $loader = new \Twig\Loader\FilesystemLoader(
            $this->template_path
        );

        // instantiate template environment object for rendering to be called upon
        $twig = new \Twig\Environment($loader);

        // render the email instructions from template
        return $twig->render("$targetStatus.twig", ['identifier' => $identifier]);
    }

    /**
     * Make HTTP PUT to File Upload Wizard to update an upload
     *
     * @param string $sender
     * @param string $recipient
     * @param string $subject
     * @param string $content
     *
     * @return bool whether or not the update was succesful
     */
    public function emailSend(string $sender, string $recipient, string $subject, string $content): bool
    {
        // construct the parameters to send to the API in the body of the POST request
        $emailParams = array_combine(['sender', 'recipient', 'subject', 'content'], func_get_args());

        // Grab the client's handler instance.
        $clientHandler = $this->httpClient->getConfig('handler');
        // Create a middleware that echoes parts of the request.
        $tapMiddleware = Middleware::tap(function ($request) {
            // Yii::log( $request->getHeaderLine('Content-Type') , 'info');
            // application/json
            // Yii::log( $request->getBody(), 'info');
            // {"foo":"bar"}
            return $request;
        });

        // reuse token to avoid "You must unsign before making changes" error
        // when multiple API calls in same session
        $this->token = $this->token ?? $this->tokenSrv->generateTokenForUser($this->requesterEmail);

        try {
            $response = $this->httpClient->request('POST', self::API_ENDPOINT, [
                'headers'         => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'form_params'     => $emailParams,
                'connect_timeout' => 5,
                'handler'         => $tapMiddleware($clientHandler),
            ]);
            if (200 === $response->getStatusCode()) {
                // Yii::log($response->getBody(),'info');
                return true;
            }
        } catch (RequestException $e) {
            Yii::log((string) $e->getRequest(), 'error');
            if ($e->hasResponse()) {
                Yii::log((string) $e->getResponse(), 'error');
            }
        }

        return false;
    }

    public function sendEmailForStatusUpdate(string $status, string $identifier, ?string $emailBody = null, ?string $submitterEmail = null): bool
    {
        $content = $emailBody ? $this->processTemplateString($emailBody, ['identifier' => $identifier]) : $this->renderNotificationEmailBody($status, $identifier);

        \Yii::log('Status changed to '. $status, 'info');

        return $this->emailSend(
            $this->sender,
            $submitterEmail ?: $this->curators_email,
            'Dataset has been '. $status,
            $content
        );
    }

    private function processTemplateString(string $inputString, array $vars): string
    {
        foreach ($vars as $key => $value) {
            $pattern = '/{{\s*' . preg_quote($key, '/') . '\s*}}/';
            $inputString = preg_replace($pattern, $value, $inputString);
        }

        return $inputString;
    }
}
