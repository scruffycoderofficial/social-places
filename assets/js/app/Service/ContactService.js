import http from '../../http-common';
import authHeader from './Auth/AuthHeader';

/**
 * ContactService
 */
class ContactService
{
    /**
     * Gets all registered Contacts
     *
     * @returns {Promise<AxiosResponse<any>>}
     */
    getAll() {
        return http.get('/contacts', { headers: authHeader() });
    }

    /**
     * Creates a new Contact
     *
     * @param data
     * @returns {Promise<AxiosResponse<any>>}
     */
    create(data) {
        return http.post('/contact', data);
    }
}

export default new ContactService;