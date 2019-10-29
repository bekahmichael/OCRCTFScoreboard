import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { MetaPageService } from '../../core/meta-page.service';
import { ApiGeneralService } from '@app/share/api-general.service';
import { WoFlashService } from '@app/wo-module/wo-flash/wo-flash.service';
import { AuthService } from '@app/auth/auth.service';

@Component({
    templateUrl: 'password-reset.component.html',
    styleUrls: ['password-reset.component.css']
})
export class PasswordResetComponent implements OnInit {
    isLoading = true;
    isError = false;
    token = '';
    dataForm = {};
    formErrors = {};

    constructor (
        private metaPage: MetaPageService,
        private router: Router,
        private activeRoute: ActivatedRoute,
        private api: ApiGeneralService,
        private woFlash: WoFlashService,
        private auth: AuthService,
    ) {}

    ngOnInit() {
        this.activeRoute.params.subscribe(res => {
            this.token = res['token'] || '';
            this.api.send('auth/check-password-reset-token', {token: this.token}).then(result => {
                this.isLoading = false;
                if (result['status'] === 200) {

                } else {
                    this.isError = true;
                }
            });
        });
    }

    doSubmit() {
        this.dataForm['token'] = this.token;
        this.api.send('auth/password-reset', {}, this.dataForm).then(res => {
            if (res['errors']) {
                this.formErrors = res['errors'];
            } else {
                this.formErrors = {};
                this.auth.setToken(res['access_token'], res['expires_in']);
                this.auth.setRefreshToken(res['refresh_token']);
                this.auth.setUser(res['user']);
                this.router.navigate(['/']);
                this.woFlash.addMessage('The operation was successfully done!');
            }
        });
    }
}
