<?php

namespace GitHub;

/*
 * GitHub interaction class.
 */
class GitHub
{

    /**
     * GITHUB_API : Generic API endpoint.
     * TOKEN Generated : https://github.com/settings/tokens
     * REPOSITORY_OWNER : Owner of the repository you are accessing.
     * REPOSITORY : The repository you are creating issues in.
     * DEBUG : Show all debug info or none at all.
     */
    const GITHUB_API = 'https://api.github.com';
    const TOKEN = '';
    const REPOSITORY_OWNER = '';
    const REPOSITORY = '';

    /**
     * Singleton Instance only allow one instance per application.
     * @var
     */
    private static $instance;

    /**
     * Get the GitHub instance.
     * @return mixed
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create an issue in the Repository with a argument array.
     * @param array $issueContents
     * @param $decode
     * @return mixed
     * @throws \Exception
     */
    public function createIssueFromArray($issueContents = [], $decode) {
        if (empty($issueContents['title'])) {
            throw new \Exception('Title parameter is missing.');
        }
        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues';
        return $this->post($url, $issueContents, $decode);
    }

    /**
     * Create an issue in the Repository.
     * @param $title
     * @param $body
     * @param string $assignee
     * @param array $labels
     * @param bool $decode
     * @return mixed
     * @throws \Exception
     */
    public function createIssue($title, $body, $assignee = '', $labels = [], $decode = true)
    {
        if (empty($title)) {
            throw new \Exception('Title parameter is missing.');
        }

        $arguments = [
            'title' => $title,
            'body' => $body,
            'assignee' => $assignee,
            'labels' => $labels
        ];

        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues';
        return $this->post($url, $arguments, $decode);
    }

    /**
     * Updates an array of issues in the Repository.
     * @param $issueIds
     * @param $issueContents
     * @param bool $decode
     * @return mixed
     */
    public function updateIssuesFromArray($issueIds, $issueContents, $decode = true)
    {
        $log = [];
        foreach ($issueIds as $issueId) {
            $log[$issueId] = $this->updateIssueFromArray($issueId, $issueContents, $decode);
        }
        return $log;
    }

    /**
     * Updates an array of issues in the Repository.
     * @param $issueIds
     * @param $title
     * @param $body
     * @param $assignee
     * @param $state
     * @param $labels
     * @param bool $decode
     * @return mixed
     */
    public function updateIssues($issueIds, $title, $body, $assignee, $state, $labels, $decode = true)
    {
        $log = [];
        foreach ($issueIds as $issueId) {
            $log[$issueId] = $this->updateIssue($issueId, $title, $body, $assignee, $state, $labels, $decode);
        }
        return $log;
    }

    /**
     * Updates a single issue in the Repository.
     * @param $issueId
     * @param $issueContents
     * @param bool $decode
     * @return mixed
     * @throws \Exception
     */
    public function updateIssueFromArray($issueId, $issueContents, $decode = true)
    {
        if (empty($issueContents['title'])) {
            throw new \Exception('Title parameter is missing.');
        }

        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues/' . $issueId;
        return $this->patch($url, $issueContents, $decode);
    }

    /**
     * Updates a single issue in the Repository.
     * @param $issueId
     * @param $title
     * @param $body
     * @param $assignee
     * @param string $state
     * @param $labels
     * @param bool $decode
     * @return mixed
     * @throws \Exception
     */
    public function updateIssue($issueId, $title, $body, $assignee, $state = 'open', $labels, $decode = true)
    {
        if (empty($title)) {
            throw new \Exception('Title parameter is missing.');
        }

        $arguments = [
            'title' => $title,
            'body' => $body,
            'assignee' => $assignee,
            'state' => $state,
            'labels' => $labels
        ];

        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues/' . $issueId;
        return $this->patch($url, $arguments, $decode);
    }

    /**
     * Get all the issues in a Repository.
     * @param bool $decode
     * @return mixed
     */
    public function getIssues($decode = true)
    {
        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues';
        return $this->get($url, $decode);
    }

    /**
     * Get an issue in a Repository.
     * @param $issueNumber
     * @param bool $decode
     * @return mixed
     * @throws \Exception
     */
    public function getIssue($issueNumber, $decode = true)
    {
        //Pre-request validation.
        if (empty($issueNumber)) {
            throw new \Exception('No issue number passed to the function.');
        }

        $url = self::GITHUB_API . '/repos/' . self::REPOSITORY_OWNER . '/' . self::REPOSITORY . '/issues/' . $issueNumber;
        return $this->get($url, $decode);
    }

    /**
     * POST to GitHub's API endpoint.
     * @param $url
     * @param $arguments
     * @param bool $decode
     * @return mixed
     */
    private function post($url, $arguments, $decode = true)
    {
        $curl = curl_init($url);
        $curl_post_data = json_encode($arguments);
        $headers = [
            'Accept: */*',
            'Accept-Encoding: gzip, deflate',
            'Content-Type: text/plain',
            'User-Agent: ' . self::REPOSITORY_OWNER,
            'Authorization: token ' . self::TOKEN,
            'Accept-Language: en-gb',
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);

        $curl_response = curl_exec($curl);

        // also get the error and response code
        $errors = curl_error($curl);
        $response = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response !== 201) {
            die(
                var_dump($errors, $response)
            );
        }

        curl_close($curl);

        if ($decode) {
            return json_decode($curl_response);
        }
        return $curl_response;
    }

    /**
     * GET to GitHub's API endpoint.
     * @param $url
     * @param bool $decode
     * @return mixed
     */
    private function get($url, $decode = true)
    {
        $curl = curl_init($url);
        $headers = [
            'Accept: */*',
            'User-Agent: ' . self::REPOSITORY_OWNER,
            'Authorization: token ' . self::TOKEN,
            'Accept-Language: en-gb',
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $curl_response = curl_exec($curl);

        // also get the error and response code
        $errors = curl_error($curl);
        $response = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response !== 200) {
            die(
                var_dump($errors, $response)
            );
        }

        curl_close($curl);

        if ($decode) {
            return json_decode($curl_response);
        }
        return $curl_response;
    }

    /**
     * PATCH to GitHub's API endpoint.
     * @param $url
     * @param $arguments
     * @param $decode
     * @return mixed
     */
    private function patch($url, $arguments, $decode)
    {
        $curl = curl_init($url);
        $curl_post_data = json_encode($arguments);
        $headers = [
            'Accept: */*',
            'Accept-Encoding: gzip, deflate',
            'Content-Type: text/plain',
            'User-Agent: ' . self::REPOSITORY_OWNER,
            'Authorization: token ' . self::TOKEN,
            'Accept-Language: en-gb',
        ];

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);

        $curl_response = curl_exec($curl);

        // also get the error and response code
        $errors = curl_error($curl);
        $response = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response !== 201) {
            die(
                var_dump($errors, $response)
            );
        }

        curl_close($curl);

        if ($decode) {
            return json_decode($curl_response);
        }
        return $curl_response;
    }
}