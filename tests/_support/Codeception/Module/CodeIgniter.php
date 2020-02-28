<?php
namespace Codeception\Module;

class CodeIgniter extends PhpBrowser
{
    protected $ci_config;
    protected $appBaseUrl;
    protected $requiredFields = ['base_url'];

    public function _setConfig($config)
    {
      $config['url'] = $config['base_url'];
      if ($config['url']) {
        $config['url'] = rtrim($config['url'], '/') .'/';
      }
      if ($url = getenv('APP_BASE_URL')) {
        $config['url'] = rtrim($url, '/') .'/';
      }
      if ($config['url'][0] === '/') {
        $config['url'] = 'http://localhost'. $config['url'];
      }
      parent::_setConfig($config);
      $this->loadCodeIgniterConfig();
    }

    protected function loadCodeIgniterConfig()
    {
      include codecept_root_dir('donjo-app/config/config.php');

      if (@$config['index_page']) {
        $this->backupConfig['index_page'] = trim($config['index_page'], '/') .'/';
      }
    }

    protected function clientRequest($method, $uri, array $parameters = [], array $files = [], array $server = [], $content = null, $changeHistory = true)
    {
      if (substr($uri, 0, 7) !== 'http://') {
        $uri = $this->config['url'] . $this->backupConfig['index_page'] . ltrim($uri, '/');
      }
      return parent::clientRequest($method, $uri, $parameters, $files, $server, $content, $changeHistory);
    }


    /** called by seeInCurrentUrl() etc */
    public function _getCurrentUri()
    {
      $uri = parent::_getCurrentUri();
      return $this->normalizeUri($uri);
    }

    private function normalizeUri($uri) {
      $path = parse_url($this->config['url'], PHP_URL_PATH);
      $uri = preg_replace("#^{$path}#", "", $uri);

      $indexPage = @$this->backupConfig['index_page'];
      return preg_replace("#^$indexPage#", "", $uri);
    }

}
