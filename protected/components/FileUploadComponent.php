<?php

class FileUploadComponent extends CApplicationComponent
{
    /**
     * @throws CException
     */
    public function getFileUploadService($webClient, $identifier)
    {
        if (!$webClient instanceof \GuzzleHttp\Client) {
            throw new CException('An error occurred');
        }

        return new  FileUploadService([
            'tokenSrv'       => new TokenService([
                'jwtTTL'     => 3600,
                'jwtBuilder' => Yii::$app->jwt->getBuilder(),
                'jwtSigner'  => new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                'users'      => new UserDAO(),
                'dt'         => new DateTime(),
            ]),
            'webClient'      => $webClient,
            'requesterEmail' => Yii::app()->user->email,
            'identifier'     => $identifier,
            'dataset'        => new DatasetDAO(['identifier' => $identifier]),
            'dryRunMode'     => false,
        ]);
    }
}
