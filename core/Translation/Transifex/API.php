<?php
/**
 * Piwik - free/libre analytics platform
 *
 * @link http://piwik.org
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
namespace Piwik\Translation\Transifex;

use Exception;
use Piwik\Exception\AuthenticationFailedException;

class API
{
    protected $apiUrl = 'https://www.transifex.com/api/2/';
    protected $username = '';
    protected $password = '';
    protected $projectSlug = '';

    public function __construct($username, $password, $project='piwik')
    {
        $this->username = $username;
        $this->password = $password;
        $this->projectSlug = $project;
    }

    /**
     * Returns all resources available on Transifex project
     *
     * @return array
     */
    public function getAvailableResources()
    {
        static $resources;

        if (empty($resources)) {
            $apiPath = 'project/' . $this->projectSlug . '/resources';
            $resources = $this->getApiResults($apiPath);
        }

        return $resources;
    }

    /**
     * Checks if the given resource exists in Transifex project
     *
     * @param string $resource
     * @return bool
     */
    public function resourceExists($resource)
    {
        $resources = $this->getAvailableResources();
        foreach ($resources as $res) {
            if ($res->slug == $resource) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns all language codes the transifex project is available for
     *
     * @return array
     * @throws AuthenticationFailedException
     * @throws Exception
     */
    public function getAvailableLanguageCodes()
    {
        static $languageCodes = array();
        if (empty($languageCodes)) {
            $apiData = $this->getApiResults('project/' . $this->projectSlug . '/languages');
            foreach ($apiData as $languageData) {
                $languageCodes[] = $languageData->language_code;
            }
        }
        return $languageCodes;
    }

    /**
     * Return the translations for the given resource and language
     *
     * @param string $resource e.g. piwik-base, piwik-plugin-api,...
     * @param string $language e.g. de, pt_BR, hy,...
     * @param bool $raw if true plain response wil be returned (unparsed json)
     * @return mixed
     * @throws AuthenticationFailedException
     * @throws Exception
     */
    public function getTranslations($resource, $language, $raw=false)
    {
        if ($this->resourceExists($resource)) {
            $apiPath = 'project/' . $this->projectSlug . '/resource/' . $resource . '/translation/' . $language . '/?mode=onlytranslated&file';
            return $this->getApiResults($apiPath, $raw);
        }
        return null;
    }

    /**
     * Returns response for API request with given path
     *
     * @param $apiPath
     * @param bool $raw
     * @return mixed
     * @throws AuthenticationFailedException
     * @throws Exception
     */
    protected function getApiResults($apiPath, $raw=false)
    {
        $apiUrl = $this->apiUrl . $apiPath;

        $curl = curl_init($apiUrl);
        curl_setopt($curl, CURLOPT_USERPWD, sprintf("%s:%s", $this->username, $this->password));
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        $httpStatus = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpStatus == 401) {
            throw new AuthenticationFailedException();
        } else if ($httpStatus != 200) {
            throw new Exception('Error while getting API results', $httpStatus);
        }

        return $raw ? $response : json_decode($response);
    }
}