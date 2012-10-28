<?php
/**
 * Interface for the different web service authenticators.
 * An authenticator is implemented for each service and is used for authenticating 
 * with the service's servers before fetching user data.
 * The authenticator instance should hold Service object's instance reference for easy 
 * authentication state passing.
 * 
 * @see  OAuthAuthenticator and PasswordAuthenticator
 */
interface ServiceAuthenticator {
	
	/**
	 * Returns authenticator's action HTML string.
	 * Can be a form or a popup-windowed link.
	 * Can contain javascript code.
	 * 
	 * @return string Action's URL.
	 */
	public function getAuthAction();
	
	/**
	 * Returns authenticated client's data.
	 * If the client's authentication failed, will return null.
	 * The data will be serialized and stored into the database for further use. 
	 * (example data: form-supplied credentials / authentication tokens / authenticated client ID )
	 * 
	 * @return mixed Authenticated client / failure.
	 */
	public function getClientData();
	
	/**
	 * Authenticates the user with the specified data.
	 * Client data is the data that was returned (and subsequently stored into the db)
	 * by the getClientData() method.
	 * 
	 * @return boolean Whether the authenticated succeeded.
	 */
	public function authenticate($clientData);
	
}
