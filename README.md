# GitHub Wrapper
A simple PHP class that allows creation and editing of GitHub's issues.

# Requirements
A GitHub account is required to use this wrapper as GitHub needs authentication from a user.

A Token is also needed to be generated for the user in question. To generate a Token for the user simply goto : https://github.com/settings/tokens

# Options
These options are found in the GitHub.php class as constants.

    (string) GITHUB_API - Generic API endpoint.
    (string) TOKEN - Generate this for your user. 
    (string) REPOSITORY_OWNER - Owner of the repository create / update issues on.
    (string) REPOSITORY - Repository to create / update issues on.

# Usage
    $github = GitHub::getInstance();
    
    createIssueFromArray(issueContents, decode);
    
        issueContents : array
        decode: boolean(*)
        
        issueContents - Structure
        [
            'title' => '',
            'body' => '',
            'assignee' => '',
            'labels' => [
                ''
            ]
        ]
    
    createIssue(title, body, assignee, labels, decode)
        
        title : string
        body : string
        assignee : string
        labels : array
        decode : boolean(*)
        
    updateIssueFromArray(issueId, issueContents, decode)
    
        issueId : int
        issueContents : array
        decode : boolean(*)
        
        issueContents - Structure
        [
            'title' => '',
            'body' => '',
            'assignee' => '',
            'state' => '',
            'labels' => [
                ''
            ]
        ]
        
    updateIssue(issueId, title, body, assignee, state, labels, decode)
    
        issueId : int
        title : string
        body : string
        assignee : string
        state: string
        labels : array
        decode : boolean(*)
        
    updateIssuesFromArray(issueId, issueContents, decode)
        
        issueId : array
        issueContents : array
        decode : boolean(*)
        
        issueContents - Structure
        [
            'title' => '',
            'body' => '',
            'assignee' => '',
            'state' => '',
            'labels' => [
                ''
            ]
        ]
        
    updateIssues(issueIds, title, body, assignee, state, labels, decode)
    
        issueIds : array
        title : string
        body : string
        assignee : string
        state: string
        labels : array
        decode : boolean(*)
        
    getIssues(decode)
    
        decode : boolean(*)
        
    
    (*) Decoding the response from the server from JSON to a PHP array.
