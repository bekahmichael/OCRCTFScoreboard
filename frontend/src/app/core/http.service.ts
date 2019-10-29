import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';
import 'rxjs/add/operator/toPromise';
import { environment } from '../../environments/environment';

/**
 * HTTP defines a set of request methods to indicate the desired action to be performed for a given resource.
 */
type APP_HTTP_METHOD = 'GET' | 'POST' | 'PUT' | 'DELETE' | 'HEAD' | 'OPTIONS';

@Injectable()
export class HttpService {
    constructor (
        private http: HttpClient,
        private router: Router,
        private auth: AuthService,
    ) {}

    /**
     * Execute the given HTTP request.
     *
     * @param method The type of HTTP request method.
     * @param url The url string.
     * @param get_params The object with get params
     * @param post_params The object with POST/PUT params
     * @param isRepeat whether retry do request repeat after fail autentification.
     */
    private exec (method: APP_HTTP_METHOD, url: string, get_params: {} = null, post_params: {} = null, isRepeat = false) {
        const promise = new Promise((resolve, reject) => {
            const query_params = new URLSearchParams();

            if (get_params) {
                for (const key in get_params) {
                    if (get_params.hasOwnProperty(key)) {
                        query_params.set(key, get_params[key]);
                    }
                }

                url = url + '?' + query_params.toString();
            }

            const headers = new HttpHeaders({
                'Content-Type': 'application/json',
                'Authorization' : 'Bearer ' + this.auth.getToken()
            });

            const options = { headers: headers };
            let httpPromise;

            if (method === 'GET') {
                httpPromise = this.http.get(url, options).toPromise();
            } else {
                if (method === 'POST') {
                    httpPromise = this.http.post(url, post_params, options).toPromise();
                } else if (method === 'PUT') {
                    httpPromise = this.http.put(url, post_params, options).toPromise();
                } else if (method === 'OPTIONS') {
                    httpPromise = this.http.options(url, options).toPromise();
                } else if (method === 'DELETE') {
                    httpPromise = this.http.delete(url, options).toPromise();
                } else if (method === 'HEAD') {
                    httpPromise = this.http.head(url, options).toPromise();
                }
            }

            httpPromise.then(res => {
                resolve(res || {});
            }).catch(error => {

                const body = error || '';
                console.log(body);
                if (body.status === 401 && body.statusText === 'Unauthorized') {
                    if (this.auth.getRefreshToken() !== null && isRepeat === false) {
                        this.http
                            .post(environment.API_BASE_URL + '/general/auth/token-refresh', {refresh_token: this.auth.getRefreshToken()})
                            .toPromise()
                            .then(r => {
                                const new_token = r || {};
                                this.auth.setToken(new_token['access_token'], new_token['expires_in']);
                                this.auth.setRefreshToken(new_token['refresh_token']);
                                this.exec(method, url, null, post_params, true)
                                .then(res => {
                                    resolve(res);
                                })
                                .catch(err => {
                                    console.error(error);
                                    reject(error);
                                });
                            })
                            .catch(ErrResponse => {
                                this.auth.logout();
                                this.router.navigate(['/admin/login']);
                                console.error(error);
                                reject(error);
                            });
                    } else {
                        console.error(error);
                        this.auth.logout();
                        this.router.navigate(['/admin/login']);
                        reject(error);
                    }
                } else {
                    console.error(error);
                    reject(error);
                }
            });
        });

        return promise;
    }

    public send (url, get_params = null, post_params = null) {
        if (post_params === null) {
            return this.exec('GET', url, get_params, post_params);
        } else {
            return this.exec('POST', url, get_params, post_params);
        }
    }
}
