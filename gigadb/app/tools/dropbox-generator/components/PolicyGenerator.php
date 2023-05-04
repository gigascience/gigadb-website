<?php

namespace app\components;

use Exception;
use GigaDB\models\Dataset;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Component;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Component service to output Wasabi policies
 */
class PolicyGenerator extends Component
{
    // Character width of text in readme file.
    public const STRING_WIDTH = 80;


    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Create a policy for an author to access their bucket in Wasabi
     *
     * @param string $username Wasabi username of the author.
     *
     * @return string Contents of policy
     */
    public function createAuthorPolicy(string $username): string
    {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        $twig = new Environment($loader);

        // Create bucket name from author username
        $bucketName = str_replace('author-', 'bucket-', $username);
        try {
            $policy = $twig->render(
                "AllowReadWriteOnBucketByAuthor.html.twig",
                ['bucket_name' => "$bucketName"]
            );
        } catch (LoaderError $e) {
            echo "Problem with loading Twig template - " . $e->getMessage() . PHP_EOL;
        } catch (RuntimeError $e) {
            echo "Problem with creating policy by Twig - " . $e->getMessage() . PHP_EOL;
        } catch (SyntaxError $e) {
            echo "Syntax problem in Twig template - " . $e->getMessage() . PHP_EOL;
        }
        return $policy;
    }
}
