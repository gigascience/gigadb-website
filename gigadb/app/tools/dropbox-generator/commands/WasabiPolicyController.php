<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use \Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use Aws\Iam\Exception\IamException;
use Aws\Iam\IamClient;

/**
 * Read file in bucket
 *
 */
class WasabiPolicyController extends Controller
{
    /**
     * For storing credentials to access Wasabi
     *
     * @var [] $credentials
     */
    public $credentials = [];

    /**
     * Template file name
     *
     * @var string $template
     */
    public $template = '';

    /**
     * Specify options available to console command provided by this controller.
     *
     * @var    $actionID
     * @return array List of controller class's public properties
     */
    public function options($actionID): array
    {
        return [
            'template',
        ];
    }

    public function init()
    {
        parent::init();
        $this->credentials = array(
            'credentials' => [
                'key' => Yii::$app->params['wasabi']['key'],
                'secret' => Yii::$app->params['wasabi']['secret']
            ],
            'endpoint' => Yii::$app->params['wasabi']['iam_endpoint'],
            'region' => Yii::$app->params['wasabi']['iam_region'],
            'version' => 'latest',
            'use_path_style_endpoint' => true,
            // 'debug'   => true
        );
    }

    /**
     * Create new Wasabi policy
     * @return int Exit code
     */
    public function actionCreate()
    {
        $optTemplate   = $this->template;

        // Return usage unless mandatory options are passed.
        if ($optTemplate === '') {
            $this->stdout(
                "\nUsage:\n\t./yii wasabi-policy/createpolicy --template theTemplateFileName" . PHP_EOL
            );
            return ExitCode::USAGE;
        }

        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader);

        echo $twig->render('first.html.twig', ['name' => 'John Doe',
            'occupation' => 'gardener']);

        return ExitCode::OK;
    }
}
