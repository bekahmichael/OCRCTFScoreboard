import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { MetaPageService } from '@app/core/meta-page.service';
import { ApiMemberService } from '@app/share/api-member.service';
import { AuthService } from '@app/auth/auth.service';

@Component({
    templateUrl: 'login.component.html',
    styleUrls: ['login.component.css']
})
export class LoginComponent {

    isLoading = false;
    dataForm = {};
    formErrors = {};

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private api: ApiMemberService,
        private auth: AuthService,
    ) {
        this.metaPage.setTitle('Log In');
    }

    doSubmit() {
        if (this.isLoading === true) {
            return false;
        }

        this.isLoading = true;
        this.formErrors = {};

        this.api.send('auth/token', {}, this.dataForm).then(res => {
            this.isLoading = false;
            if (!res['errors']) {
                this.auth.setToken(res['access_token'], res['expires_in']);
                this.auth.setRefreshToken(res['refresh_token']);
                this.auth.setUser(res['user']);
                this.router.navigate(['/']);
            } else {
                setTimeout(() => {
                    this.formErrors = res['errors'];
                }, 200);
            }
        }).catch(err => {
            console.error(err);
            this.isLoading = false;
        });
    }
}
