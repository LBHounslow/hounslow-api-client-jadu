## Hounslow API Client

## Changelog

### Release v0.4 `04/06/2024`

Update required to Guzzle version so that it is compatible with Jadu core.

### Release v0.3 `05/11/2021`

Changed namespace from Hounslow to LBHounslow.

### Release v0.2 `15/07/2021`

This change is so that we have more visibility over the request to fetch an accessToken and the Guzzle response body of that request.
- The session storage of a valid accessToken has been removed. We cannot go down the route of caching/refreshing the token continuously, so we now request a fresh token each time we make a request.
- In cases where the call to `/api/accessToken` does not return a valid accessToken response (eg. [here](https://github.com/LBHounslow/hounslow-api-client-jadu/blob/develop/src/Client/Client.php#L320)), the Guzzle response body is stored in [ApiException->getResponseBody()](https://github.com/LBHounslow/hounslow-api-client-jadu/blob/develop/src/Exception/ApiException.php#L49) and is available for logging purposes so we can debug issues.

### Release v0.1 `14/07/2021`

This is the first release. It is based on the [v0.4 release](https://github.com/LBHounslow/hounslow-api-client/releases/tag/v0.4) of the [Hounslow API Client](https://github.com/LBHounslow/hounslow-api-client) which was a specific release for Jadu compatibility. Going forward this will be the Jadu specific version of the Hounslow API Client. 
