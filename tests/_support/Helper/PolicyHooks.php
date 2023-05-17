<?php

namespace Helper;

class PolicyHooks extends \Codeception\Module
{
    /**
     * HOOK: after each test scenario, all test buckets, policy and account will be deleted
     * @param \Codeception\TestInterface $test
     * @return void
     */
    public function _after(\Codeception\TestInterface $test)
    {
        $secretFile = __DIR__ . '/../../../.secrets';
        $lines = file($secretFile);
        $accessKeyId = null;
        $secretKey = null;
        foreach ($lines as $line) {
            if (str_contains($line, 'DROPBOX_WASABI_ACCESS_KEY_ID')) {
                $accessKeyId = trim(str_replace('DROPBOX_WASABI_ACCESS_KEY_ID=', '', $line));
            } elseif (str_contains($line, 'DROPBOX_WASABI_SECRET_ACCESS_KEY')) {
                $secretKey = trim(str_replace('DROPBOX_WASABI_SECRET_ACCESS_KEY=', '', $line));
            }
        }

        $loader = new \Twig\Loader\FilesystemLoader('/project/tests/_data/RcloneConfigs');
        $twig = new \Twig\Environment($loader);
        try {
            file_put_contents(
                "/project/tests/_output/test_admin.conf",
                $twig->render('developer.conf.twig', [
                    'wasabi_group_developer_test_access_key_id' => $accessKeyId,
                    'wasabi_group_developer_test_secret_access_key' => $secretKey,
                ]),
            );
        } catch (\Twig\Error\LoaderError | \Twig\Error\RuntimeError | \Twig\Error\SyntaxError $e) {
            codecept_debug($e->getMessage());
        }

        $this->assertFileExists("/project/tests/_output/test_admin.conf");

        system("rclone --config=/project/tests/_output/test_admin.conf rmdir wasabiTest:bucket-giga-d-23-12345", $status);
        $this->assertEquals(0, $status);
    }
}
