import http from '../../../http-common';

class AuthService {

    login(user) {
        return http
            .post('/login_check', {
                username: user.username,
                password: user.password
            })
            .then(response => {
                if (response.data.token) {
                    localStorage.setItem('user', JSON.stringify(response.data));
                }
                return response.data;
            });
    }

    logout() {
        localStorage.removeItem('user');
    }

    register(user) {
        return http.post('/register', {
            username: user.username,
            email: user.email,
            password: user.password
        });
    }
}
export default new AuthService();