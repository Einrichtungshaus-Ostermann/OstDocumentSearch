<?php declare(strict_types=1);

/**
 * Einrichtungshaus Ostermann GmbH & Co. KG - Document Search
 *
 * A Plugin to connect to teedy and search for documents
 *
 * @package   OstDocumentSearch
 *
 * @author    Tim Windelschmidt <tim.windelschmidt@ostermann.de>
 * @copyright 2020 Einrichtungshaus Ostermann GmbH & Co. KG
 * @license   proprietary
 */

use GuzzleHttp\Client;
use Shopware\Components\CSRFWhitelistAware;

class Shopware_Controllers_Frontend_OstDocumentSearch extends Enlight_Controller_Action implements CSRFWhitelistAware
{

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var array
     */
    private $config;

    public function preDispatch()
    {
        $viewDir = $this->container->getParameter('ost_document_search.view_dir');
        $this->get('template')->addTemplateDir($viewDir);

        $this->httpClient = new Client();
        $this->config = $this->container->get('ost_document_search.configuration');

        parent::preDispatch();
    }

    private function getCookies() {
        $response = $this->apiPost('/api/user/login', [
            'body' => [
                'username' => $this->config['username'],
                'password' => $this->config['password'],
                'remember' => 'false',
            ]
        ]);

        if ($response === null) {
            throw new RuntimeException('Invalid Login credentials');
        }

        return $response->getHeader('Set-Cookie');
    }

    private function apiGet(string $path, array $options) {
        return $this->httpClient->get($this->config['baseUrl'] . $path, $options);
    }

    private function apiPost(string $path, array $options) {
        return $this->httpClient->post($this->config['baseUrl'] . $path, $options);
    }

    public function getWhitelistedCSRFActions()
    {
        // return all actions
        return array_values(array_filter(
            array_map(
                function ($method) {
                    return (substr($method, -6) === 'Action') ? substr($method, 0, -6) : null;
                },
                get_class_methods($this)
            ),
            function ($method) {
                return !in_array((string)$method, ['', 'index', 'load', 'extends'], true);
            }
        ));
    }

    public function indexAction()
    {
        $this->forward('search');
    }

    public function searchAction()
    {
        $searchTerm = $this->Request()->get('searchTerm');
        $this->View()->assign('searchTerm', $searchTerm);

        if ($searchTerm === null) {
            $searchTerm = '';
        }

        $response = $this->apiGet('/api/document/list', [
            'headers' => [
                'Cookie' => $this->getCookies()
            ],
            'query' => [
                'search' => $searchTerm
            ]
        ]);

        $searchResponseContent = json_decode($response->getBody()->getContents(), true);

        $this->View()->assign('documents', $searchResponseContent['documents']);
    }

    //
    // After this there are only proxy Actions
    //

    public function thumbAction() {
        $fileID = $this->Request()->get('file_id');

        $response = $this->apiGet('/api/file/' . $fileID . '/data?size=thumb', [
            'headers' => [
                'Cookie' => $this->getCookies()
            ]
        ]);

        $this->Front()->Plugins()->ViewRenderer()->setNoRender(true);
        $this->Response()->setBody($response->getBody());

        foreach ($response->getHeaders() as $k => $v) {
            $this->Response()->setHeader($k, $v[0]);
        }
    }

    public function docAction() {
        $fileID = $this->Request()->get('file_id');

        $response = $this->apiGet('/api/file/' . $fileID . '/data', [
            'headers' => [
                'Cookie' => $this->getCookies()
            ]
        ]);

        $this->Front()->Plugins()->ViewRenderer()->setNoRender(true);
        $this->Response()->setBody($response->getBody());

        foreach ($response->getHeaders() as $k => $v) {
            $this->Response()->setHeader($k, $v[0]);
        }
    }
}
