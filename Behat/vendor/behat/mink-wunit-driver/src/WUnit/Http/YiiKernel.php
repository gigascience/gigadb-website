<?php
/**
 * @author Weavora Team <hello@weavora.com>
 * @link http://weavora.com
 * @copyright Copyright (c) 2011 Weavora LLC
 */

namespace WUnit\Http;

use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class YiiKernel implements HttpKernelInterface
{
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
  {
    $request->overrideGlobals();
    $app = \Yii::app();

    $requestClass = get_class($app->request);
    if (!$requestClass)
      $app->setComponent('request',new YiiRequest());
    else
      $app->setComponent('request',new $requestClass);

    $this->setHeaders();
    $this->setFiles($request->files->all());

    $exception = null;

    ob_start();
    try {
      $app->processRequest();
    } catch (YiiExitException $e) {
      // nothing to do
    } catch (\Exception $e) {
      $exception = $e;
    }

        $content = ob_get_contents();
        ob_end_clean();

        $headers = $this->getHeaders();

        $sessionId = session_id();
        if (empty($sessionId)) {
            session_regenerate_id();
            $app->session->open();
        }

    if ($exception != null) {
      if (get_class($exception) == "CHttpException")
        return new Response($content, $exception->statusCode, $headers);
      echo $exception;
      return new Response($content, 503, $headers);
    }
    return new Response($content, $this->getStatusCode($headers), $headers);
  }

    protected function getHeaders()
    {
        $rawHeaders = xdebug_get_headers();
        $headers = array();
        foreach ($rawHeaders as $rawHeader) {
            list($name, $value) = explode(":", $rawHeader, 2);
            $name = strtolower(trim($name));
            $value = trim($value);
            if (!isset($headers[$name])) {
                $headers[$name] = array();
            }

            $headers[$name][] = $value;
        }
        return $headers;
    }

  protected function getStatusCode($headers)
  {
        if (array_key_exists('location', $headers)) {
            return 302;
        }

        return 200;
    }

  public function setHeaders() {
    if (empty($_SERVER['PHP_SELF']))
      $_SERVER['PHP_SELF'] = '/index.php';
    if (empty($_SERVER['SCRIPT_FILENAME']))
      $_SERVER['SCRIPT_FILENAME'] = \Yii::getPathOfAlias('application') . '/../index.php';
  }

  private function setFiles($files = array()) {
    $filtered = array();
    foreach ($files as $key => $value) {
      if (is_array($value)) {
        $filtered[$key] = $this->filterFiles($value);
      } elseif (is_object($value)) {
        // Yii style :)
        $filtered['tmp_name'][$key] = $value->getPathname();
        $filtered['name'][$key] = $value->getClientOriginalName();
        $filtered['type'][$key] = $value->getClientMimeType();
        $filtered['size'][$key] = $value->getClientSize();
        $filtered['error'][$key] = $value->getError();
//        $filtered[$key] = array(
//          'tmp_name' => $value->getPathname(),
//          'name' => $value->getClientOriginalName(),
//          'type' => $value->getClientMimeType(),
//          'size' => $value->getClientSize(),
//          'error' => $value->getError(),
//        );
      }
    }

    $_FILES = $filtered;
  }
}
