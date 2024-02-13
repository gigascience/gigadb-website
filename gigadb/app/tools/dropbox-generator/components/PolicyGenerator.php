<?php

namespace app\components;

use yii\base\Component;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Component class that uses Twig to generate content for Wasabi policies
 */
class PolicyGenerator extends Component
{
    /**
     * @const string For storing credentials to access Wasabi
     */
    public const TEMPLATE_LOCATION = __DIR__ . '/../templates';

    /**
     * @const string Filename for author policy template
     */
    public const AUTHOR_POLICY_TEMPLATE = 'AllowReadWriteOnBucketByAuthor.html.twig';

    /**
     * Initialize component
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Generates content for a policy that restricts an author to their bucket
     *
     * @param string $username Wasabi username of the author
     * @return string Contains contents of policy
     */
    public function generateAuthorPolicy(string $username): string
    {
        $loader = new FilesystemLoader(self::TEMPLATE_LOCATION);
        $twig   = new Environment($loader);

        // Create bucket name from author username
        $bucketName = str_replace('author-', 'bucket-', $username);
        try {
            $policy = $twig->render(
                self::AUTHOR_POLICY_TEMPLATE,
                ['bucket_name' => $bucketName]
            );
        } catch (LoaderError $e) {
            echo "Problem loading Twig template: " . $e->getMessage() . PHP_EOL;
        } catch (RuntimeError $e) {
            echo "Problem creating policy by Twig: " . $e->getMessage() . PHP_EOL;
        } catch (SyntaxError $e) {
            echo "Syntax problem in Twig template: " . $e->getMessage() . PHP_EOL;
        }
        return $policy;
    }
}
