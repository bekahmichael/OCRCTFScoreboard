import { Component } from '@angular/core';
import { Title } from '@angular/platform-browser';
import { HttpService } from '@app/core/http.service';
import { AuthService } from '@app/auth/auth.service';
import { Router } from '@angular/router';
import { environment } from '../../../environments/environment';

@Component({
    templateUrl: 'login.component.html',
    styleUrls: ['login.component.css']
})

export class LoginComponent {
    username = '';
    password = '';
    formErrors: any;
    isLoading = false;

    constructor (
        private title: Title,
        private http: HttpService,
        private auth: AuthService,
        private router: Router
    ) {
        this.title.setTitle('Login');
        if (this.isLoading && this.auth.can('admin')) {
            this.router.navigate(['/admin/users']);
        }
    }

    doLogin() {
        if (this.isLoading === true) {
            return false;
        }
        this.isLoading = true;
        this.formErrors = {};
        this.http.send(
            environment.API_BASE_URL + '/admin/auth/token',
            null,
            {username: this.username, password: this.password}
        ).then((res) => {
            this.isLoading = false;
            if (!res['errors']) {
                this.auth.setToken(res['access_token'], res['expires_in']);
                this.auth.setRefreshToken(res['refresh_token']);
                this.auth.setUser(res['user']);
                this.router.navigate(['/admin/users']);
            } else {
                setTimeout(() => {
                    this.formErrors = res['errors'];
                }, 200);
            }
        }).catch(res => {
            this.formErrors = {};
            this.isLoading = false;
            alert('Oops! Something went wrong.');
        });
    }
}
