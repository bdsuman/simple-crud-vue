import { defineStore } from 'pinia'
import axios from 'axios'
import { loadLanguageAsync } from 'laravel-vue-i18n'

export const useUserStore = defineStore({
    id: 'UserStoreId',

    state: () => ({
        token: localStorage.getItem('token') || 0,
        user: JSON.parse(localStorage.getItem('user')) || null,
        language: localStorage.getItem('language') || 'en',
    }),

    getters: {
        getToken: state => state.token,
        getUser: state => state.user,
        getLanguage: state => state.language
    },

    actions: {
        async login(email, password) {
            const res = await axios.post('/login', { email, password });
            const tok = res.data.data.access_token;

            this.setToken(tok);

            if (res.data.data.user) {
                await this.setUser(res.data.data.user);
            }

            return res;
        },

        async register(fullName, email, password, passwordConfirmation) {
            const res = await axios.post('/register', {
                full_name: fullName,
                email: email,
                password: password,
                password_confirmation: passwordConfirmation,
            });
            const tok = res.data.data.access_token;

            this.setToken(tok);

            if (res.data.data.user) {
                await this.setUser(res.data.data.user);
            }

            return res;
        },

        setToken(token) {
            this.token = token;
            localStorage.setItem('token', token);
        },

        async setUser(user) {
            this.user = user;
            localStorage.setItem('user', JSON.stringify(user));

            const lang = user.language || 'en';
            this.language = lang;
            localStorage.setItem('language', lang);

            try {
                await loadLanguageAsync(lang);
            } catch (e) {
                console.error("Failed to load language:", lang, e);
            }
        },

        async updateProfile(fullName, language, password = null, passwordConfirmation = null, avatar = null) {
            const formData = new FormData();
            formData.append('full_name', fullName);
            formData.append('language', language);

            if (password) {
                formData.append('password', password);
                formData.append('password_confirmation', passwordConfirmation);
            }

            if (avatar && avatar instanceof File) {
                formData.append('avatar', avatar);
            }

            formData.append('_method', 'PUT');

            const res = await axios.post('/update-profile', formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            if (res.data.data) {
                await this.setUser(res.data.data);
            }

            return res;
        },

        async changePassword(currentPassword, password, passwordConfirmation) {
            const res = await axios.post('/change-password', {
                current_password: currentPassword,
                password: password,
                password_confirmation: passwordConfirmation,
            });

            return res;
        },

        async logout() {
            try {
                await axios.post('/logout');
            } catch (_) {
                // ignore
            } finally {
                this.token = 0;
                this.user = null;
                localStorage.removeItem('token');
                localStorage.removeItem('user');
            }
        },
    }
});
