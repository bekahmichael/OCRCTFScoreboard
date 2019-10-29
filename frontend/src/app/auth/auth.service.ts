import { Injectable } from '@angular/core';
import { AppStorageService } from '../core/app-storage.service';

@Injectable()
export class AuthService {
    isGuest = true;
    token = null;
    redirectUrl = null;
    user: Object;
    private storeKey = '__auth.service_';

    constructor (
        private storage: AppStorageService,
    ) {
        if (this.getStoreParam('access_token') != null) {
            this.token = this.getStoreParam('access_token');
            this.isGuest = !this.isAuthenticated();
            this.user = this.getStoreParam('user');
        }
    }

    private getStoreParam (key: string) {
        return this.storage.getParam(this.storeKey + key);
    }

    private setStoreParam (key: string, value: any) {
        return this.storage.setParam(this.storeKey + key, value);
    }

    /**
     * Set the access token.
     */
    setToken (token, expires_in) {
        this.token = token;
        this.setStoreParam('access_token', this.token);
        this.setStoreParam('expires_in', expires_in);
        this.touchToken();
        this.isGuest = !this.isAuthenticated();
    }

    /**
     * Set user object.
     * @param userInfo the object.
     */
    setUser (userInfo: Object) {
        this.user = userInfo;
        this.setStoreParam('user', this.user);
    }

    /**
     * Set the refresh access token.
     */
    setRefreshToken(refresh_token) {
        this.setStoreParam('refresh_token', refresh_token);
    }

    /**
     * Return the refresh access token.
     */
    getRefreshToken() {
        return this.getStoreParam('refresh_token');
    }

    /**
     * Return the access token.
     */
    getToken () {
        return this.getStoreParam('access_token');
    }

    /**
     * Whether the current user is a authenticated.
     */
    isAuthenticated (): boolean {
        if (this.getStoreParam('access_token') !== null) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Logs out the current user.
     */
    logout () {
        this.isGuest = true;
        this.setStoreParam('access_token', null);
        this.setStoreParam('refresh_token', null);
    }

    /**
     * Sets modification time of token.
     */
    touchToken () {
        const timeStamp = Math.floor(Date.now() / 1000);
        this.setStoreParam('last_active_time', timeStamp);
    }

    /**
     * Checks if the user can perform the operation as specified by the given permission.
     * @param role string
     */
    can(role) {
        if (this.isGuest === true) {
            return false;
        }
        return this.user && this.user['roles'] && typeof this.user['roles'][role] !== 'undefined';
    }
}
