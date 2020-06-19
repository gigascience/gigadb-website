<?php
/**
 * Functional test for captcha image display
 *
 * @author Peter Li <peter+git@gigasciencejournal.com>
 * @license GPL-3.0
 */
class CaptchaImageTest extends FunctionalTesting
{
    use BrowserPageSteps;

    /**
     * The /site/contact page should display a captcha image
     *
     * @uses \BrowserPageSteps::getTagsWithSessionAndUrl($url, $tag)
     */
    public function testItShouldDisplayCaptchaImageOnContactPage() {

        // Go to Contact page
        $url = "http://gigadb.dev/site/contact";
        $img_tags = $this->getTagsWithSessionAndUrl($url, "img");
        $img_path = "";
        foreach($img_tags[0] as $img_tag)
        {
            // Extract src metadata for captcha img tag
            if (strpos($img_tag, 'tempcaptcha') !== false) {
                preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $img_tag, $matches);
                $img_path = array_pop($matches);
            }
        }
        $this->assertFalse($img_path == "", "Could not find a path to a captcha image");

        // Get size info for captcha image
        $img_url = 'http://gigadb.dev'.$img_path;
        $img_size = getimagesize($img_url);
        $img_mime = strtolower(substr($img_size['mime'], 0, 5));

        $this->assertEquals("image", $img_mime, $img_url." is not an image");
        $this->assertEquals("600", $img_size[0], "Captcha image has wrong width");
        $this->assertEquals("100", $img_size[1], "Captcha image has wrong height");
    }
}

?>
