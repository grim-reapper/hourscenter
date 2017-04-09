<?php
// require_once APPPATH.'/third_party/yelp/OAuth.php';
/**
 * Yelp api to fetch data
 */
class Yelp_api
{

 private $_CLIENT_ID = NULL;
 private $_CLIENT_SECRET = NULL;
// Complain if credentials haven't been filled out.
// API constants, you shouldn't have to change these.
private $_API_HOST = "https://api.yelp.com";
private $_SEARCH_PATH = "/v3/businesses/search";
private $_BUSINESS_PATH = "/v3/businesses/";  // Business ID will come after slash.
private $_TOKEN_PATH = "/oauth2/token";
private $_GRANT_TYPE = "client_credentials";
// Defaults for our simple example.
private $_DEFAULT_TERM = "dinner";
private $_DEFAULT_LOCATION = "San Francisco, CA";
private $_SEARCH_LIMIT = 3;
/**
 * Given a bearer token, send a GET request to the API.
 *
 * @return   OAuth bearer token, obtained using client_id and client_secret.
 */
function obtain_bearer_token() {
    try {
        # Using the built-in cURL library for easiest installation.
        # Extension library HttpRequest would also work here.
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $postfields = "client_id=" . $this->_CLIENT_ID .
            "&client_secret=" . $this->_CLIENT_SECRET .
            "&grant_type=" . $this->_GRANT_TYPE;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->_API_HOST . $this->_TOKEN_PATH,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded",
            ),
        ));
        $response = curl_exec($curl);
        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);
        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
    $body = json_decode($response);
    $bearer_token = $body->access_token;
    return $bearer_token;
}
/**
 * Makes a request to the Yelp API and returns the response
 *
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $host    The domain host of the API
 * @param    $path    The path of the API after the domain.
 * @param    $url_params    Array of query-string parameters.
 * @return   The JSON response from the request
 */
function request($bearer_token, $host, $path, $url_params = array()) {
    // Send Yelp API Call
    try {
        $curl = curl_init();
        if (FALSE === $curl)
            throw new Exception('Failed to initialize');
        $url = $host . $path . "?" . http_build_query($url_params);
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,  // Capture response.
            CURLOPT_ENCODING => "",  // Accept gzip/deflate/whatever.
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "authorization: Bearer " . $bearer_token,
                "cache-control: no-cache",
            ),
        ));
        $response = curl_exec($curl);
        if (FALSE === $response)
            throw new Exception(curl_error($curl), curl_errno($curl));
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        if (200 != $http_status)
            throw new Exception($response, $http_status);
        curl_close($curl);
    } catch(Exception $e) {
        trigger_error(sprintf(
            'Curl failed with error #%d: %s',
            $e->getCode(), $e->getMessage()),
            E_USER_ERROR);
    }
    return $response;
}
/**
 * Query the Search API by a search term and location
 *
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $term        The search term passed to the API
 * @param    $location    The search location passed to the API
 * @return   The JSON response from the request
 */
function search($bearer_token, $term, $location) {
    $url_params = array();

    $url_params['term'] = $term;
    $url_params['location'] = $location;
    $url_params['limit'] = $GLOBALS['SEARCH_LIMIT'];

    return $this->request($bearer_token, $this->_API_HOST, $this->_SEARCH_PATH, $url_params);
}
/**
 * Query the Business API by business_id
 *
 * @param    $bearer_token   API bearer token from obtain_bearer_token
 * @param    $business_id    The ID of the business to query
 * @return   The JSON response from the request
 */
function get_business($bearer_token, $business_id) {
    $business_path = $this->_BUSINESS_PATH . urlencode($business_id);

    return $this->request($bearer_token, $this->_API_HOST, $business_path);
}
/**
 * Queries the API by the input values from the user
 *
 * @param    $term        The search term to query
 * @param    $location    The location of the business to query
 */
function query_api($term, $location) {
    $bearer_token = $this->obtain_bearer_token();
    $response = json_decode($this->search($bearer_token, $term, $location));
    $business_id = $response->businesses[0]->id;

    print sprintf(
        "%d businesses found, querying business info for the top result \"%s\"\n\n",
        count($response->businesses),
        $business_id
    );

    $response = $this->get_business($bearer_token, $business_id);

    print sprintf("Result for business \"%s\" found:\n", $business_id);
    $pretty_response = json_encode(json_decode($response), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    print "$pretty_response\n";
}

}


 ?>
